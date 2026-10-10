# -*- coding: utf-8 -*-
"""Квалификация по УК РТ: переписывание запроса и уточняющие вопросы.

    python tests/test_crime_flow.py

Модель подменена: проверяется то, что код обязан гарантировать сам — что сбой
модели не ломает поиск, что вопросов не больше трёх и что выдуманные номера
статей из переписанного запроса не попадают в поиск. Качество поиска на
реальных описаниях проверяет tests/eval_crime.py.
"""

from __future__ import annotations

import json
import os
import sys
import traceback

HERE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, HERE)
os.environ.setdefault("OPENAI_API_KEY", "test-key-not-used")

import app  # noqa: E402

FAILS = []
CHECKS = 0
CALLS = []


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


class patched:
    """Временно подменяет атрибут модуля app."""

    def __init__(self, name, value):
        self.name, self.value = name, value

    def __enter__(self):
        self.old = getattr(app, self.name)
        setattr(app, self.name, self.value)

    def __exit__(self, *exc):
        setattr(app, self.name, self.old)


def fake_llm(reply=None, error=None):
    def _once(model, prompt, num_predict, **kwargs):
        CALLS.append({"model": model, "prompt": prompt, "kwargs": kwargs})
        if error:
            raise error
        return reply
    return _once


LONG = "Гражданин тайно проник в квартиру соседа через окно и похитил ноутбук"


def test_rewrite_returns_code_language():
    app._crime_rewrite_cache.clear()
    with patched("llm_once", fake_llm("Кража, тайное хищение чужого имущества")):
        out = app._crime_rewrite_query(LONG)
    check("rewrite text", out == "Кража, тайное хищение чужого имущества", repr(out))
    check("rewrite uses the crime gpt model", CALLS[-1]["kwargs"].get("openai_model") == app.CRIME_GPT_MODEL)


def test_rewrite_is_cached():
    app._crime_rewrite_cache.clear()
    before = len(CALLS)
    with patched("llm_once", fake_llm("Кража")):
        app._crime_rewrite_query(LONG)
        app._crime_rewrite_query(LONG)
    check("one model call for two identical queries", len(CALLS) - before == 1, str(len(CALLS) - before))


def test_rewrite_failure_never_blocks_search():
    app._crime_rewrite_cache.clear()
    with patched("llm_once", fake_llm(error=RuntimeError("down"))):
        out = app._crime_rewrite_query(LONG)
    check("failure gives empty rewrite", out == "", repr(out))
    # поиск при этом работает по исходному тексту
    with patched("llm_once", fake_llm(error=RuntimeError("down"))):
        results, index = app.retrieve_crime_rag("кража")
    check("search still answers", index.get("ready") and len(results) > 0)


def test_rewrite_says_no_crime():
    app._crime_rewrite_cache.clear()
    for reply in ("НЕТ", "Состав преступления отсутствует", "Нет"):
        app._crime_rewrite_cache.clear()
        with patched("llm_once", fake_llm(reply)):
            out = app._crime_rewrite_query("Какая сегодня погода в Душанбе и что приготовить на ужин")
        check("no-crime reply is dropped: " + reply, out == "", repr(out))


def test_rewrite_strips_invented_article_numbers():
    app._crime_rewrite_cache.clear()
    with patched("llm_once", fake_llm("Кража, ст. 244, тайное хищение, статья 999")):
        out = app._crime_rewrite_query(LONG)
    check("no numbers in rewrite", "244" not in out and "999" not in out, repr(out))
    check("words kept", "Кража" in out and "тайное хищение" in out, repr(out))


def test_short_query_skips_model():
    app._crime_rewrite_cache.clear()
    before = len(CALLS)
    with patched("llm_once", fake_llm("Кража")):
        out = app._crime_rewrite_query("кража")
    check("short query not rewritten", out == "" and len(CALLS) == before)


def test_rewrite_can_be_disabled():
    app._crime_rewrite_cache.clear()
    before = len(CALLS)
    with patched("CRIME_QUERY_REWRITE", False), patched("llm_once", fake_llm("Кража")):
        out = app._crime_rewrite_query(LONG)
    check("disabled rewrite is silent", out == "" and len(CALLS) == before)


