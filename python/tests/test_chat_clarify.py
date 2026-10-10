# -*- coding: utf-8 -*-
"""Тесты шага «понять вопрос» в чате: уточнения, ключевые слова поиска, метка RAG.

    python tests/test_chat_clarify.py

Модуль app целиком не импортируется (Flask, RAG-индекс, провайдер): нужные
функции достаются из app.py через AST, а обращение к модели подменяется.
Решение модели о переспросе недетерминировано, поэтому здесь проверяется то,
что код обязан гарантировать поверх неё.
"""

from __future__ import annotations

import ast
import difflib
import json
import os
import re
import sys
import traceback
import types

APP_PATH = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "app.py")

NAMES = (
    "_parse_json_object", "_topic_is_generic", "_vague_reason", "_augment_search",
    "_understand_question", "_TG_LETTERS_RE", "_CYRILLIC_RE", "_GENERIC_TOPIC_STEMS",
    "_GENERIC_TOPIC_STOP", "_FIRST_PERSON_RE", "_SITUATION_ASK_RE", "_DEMONSTRATIVE_RE",
    "_TITLE_WORD_HINTS", "_SEARCH_NOISE_RE", "_RAG_LABEL_RE", "_FALLBACK_ASK", "_FALLBACK_OPTIONS",
    "CHAT_QUERIES_MAX",
)


def _load():
    with open(APP_PATH, encoding="utf-8") as handle:
        tree = ast.parse(handle.read())
    translator = types.SimpleNamespace(
        available=lambda: True,
        translate=lambda texts, src, tgt: ["<%s>" % text for text in texts],
    )
    sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    import legal_rag  # noqa: WPS433 — тот же модуль, что использует app.py
    import chat_modes  # noqa: WPS433

    namespace: dict = {
        "re": re, "json": json, "difflib": difflib, "CHAT_KEEP_ALIVE": "1m", "legal_rag": legal_rag,
        "chat_modes": chat_modes,
        "lang_service": types.SimpleNamespace(translator=translator),
        "_tajik_cards": lambda texts: translator.translate(texts, "ru", "tg"),
        "llm_once": None,
    }
    wanted = set(NAMES)
    for node in tree.body:
        picked = (isinstance(node, ast.FunctionDef) and node.name in wanted) or (
            isinstance(node, ast.Assign) and getattr(node.targets[0], "id", "") in wanted
        )
        if picked:
            exec(compile(ast.Module([node], []), APP_PATH, "exec"), namespace)
    missing = [name for name in NAMES if name not in namespace]
    assert not missing, "не найдено в app.py: %s" % ", ".join(missing)
    return namespace


NS = _load()


def understand(reply, original="вопрос", machine="вопрос", lang="ru", prior=(), allow=True):
    """Запускает _understand_question с ответом модели `reply` (dict или строка)."""
    raw = reply if isinstance(reply, str) else json.dumps(reply, ensure_ascii=False)
    NS["llm_once"] = lambda *args, **kwargs: raw
    return NS["_understand_question"]("m", original, machine, lang, list(prior), allow)


def test_bare_words_are_vague_without_history():
    vague = NS["_vague_reason"]
    for text in ("сколько штраф", "какой срок", "Ҷарима чӣ қадар аст?", "Сколько будет штраф?"):
        assert vague(text, "", False) == "no_topic", text


def test_short_tajik_and_russian_questions_without_subject_are_vague():
    vague = NS["_vague_reason"]
    for text in ("Оё мумкин аст?", "Мумкин аст?", "Чӣ кор кунам?", "Можно ли?", "Что делать?"):
        assert vague(text, "", False) == "no_topic", text


def test_yes_no_options_are_not_a_clarification():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "", "search": "",
         "ask": "Уточните вопрос", "choices": ["Да", "Нет", "Подробнее"]},
        original="Оё мумкин аст?", machine="Можно ли?",
    )
    assert result["clarify"]["options"] == list(NS["_FALLBACK_OPTIONS"])  # «Да/Нет» не показаны
    kept = understand(
        {"topic": "", "gap": "no_topic", "ru": "", "search": "",
         "ask": "К какой области права относится вопрос?",
         "choices": ["Да", "Трудовое право", "Семейное право"]},
        original="Оё мумкин аст?", machine="Можно ли?",
    )
    assert kept["clarify"]["options"] == ["Трудовое право", "Семейное право"]


