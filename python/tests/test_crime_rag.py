# -*- coding: utf-8 -*-
"""Тесты RAG-квалификации по УК РТ (страница /contract/ugalov-kodex).

    python tests/test_crime_rag.py

Страница обязана отвечать только тем, что нашлось в Уголовном кодексе
Республики Таджикистан. Отсюда две группы проверок:

* чистые функции из app.py — приём описания, список разрешённых статей,
  отлов ссылок мимо выдачи и порог, ниже которого совпадение считается шумом;
* качество самого поиска на собранном индексе — бытовое описание события
  обязано поднимать профильную статью, а посторонний вопрос не должен
  проходить порог вовсе.

Модуль app целиком не импортируется: он тянет Flask, RAG-индекс и Ollama,
а проверяемая логика от них не зависит. Проверки на индексе пропускаются,
если индекс ещё не собран.
"""

from __future__ import annotations

import ast
import json
import os
import re
import sys
import traceback

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
APP_PATH = os.path.join(ROOT, "app.py")
CHAT_INDEX = os.path.join(
    os.path.dirname(ROOT), "storage", "app", "rag", "laws-rt-chat.index.json"
)
CRIME_SOURCE = "574_ru.doc"

sys.path.insert(0, ROOT)

import legal_rag as lr  # noqa: E402

FAILURES: list[str] = []
SKIPPED: list[str] = []


def _load_from_app(*names):
    """Достаёт функции и константы из app.py, не выполняя модуль целиком."""
    with open(APP_PATH, encoding="utf-8") as handle:
        tree = ast.parse(handle.read())

    namespace: dict = {"re": re}
    wanted = set(names)
    for node in tree.body:
        picked = (
            isinstance(node, ast.FunctionDef) and node.name in wanted
        ) or (
            isinstance(node, ast.Assign) and getattr(node.targets[0], "id", "") in wanted
        )
        if picked:
            exec(compile(ast.Module([node], []), APP_PATH, "exec"), namespace)

    missing = [name for name in names if name not in namespace]
    if missing:
        raise AssertionError("не найдено в app.py: %s" % ", ".join(missing))
    return [namespace[name] for name in names]


(
    crime_description,
    allowed_articles,
    unverified,
) = _load_from_app(
    "_crime_description", "_allowed_article_numbers", "_unverified_article_numbers",
    "_ARTICLE_REF_RE", "_crime_text_to_russian", "_crime_llm_translate_to_russian",
)[:3]


def _min_score():
    """Порог отсечки шума читается из исходника: дублировать его нельзя."""
    with open(APP_PATH, encoding="utf-8") as handle:
        source = handle.read()
    match = re.search(r'CRIME_RAG_MIN_SCORE",\s*"([\d.]+)"', source)
    if not match:
        raise AssertionError("CRIME_RAG_MIN_SCORE не найден в app.py")
    return float(match.group(1))


MIN_SCORE = _min_score()


def check(condition, message):
    if not condition:
        FAILURES.append(message)


def _crime_index():
    """Подындекс только по УК РТ — так же, как его строит load_crime_rag_index."""
    if not os.path.isfile(CHAT_INDEX):
        return None
    with open(CHAT_INDEX, encoding="utf-8") as handle:
        chunks = json.load(handle).get("chunks", [])
    chunks = [chunk for chunk in chunks if chunk.get("source") == CRIME_SOURCE]
    if not chunks:
        return None
    stats = lr.build_stats(chunks)
    return {
        "chunks": chunks,
        "idf": stats["idf"],
        "avgdl": stats["avgdl"],
        "ready": True,
    }


def _search(index, query, top_k=8):
    return lr.search(index, lr.expand_query(query), top_k=top_k)


def _grounded(results):
    return [
        item for item in results
        if float(item.get("lexical_score") or 0) >= MIN_SCORE
        or float(item.get("vector_score") or 0) >= lr.VECTOR_ONLY_MIN_SCORE
    ]


def test_description_accepts_both_field_names():
    """Регрессия: фронт слал crime_description, эндпоинт читал description."""
    check(crime_description({"description": "кража"}) == "кража", "не принят ключ description")
    check(crime_description({"crime_description": "кража"}) == "кража",
          "не принят ключ crime_description")
    check(crime_description({"query": " грабёж "}) == "грабёж", "не обрезаны пробелы у query")
    check(crime_description({}) == "", "пустой запрос должен давать пустую строку")
    check(crime_description({"description": "   "}) == "", "пробельное описание должно считаться пустым")