def test_clarify_caps_questions_at_three():
    payload = json.dumps({"enough": False, "questions": ["Какова сумма ущерба?", "Было ли насилие?",
                                                          "Применялось ли оружие?", "Сколько лет виновному?",
                                                          "Был ли сговор?"]}, ensure_ascii=False)
    with patched("llm_once", fake_llm(payload)):
        out = app._crime_clarify_questions(LONG)
    check("at most 3 questions", len(out) == 3, str(out))


def test_clarify_enough_and_garbage_give_no_questions():
    with patched("llm_once", fake_llm(json.dumps({"enough": True, "questions": []}))):
        check("enough -> no questions", app._crime_clarify_questions(LONG) == [])
    with patched("llm_once", fake_llm("это не JSON")):
        check("garbage -> no questions", app._crime_clarify_questions(LONG) == [])
    with patched("llm_once", fake_llm(error=RuntimeError("down"))):
        check("model error -> no questions", app._crime_clarify_questions(LONG) == [])
    with patched("llm_once", fake_llm(json.dumps({"enough": False, "questions": ["", "?", "ок"]}))):
        check("junk questions dropped", app._crime_clarify_questions(LONG) == [])


def test_clarify_endpoint_shape():
    client = app.app.test_client()
    payload = json.dumps({"enough": False, "questions": ["Какова сумма причинённого ущерба?"]}, ensure_ascii=False)
    with patched("llm_once", fake_llm(payload)):
        data = client.post("/api/crime/clarify", json={"description": LONG}).get_json()
    check("endpoint reports missing facts", data.get("enough") is False and data.get("questions") == ["Какова сумма причинённого ущерба?"], str(data))
    check("clarify tells the language of the question", data.get("lang") == "ru", str(data.get("lang")))
    check("empty description is 400", client.post("/api/crime/clarify", json={}).status_code == 400)


def test_clarify_can_be_disabled():
    with patched("CRIME_CLARIFY_ENABLED", False), patched("llm_once", fake_llm(json.dumps({"enough": False, "questions": ["Какова сумма?"]}))):
        check("disabled clarify asks nothing", app._crime_clarify_questions(LONG) == [])


def test_health_tells_page_where_the_model_runs():
    info = app.app.test_client().get("/health").get_json().get("crime_llm") or {}
    check("health has crime_llm", set(info) >= {"provider", "model", "cloud"}, str(info))
    check("cloud flag matches provider", info.get("cloud") == (app.LLM_PROVIDER == "openai"), str(info))


def test_prompt_no_longer_forces_insufficient_phrase():
    prompt = app._crime_qualify_prompt("описание", [], ["244"])
    check("phrase is conditional", "ТОЛЬКО если ни одна" in prompt)


def test_short_mode_uses_short_prompt_and_budget():
    client = app.app.test_client()
    seen = {}

    def fake_stream(prompt, max_tokens=None):
        seen["prompt"], seen["max"] = prompt, max_tokens
        yield "1. КВАЛИФИКАЦИЯ\n   • Основная статья УК РТ: статья 244 Кража"

    hit = {"article": "244", "article_title": "Кража", "title": "Статья 244. Кража",
           "text": "Статья 244. Кража, то есть тайное хищение чужого имущества, - наказывается лишением свободы.",
           "lexical_score": 50.0, "vector_score": None, "source": app.CRIME_RAG_SOURCE_NAME}
    # Поиск подменён: тест проверяет режим, а не содержимое индекса, которое
    # другие тесты вправе подменять в общем модуле app.
    with patched("_crime_ollama_stream", fake_stream), patched("llm_once", fake_llm("Кража")),             patched("retrieve_crime_rag", lambda query, top_k=None: ([hit], {"ready": True})),             patched("retrieve_related_norms", lambda query: []), patched("crime_rag_status", lambda: {}):
        short = client.post("/api/crime/qualify", json={"description": LONG, "mode": "short", "stream": False}).get_json()
        short_seen = dict(seen)
        full = client.post("/api/crime/qualify", json={"description": LONG, "stream": False}).get_json()
    check("short meta mode", short.get("mode") == "short", str(short.get("mode")))
    check("short budget is smaller", short_seen["max"] == app.CRIME_MAX_TOKENS_SHORT < app.CRIME_MAX_TOKENS_FULL, str(short_seen["max"]))
    check("short prompt is the short one", "не больше 220 слов" in short_seen["prompt"])
    check("full is the default", full.get("mode") == "full" and seen["max"] == app.CRIME_MAX_TOKENS_FULL, str(full.get("mode")))
    check("full prompt is the detailed one", "ЖЁСТКИЕ ПРАВИЛА (нарушение" in seen["prompt"])