def test_concrete_questions_are_not_vague():
    vague = NS["_vague_reason"]
    for text in (
        "Какой срок исковой давности по гражданским делам?",
        "Сколько дней ежегодного отпуска положено работнику?",
        "С какого возраста можно вступить в брак?",
        "Что грозит за кражу?",
    ):
        assert vague(text, text, False) is None, text


def test_history_makes_short_followup_understandable():
    assert NS["_vague_reason"]("а сколько штраф?", "", True) is None


def test_demonstrative_contract_without_history_is_vague():
    vague = NS["_vague_reason"]
    assert vague("Можно ли расторгнуть этот договор?", "", False) == "no_topic"
    assert vague("Можно ли расторгнуть этот договор?", "", True) is None


def test_personal_situation_without_details_needs_facts():
    vague = NS["_vague_reason"]
    assert vague("Меня уволили, что делать?", "", False) == "no_facts"
    long_text = "Меня уволили вчера без предупреждения и не выплатили расчёт, что делать дальше в такой ситуации?"
    assert vague(long_text, "", False) is None


def test_generic_topic_matches_forms_but_not_specific_terms():
    generic = NS["_topic_is_generic"]
    assert generic("") and generic("штраф") and generic("размер штрафа")
    assert not generic("срок исковой давности") and not generic("кража")


def test_augment_search_adds_title_words_and_drops_code_names():
    augment = NS["_augment_search"]
    result = augment("ежегодный оплачиваемый отпуск Трудовой кодекс Таджикистан",
                     "Сколько дней ежегодного отпуска положено работнику?")
    assert "продолжительность" in result
    assert "кодекс" not in result.lower() and "таджикистан" not in result.lower()
    assert augment("кража", "Что грозит за кражу?") == "кража"


def test_rag_label_is_replaced_even_in_latin():
    label = NS["_RAG_LABEL_RE"]
    assert label.sub("Источник:", "ПРОВЕРЕННАЯ ССЫЛКА RAG: УК РТ") == "Источник: УК РТ"
    assert label.sub("Источник:", "PROVERENNAYA SSILKA RAG: УК") == "Источник: УК"


def test_clarify_card_is_built_from_model_reply():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "Сколько штраф", "search": "штраф",
         "ask": "За какое нарушение?", "choices": ["ПДД", "Налоги", "Труд"]},
        original="сколько штраф", machine="сколько штраф",
    )
    card = result["clarify"]
    assert card and card["options"] == ["ПДД", "Налоги", "Труд"]
    assert card["source"].startswith("За какое нарушение?")


def test_model_saying_clarify_on_concrete_question_is_overridden():
    result = understand(
        {"topic": "срок исковой давности", "gap": "no_topic", "ru": "Какой срок исковой давности?",
         "search": "исковая давность", "ask": "О каком сроке?", "choices": ["Общий", "Особый"]},
        original="Какой срок исковой давности?", machine="Какой срок исковой давности?",
    )
    assert result["clarify"] is None


def test_no_facts_ignored_for_impersonal_question():
    result = understand(
        {"topic": "отпуск", "gap": "no_facts", "ru": "Сколько дней отпуска?", "search": "отпуск",
         "ask": "Какой у вас стаж?", "choices": ["До года", "Больше года"]},
        original="Сколько дней отпуска положено работнику?", machine="Сколько дней отпуска положено работнику?",
    )
    assert result["clarify"] is None


def test_echo_of_user_question_is_not_a_clarification():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "", "search": "",
         "ask": "Сколько штраф?", "choices": ["А", "Б"]},
        original="сколько штраф", machine="сколько штраф",
    )
    # Эхо пользователя не показывается; для пустой формулировки вместо него запасной вопрос.
    assert result["clarify"]["question"] == NS["_FALLBACK_ASK"]


def test_latin_garbage_in_ask_is_dropped():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "", "search": "",
         "ask": "işti", "choices": ["Да", "Нет"]},
        original="сколько штраф", machine="сколько штраф",
    )
    assert result["clarify"]["question"] == NS["_FALLBACK_ASK"]  # мусор не показан


def test_no_second_clarification_after_answer():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "Штраф за ПДД", "search": "штраф",
         "ask": "За что?", "choices": ["А", "Б"]},
        original="сколько штраф", machine="сколько штраф", allow=False,
    )
    assert result["clarify"] is None and result["ru"] == "Штраф за ПДД"