def test_allowed_articles_keep_search_order():
    """Список разрешённых статей — это выдача поиска без повторов."""
    results = [{"article": "248"}, {"article": "248"}, {"article": "244"}, {"article": ""}]
    check(allowed_articles(results) == ["248", "244"],
          "порядок или дедупликация статей нарушены: %s" % allowed_articles(results))
    check(allowed_articles([]) == [], "пустая выдача должна давать пустой список")


def test_unverified_catches_articles_outside_rag():
    """Статья, которой не было в выдаче, обязана попасть в список непроверенных."""
    text = "Деяние подпадает под статью 248 УК РТ, а также ст. 158 и ч. 2 ст. 161."
    check(unverified(text, ["248"]) == ["158", "161"],
          "не пойманы ссылки мимо RAG: %s" % unverified(text, ["248"]))
    check(unverified("Квалификация по статье 248 УК РТ", ["248", "244"]) == [],
          "статья из выдачи помечена как непроверенная")
    # Часть статьи — не статья: «ч. 2 ст. 248» не должна превращаться
    # в ссылку на несуществующую статью 2.
    check(unverified("Квалификация по ч. 2 ст. 248 УК РТ", ["248"]) == [],
          "номер части принят за номер статьи: %s"
          % unverified("Квалификация по ч. 2 ст. 248 УК РТ", ["248"]))


def test_index_holds_only_criminal_code():
    """В подындексе не должно быть ни одной статьи чужого кодекса."""
    index = _crime_index()
    if index is None:
        SKIPPED.append("индекс кодексов не собран — проверки поиска пропущены")
        return
    sources = {chunk.get("source") for chunk in index["chunks"]}
    check(sources == {CRIME_SOURCE}, "в уголовный подындекс попали чужие источники: %s" % sources)


def test_everyday_description_finds_its_article():
    """Событие описано бытовыми словами — статья обязана найтись.

    Именно здесь проваливался поиск без синонимов: «вырвал сумку и убежал»
    не давало ни одного совпадения, потому что кодекс называет это
    «открытым хищением чужого имущества».
    """
    index = _crime_index()
    if index is None:
        return
    cases = [
        ("Вчера вечером на улице Рудаки неизвестный вырвал у меня сумку из рук и убежал", "248"),
        ("В метро в час пик кто-то вытащил у меня из кармана телефон", "244"),
        ("На меня напали двое, один держал нож и угрожал убить, второй забрал кошелёк", "249"),
        ("Мне позвонили из банка и обманом заставили перевести сбережения", "247"),
        ("Ко мне подошли трое и потребовали денег, угрожая расправой над семьёй", "250"),
        ("В баре мужчина ударил меня кулаком по лицу три раза, остались синяки", "116"),
    ]
    for description, expected in cases:
        found = [item.get("article") for item in _grounded(_search(index, description))]
        check(expected in found,
              "ст. %s не найдена по описанию %r, поднялись: %s" % (expected, description[:40], found))


def test_off_topic_question_stays_unanswered():
    """Вопрос не по уголовному праву не должен проходить порог опоры.

    Поиск всегда возвращает лучшее из имеющегося, поэтому решает не факт
    выдачи, а порог: без него страница отвечала бы по случайной статье.
    """
    index = _crime_index()
    if index is None:
        return
    for question in ("как приготовить плов", "какая погода завтра в Душанбе",
                     "хочу купить билет на самолет"):
        grounded = _grounded(_search(index, question))
        check(not grounded,
              "посторонний вопрос %r прошёл порог: %s"
              % (question, [(i.get("article"), i.get("lexical_score")) for i in grounded]))


def test_answer_carries_verifiable_citation():
    """У каждого фрагмента есть готовая ссылка на норму — её показывает страница."""
    index = _crime_index()
    if index is None:
        return
    results = _grounded(_search(index, "у меня украли телефон из кармана"))
    check(bool(results), "поиск по краже не дал ни одного фрагмента")
    for item in results:
        citation = item.get("citation") or lr.citation_for(item)
        check("Уголовный кодекс" in (citation or ""),
              "фрагмент без ссылки на УК РТ: %r" % citation)
        check(bool(item.get("text")), "фрагмент без текста статьи")


def main() -> int:
    tests = [value for name, value in sorted(globals().items()) if name.startswith("test_")]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILURES.append(f"{test.__name__}: исключение\n{traceback.format_exc()}")

    for note in SKIPPED:
        print("  ~", note)
    if FAILURES:
        print(f"ПРОВАЛЕНО {len(FAILURES)} проверок из {len(tests)} тестов:")
        for failure in FAILURES:
            print("  ✗", failure)
        return 1
    print(f"OK: {len(tests)} тестов пройдено")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