def test_short_prompt_keeps_section_names_the_page_parses():
    prompt = app._crime_qualify_prompt_short("описание", [], ["244"])
    for name in ("1. ЧТО ДЕЛАТЬ ПОСТРАДАВШЕМУ", "2. ЧТО ГРОЗИТ НАРУШИТЕЛЮ", "3. КАКИЕ ЗАКОНЫ НАРУШЕНЫ", "Основная статья УК РТ"):
        check("short prompt has " + name, name in prompt)


def test_todo_block_is_grounded_in_procedure_articles():
    hit = {"article": "141", "article_title": "Заявление о преступлении", "title": "Статья 141",
           "text": "Статья 141. Заявление о преступлении. Заявление подаётся устно или письменно.",
           "lexical_score": 50.0, "source": app.CRIME_PROCEDURE_SOURCE}
    with_upk = app._crime_qualify_prompt_short("описание", [], ["244"], [hit], ["141"])
    without = app._crime_qualify_prompt_short("описание", [], ["244"], [], [])
    check("upk article is offered to the model", "СТАТЬИ УПК РТ ДЛЯ СОВЕТА" in with_upk and "ст. N УПК РТ" in with_upk)
    check("only listed upk numbers allowed", "Разрешены только эти статьи УПК: 141" in with_upk)
    check("without upk only general advice", "(общий совет)" in without and "СТАТЬИ УПК РТ ДЛЯ СОВЕТА" not in without)
    full = app._crime_qualify_prompt("описание", [], ["244"], [hit], ["141"])
    check("full prompt keeps todo block and six sections",
          "ЧТО ДЕЛАТЬ ПОСТРАДАВШЕМУ" in full and "6. ИТОГОВЫЙ ВЫВОД" in full and "6-8 пронумерованных" in full)


def test_upk_reference_is_checked_but_not_flagged():
    hit = {"article": "244", "article_title": "Кража", "title": "Статья 244. Кража",
           "text": "Статья 244. Кража.", "lexical_score": 50.0, "source": app.CRIME_RAG_SOURCE_NAME}
    upk = {"article": "141", "article_title": "Заявление о преступлении", "title": "Статья 141",
           "text": "Статья 141. Заявление.", "lexical_score": 40.0, "source": app.CRIME_PROCEDURE_SOURCE}
    answer = "1. Подайте заявление (ст. 141 УПК РТ).\n3. Основная статья: статья 244. Также ст. 999."

    with patched("_crime_ollama_stream", lambda prompt, max_tokens=None: iter([answer])),             patched("retrieve_crime_rag", lambda query, top_k=None: ([hit], {"ready": True})),             patched("retrieve_crime_procedure", lambda description: [upk]),             patched("retrieve_related_norms", lambda query: []), patched("crime_rag_status", lambda: {}),             patched("llm_once", fake_llm("Кража")):
        data = app.app.test_client().post("/api/crime/qualify", json={"description": LONG, "stream": False}).get_json()
    check("upk article 141 is not flagged", "141" not in data["unverified_articles"], str(data["unverified_articles"]))
    check("invented article 999 is flagged", data["unverified_articles"] == ["999"], str(data["unverified_articles"]))
    check("meta carries procedure articles", [a["article"] for a in data.get("procedure", [])] == ["141"])


def test_procedure_search_finds_application_article():
    items = app.retrieve_crime_procedure("Неизвестный вырвал у меня сумку и убежал")
    numbers = [i.get("article") for i in items]
    check("upk search returns articles", 1 <= len(numbers) <= app.CRIME_PROCEDURE_TOP, str(numbers))
    check("application for crime (st.141) is found", "141" in numbers, str(numbers))


TG_LONG = "Ман дишаб дар кӯчаи Рӯдакӣ ҳамёнамро гум кардам, касе онро аз дастам кашида гирифт ва гурехт"
VERDICT_RU = ("1. КВАЛИФИКАЦИЯ\n   • Основная статья УК РТ: ст. 162\n"
              "6. ИТОГОВЫЙ ВЫВОД\n   • Квалификация: ст. 162 УК РТ (грабёж)\n   • Наказание по статье: лишение свободы от 3 до 5 лет")