def test_tajik_card_is_translated_from_russian():
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "", "search": "",
         "ask": "За что штраф?", "choices": ["ПДД", "Налоги"]},
        original="Ҷарима чӣ қадар аст?", machine="Сколько штраф?", lang="tg",
    )
    card = result["clarify"]
    assert card["question"] == "<За что штраф?>" and card["options"] == ["<ПДД>", "<Налоги>"]
    assert card["source"].startswith("За что штраф?")  # в историю идёт русская версия


def test_offtopic_needs_no_legal_markers_and_no_history():
    weather = understand({"topic": "", "gap": "offtopic", "ru": "Какая погода завтра", "search": "погода"},
                         original="Какая погода завтра в Душанбе?", machine="Какая погода завтра в Душанбе?")
    assert weather["offtopic"] is True and weather["clarify"] is None
    # Юридический маркер в вопросе («договор») отменяет вывод модели.
    legal = understand({"topic": "", "gap": "offtopic", "ru": "Как расторгнуть договор", "search": "договор"},
                       original="Как расторгнуть договор аренды?", machine="Как расторгнуть договор аренды?")
    assert legal["offtopic"] is False
    # С историей диалога «не по теме» тоже не принимается.
    followup = understand({"topic": "", "gap": "offtopic", "ru": "а завтра", "search": "погода"},
                          original="а завтра?", machine="а завтра?", prior=[("user", "Какая погода?")])
    assert followup["offtopic"] is False


def test_named_legal_area_cancels_model_no_topic_verdict():
    """«Виды административных процедур» — область названа, переспрашивать нечего."""
    result = understand(
        {"topic": "", "gap": "no_topic", "ru": "Виды административных процедур", "search": "административная процедура",
         "ask": "Какую область права уточнить?", "choices": ["Трудовое право", "Семейное право"]},
        original="Какие виды административных процедур существуют?",
        machine="Какие виды административных процедур существуют?",
    )
    assert result["clarify"] is None
    # А совсем пустая формулировка по-прежнему переспрашивается, хотя «штраф» тоже маркер.
    vague = understand(
        {"topic": "", "gap": "none", "ru": "Сколько штраф", "search": "штраф",
         "ask": "За что штраф?", "choices": ["ПДД", "Налоги"]},
        original="сколько штраф", machine="сколько штраф",
    )
    assert vague["clarify"] is not None


def test_vague_question_gets_fallback_card_when_model_reply_is_unusable():
    for reply in ({"topic": "", "gap": "no_topic", "ask": "", "choices": []},
                  {"topic": "", "gap": "none", "ask": "сколько штраф", "choices": ["А", "Б"]},
                  "не json"):
        result = understand(reply, original="сколько штраф", machine="сколько штраф")
        if result is None:  # непарсящийся ответ: шаг не удался, отвечаем как раньше
            continue
        card = result["clarify"]
        assert card and card["question"] == NS["_FALLBACK_ASK"], reply
        assert card["options"] == list(NS["_FALLBACK_OPTIONS"])


def test_english_or_tajik_rewrites_are_discarded():
    result = understand(
        {"topic": "штраф за кражу", "gap": "none", "ru": "What is the fine?", "search": "қарз"},
        original="Ҷарима чӣ қадар аст барои дуздӣ?", machine="Какой штраф за кражу?", lang="tg",
    )
    assert result["ru"] == "" and result["search"] == ""


def test_instruction_leak_in_rewrite_is_cleaned():
    result = understand(
        {"topic": "развод", "gap": "none",
         "ru": "С учетом истории диалога и российского перевода вопроса", "search": "развод"},
        original="Ман мехоҳам ҷудо шавам", machine="Я хочу развестись", lang="tg",
    )
    assert result["ru"] == ""


def test_unparseable_reply_gives_none():
    assert understand("не json вовсе") is None


def test_json_wrapped_in_text_is_parsed():
    parsed = NS["_parse_json_object"]('Вот ответ: ```json\n{"gap": "none"}\n``` спасибо')
    assert parsed == {"gap": "none"}


def main() -> int:
    tests = [value for name, value in sorted(globals().items()) if name.startswith("test_")]
    failures = []
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            failures.append("%s\n%s" % (test.__name__, traceback.format_exc()))
    if failures:
        print("ПРОВАЛЕНО %d из %d тестов:" % (len(failures), len(tests)))
        for failure in failures:
            print("  ✗", failure)
        return 1
    print("OK: %d тестов пройдено" % len(tests))
    return 0


if __name__ == "__main__":
    sys.exit(main())