def test_tajik_rule_translates_quotes_too():
    check("quotes are no longer kept in Russian", "дословно по-русски" not in app.CRIME_TG_RULE)
    check("rule demands full Tajik", "тоже переведи на таджикский" in app.CRIME_TG_RULE and "Не оставляй русских слов" in app.CRIME_TG_RULE)
    check("section headings stay for the parser", "оставь без изменений на русском вместе с номерами" in app.CRIME_TG_RULE)


def test_summary_is_taken_from_russian_verdict():
    summary = app._crime_summary_lines(VERDICT_RU)
    check("qualification line", summary["qual"] == "ст. 162 УК РТ (грабёж)", str(summary))
    check("punishment line", summary["punish"] == "лишение свободы от 3 до 5 лет", str(summary))
    check("verdict is positive", summary["none"] is False)
    none = app._crime_summary_lines("6. ИТОГОВЫЙ ВЫВОД\n   • Квалификация: установить нельзя\n   • Наказание по статье: не указано")
    check("negative verdict is flagged", none["none"] is True and none["punish"] == "", str(none))
    check("empty text is none", app._crime_summary_lines("")["none"] is True)


def test_summary_is_translated_for_tajik_questions():
    with patched("_crime_to_tajik", lambda items: (["тг:" + i for i in items], [True] * len(items))):
        tg = app._crime_summary(VERDICT_RU, "tg")
        ru = app._crime_summary(VERDICT_RU, "ru")
    check("tajik summary translated", tg["qual"].startswith("тг:") and tg["punish"].startswith("тг:") and tg["lang"] == "tg", str(tg))
    check("russian summary untouched", ru["qual"] == "ст. 162 УК РТ (грабёж)" and ru["lang"] == "ru", str(ru))


def test_to_tajik_caches_and_falls_back():
    app.CRIME_TR_CACHE.clear()
    calls = []

    def cards(texts):
        calls.append(list(texts))
        return ["тг-" + t for t in texts]

    with patched("_tajik_cards", cards):
        first, flags = app._crime_to_tajik(["Кража", "Грабёж", ""])
        again, _ = app._crime_to_tajik(["Кража"])
    check("short strings translated in one call", first[:2] == ["тг-Кража", "тг-Грабёж"] and len(calls) == 1, str(first))
    check("empty string counts as done", flags == [True, True, True], str(flags))
    check("second request comes from cache", again == ["тг-Кража"] and len(calls) == 1, str(calls))

    app.CRIME_TR_CACHE.clear()
    with patched("_tajik_cards", lambda texts: None):
        out, flags = app._crime_to_tajik(["Мошенничество"])
    check("no translator -> original and not cached", out == ["Мошенничество"] and flags == [False] and not app.CRIME_TR_CACHE)

    long_text = "Статья 244. Кража. " * 40

    class FakeTranslator:
        @staticmethod
        def available():
            return True

        @staticmethod
        def translate_text(text, src, tgt):
            return "тг-длинный"

    class FakeLang:
        translator = FakeTranslator()

    with patched("_llm_tajik_available", lambda: False), patched("lang_service", FakeLang()):
        out, flags = app._crime_to_tajik([long_text])
    check("long statute text goes through the text translator", out == ["тг-длинный"] and flags == [True], str(out))
    app.CRIME_TR_CACHE.clear()


def test_translate_endpoint():
    client = app.app.test_client()
    check("empty items is 400", client.post("/api/crime/translate", json={}).status_code == 400)
    check("items must be a list", client.post("/api/crime/translate", json={"items": "x"}).status_code == 400)
    with patched("_crime_to_tajik", lambda items: (["тг-" + i for i in items], [True] * len(items))):
        data = client.post("/api/crime/translate", json={"items": ["Кража", "Грабёж"]}).get_json()
    check("endpoint returns translated items", data == {"items": ["тг-Кража", "тг-Грабёж"], "translated": [True, True]}, str(data))
    with patched("_crime_to_tajik", lambda items: (items, [False] * len(items))):
        many = client.post("/api/crime/translate", json={"items": ["а"] * 500}).get_json()
    check("item count is capped", len(many["items"]) == app.CRIME_TR_MAX_ITEMS, str(len(many["items"])))


def test_answer_language_follows_the_question():
    client = app.app.test_client()
    hit = {"article": "162", "article_title": "Грабёж", "title": "Статья 162. Грабёж",
           "text": "Статья 162. Грабёж.", "lexical_score": 50.0, "source": app.CRIME_RAG_SOURCE_NAME}
    patches = [
        patched("_crime_text_to_russian", lambda text: text),
        patched("_crime_ollama_stream", lambda prompt, max_tokens=None: iter([VERDICT_RU])),
        patched("retrieve_crime_rag", lambda query, top_k=None: ([hit], {"ready": True})),
        patched("retrieve_crime_procedure", lambda description: []),
        patched("retrieve_related_norms", lambda query: []),
        patched("crime_rag_status", lambda: {}),
        patched("_crime_translate_answer", lambda text: "ТАРҶУМА"),
        patched("_crime_to_tajik", lambda items: (["тг:" + i for i in items], [True] * len(items))),
    ]
    for ctx in patches:
        ctx.__enter__()
    try:
        tg = client.post("/api/crime/qualify", json={"description": TG_LONG, "stream": False}).get_json()
        ru = client.post("/api/crime/qualify", json={"description": LONG, "stream": False}).get_json()
        raw = client.post("/api/crime/qualify", json={"description": TG_LONG}).get_data(as_text=True)
        events = [json.loads(line) for line in raw.splitlines() if line.strip()]
        search_tg = client.post("/api/crime/search", json={"description": TG_LONG}).get_json()
        search_ru = client.post("/api/crime/search", json={"description": LONG}).get_json()
    finally:
        for ctx in reversed(patches):
            ctx.__exit__(None, None, None)
    check("tajik question -> tajik answer", tg["lang"] == "tg" and tg["qualification"].startswith("ТАРҶУМА"), str(tg.get("qualification")))
    check("tajik question -> tajik summary", tg["summary"]["lang"] == "tg" and tg["summary"]["qual"].startswith("тг:"), str(tg.get("summary")))
    check("russian question -> russian answer", ru["lang"] == "ru" and ru["qualification"] == VERDICT_RU, str(ru.get("qualification")))
    check("russian summary is not translated", ru["summary"]["qual"] == "ст. 162 УК РТ (грабёж)", str(ru.get("summary")))
    check("stream meta carries the language", events[0]["meta"]["lang"] == "tg")
    check("stream does not leak russian tokens for tajik", [e["response"] for e in events if "response" in e] == ["ТАРҶУМА"], str(events))
    check("stream done carries summary", events[-1].get("done") and events[-1]["summary"]["lang"] == "tg", str(events[-1]))
    check("search reports the language", search_tg["lang"] == "tg" and search_ru["lang"] == "ru")


def test_not_found_message_follows_the_question():
    client = app.app.test_client()
    patches = [
        patched("_crime_text_to_russian", lambda text: text),
        patched("retrieve_crime_rag", lambda query, top_k=None: ([], {"ready": True})),
        patched("retrieve_related_norms", lambda query: []),
        patched("crime_rag_status", lambda: {}),
    ]
    for ctx in patches:
        ctx.__enter__()
    try:
        tg = client.post("/api/crime/qualify", json={"description": TG_LONG, "stream": False}).get_json()
        ru = client.post("/api/crime/qualify", json={"description": LONG, "stream": False}).get_json()
        search_tg = client.post("/api/crime/search", json={"description": TG_LONG}).get_json()
    finally:
        for ctx in reversed(patches):
            ctx.__exit__(None, None, None)
    check("not found in tajik", tg["qualification"] == app.CRIME_NOT_FOUND_MESSAGE_TG and tg["lang"] == "tg")
    check("not found in russian", ru["qualification"] == app.CRIME_NOT_FOUND_MESSAGE and ru["lang"] == "ru")
    check("search not found in tajik", search_tg["message"] == app.CRIME_NOT_FOUND_MESSAGE_TG and search_tg["lang"] == "tg")
    check("tajik message has no russian-only letters", not set("ыщьц") & set(app.CRIME_NOT_FOUND_MESSAGE_TG.lower()))


def main():
    tests = [v for k, v in sorted(globals().items()) if k.startswith("test_") and callable(v)]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILS.append("%s: исключение\n%s" % (test.__name__, traceback.format_exc()))
    if FAILS:
        print("FAIL: %d из %d проверок" % (len(FAILS), CHECKS))
        for item in FAILS:
            print("  -", item)
        sys.exit(1)
    print("OK: %d проверок пройдено" % CHECKS)


if __name__ == "__main__":
    main()
