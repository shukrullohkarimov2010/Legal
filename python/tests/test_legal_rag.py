# -*- coding: utf-8 -*-
"""Модульные тесты движка поиска по кодексам РТ.

    python tests/test_legal_rag.py

Тесты не требуют собранного индекса и сети: они проверяют инварианты
стеммера, нарезки по статьям, синонимов и маршрутизатора на синтетических
данных. Регрессия в любом из них немедленно ломает качество поиска.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import legal_rag as lr  # noqa: E402

FAILURES: list[str] = []


def check(condition, message):
    if not condition:
        FAILURES.append(message)


def test_stemmer_collapses_inflections():
    """Словоформы одной лексемы обязаны давать одну основу."""
    groups = [
        ("договор", "договора", "договоры", "договором", "договорах"),
        ("отпуск", "отпуска", "отпуску", "отпусков"),
        ("работник", "работника", "работнику", "работников"),
        ("наказание", "наказания", "наказанию", "наказаний"),
        ("время", "времени", "временем", "временах"),
        ("статья", "статьи", "статьей"),
    ]
    for group in groups:
        stems = {lr._STEMMER.stem(word) for word in group}
        check(len(stems) == 1, f"стеммер: {group[0]} даёт разные основы {stems}")


def test_stemmer_keeps_distinct_lexemes_apart():
    """Стеммер не должен склеивать несвязанные слова."""
    pairs = [("договор", "договорённость"), ("работник", "работодатель"), ("налог", "налим")]
    for left, right in pairs:
        check(
            lr._STEMMER.stem(left) != lr._STEMMER.stem(right),
            f"стеммер склеил несвязанные слова: {left} / {right}",
        )


def test_tokenizer_drops_stopwords_and_keeps_abbreviations():
    tokens = lr.tokenize("Какой размер отпуска положен работнику по НДС и ГК?")
    check("отпуск" in tokens, "токенизатор потерял «отпуск»")
    check("ндс" in tokens, "токенизатор потерял аббревиатуру НДС")
    check("по" not in tokens and "и" not in tokens, "токенизатор оставил служебные слова")


def test_synonyms_match_by_stem():
    """Ключ «зарплата» обязан срабатывать на форму «зарплаты»."""
    expanded = lr.expand_query("Можно ли взять отпуск без сохранения зарплаты?").lower()
    check("заработная плата" in expanded, "синонимы: «зарплаты» не раскрылось")
    check("налог на добавленную стоимость" in lr.expand_query("ставка НДС").lower(),
          "синонимы: НДС не раскрылся")
    check(lr.expand_query("совершенно нейтральный текст") == "совершенно нейтральный текст",
          "синонимы: сработали там, где триггера нет")


def test_civil_penalty_wording_reaches_the_penalty_articles():
    """«Штраф по договору» — это неустойка, а не вид наказания."""
    expanded = lr.expand_query("Может ли суд уменьшить штраф по договору?").lower()
    check(expanded.count("неустойка") >= 2, "«штраф по договору» не привёл к неустойке: %s" % expanded)
    expanded = lr.expand_query("Может ли суд уменьшить договорную штрафную санкцию?").lower()
    check(expanded.count("неустойка") >= 2, "«штрафная санкция» + договор не привели к неустойке: %s" % expanded)
    expanded = lr.expand_query("Какой штраф за просрочку обязательства?").lower()
    check(expanded.count("неустойка") >= 2, "«штраф» + обязательство не привели к неустойке: %s" % expanded)
    # Без гражданского контекста «штраф» остаётся уголовным и налоговым: вес
    # неустойки не добавляется (одно упоминание даёт общий синоним «штраф»).
    check(lr.expand_query("Какой штраф за кражу?").lower().count("неустойка") <= 1,
          "уголовный вопрос про штраф получил вес неустойки")
    check(lr.expand_query("Кто платит штраф ГИБДД?").lower().count("неустойка") <= 1,
          "вопрос про штраф ГИБДД получил вес неустойки")
    # Если человек сам сказал «неустойка», правило не добавляет свою.
    once = lr.expand_query("Как уменьшить неустойку по договору?").lower()
    check(once.count("неустойка") == lr.expand_query("Как уменьшить неустойку?").lower().count("неустойка"),
          "правило дописало неустойку поверх собственной: %s" % once)


def test_router_prefers_matching_code():
    labour = lr.route_codes("за что работодатель может уволить работника")
    check(labour["трудовой кодекс.docx"] > labour["1918_ru_1.docx"],
          "маршрутизатор не выделил Трудовой кодекс")
    family = lr.route_codes("каких детей можно усыновить")
    check(family["семейный кодекс.docx"] > family["трудовой кодекс.docx"],
          "маршрутизатор не выделил Семейный кодекс на «усыновить»")
    neutral = lr.route_codes("здравствуйте")
    check(set(neutral.values()) == {1.0}, "маршрутизатор сработал на пустом сигнале")


SAMPLE = """
РАЗДЕЛ I. ОБЩИЕ ПОЛОЖЕНИЯ
ГЛАВА 1. ОСНОВЫ
Статья 1. Первая статья
Статья 2. Вторая статья
Статья 3. Третья статья
ГЛАВА 2. ПРЕДМЕТ
Статья 1. Первая статья
Тело первой статьи с достаточным объёмом текста для прохождения порога длины.
Второй абзац первой статьи.
Статья 2. Вторая статья
Тело второй статьи, также достаточно длинное, чтобы не быть отброшенным как оглавление.
Статья 3. Третья статья
Тело третьей статьи с нормой о сроках и порядке исполнения обязательств сторонами.
"""


def test_chunker_skips_table_of_contents():
    """Строки оглавления без тела не должны попадать в индекс."""
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    check(len(chunks) == 3, f"нарезка: ожидалось 3 фрагмента, получено {len(chunks)}")
    for chunk in chunks:
        check(chunk["title"].startswith("Статья "), f"нарезка: битый заголовок {chunk['title']!r}")
        check(chunk["chapter"] == "2", f"нарезка: неверная глава {chunk['chapter']!r}")
        check(len(chunk["text"]) > 60, "нарезка: в индекс попала строка оглавления")


def test_chunker_preserves_document_order():
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    check([c["article"] for c in chunks] == ["1", "2", "3"],
          "нарезка: порядок статей не совпадает с порядком в документе")


def test_citation_built_from_metadata():
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    citation = lr.citation_for(chunks[0])
    check(citation == "Тестовый кодекс, Глава 2, Статья 1", f"ссылка собрана неверно: {citation}")


def test_search_finds_article_by_meaning():
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    index = dict({"chunks": chunks, "ready": True}, **lr.build_stats(chunks))
    results = lr.search(index, "сроки и порядок исполнения обязательств", top_k=3)
    check(results and results[0]["article"] == "3",
          f"поиск: ожидалась статья 3, получено {results[0]['article'] if results else 'ничего'}")
    check(results[0].get("citation"), "поиск: результат без готовой ссылки на норму")


def test_search_honours_explicit_article_number():
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    index = dict({"chunks": chunks, "ready": True}, **lr.build_stats(chunks))
    results = lr.search(index, "статья 2", top_k=3)
    check(results and results[0]["article"] == "2",
          "поиск: прямая ссылка на номер статьи не сработала")


def test_normalize_document_text_handles_cr():
    """Извлечение .doc отдаёт абзацы через \\r — без нормализации теряются границы."""
    normalized = lr.normalize_document_text("Статья 1. Название\rТело статьи\r\nЕщё абзац")
    check(normalized.count("\n") == 2, "нормализация переводов строк не сработала")


def test_embeddings_degrade_gracefully():
    """Недоступный векторный слой не должен ронять поиск."""
    provider = lr.EmbeddingProvider(url="http://127.0.0.1:9/none")
    check(provider.available() is False, "провайдер не распознал недоступность")
    chunks = lr.build_article_chunks(SAMPLE, "test.docx", "Тестовый кодекс")
    index = dict({"chunks": chunks, "ready": True}, **lr.build_stats(chunks))
    results = lr.search(index, "сроки исполнения", top_k=2, store={"ids": [], "vectors": None},
                        provider=provider)
    check(bool(results), "поиск упал при недоступных эмбеддингах")


def test_every_code_has_short_name_and_routing_hints():
    """Кодекс без сокращения или маркеров молча выпадает из маршрутизации."""
    missing_hints = sorted(set(lr.CODE_SHORT) - set(lr.CODE_HINTS))
    assert not missing_hints, "нет маркеров маршрутизации: %s" % missing_hints
    assert lr.CODE_SHORT["КОДЕКС ОБ АДМИНИСТРАТИВНЫХ ПРОЦЕДУРАХ.docx"] == "КоАПр РТ"
    assert lr.CODE_SHORT["ЭКОНОМИЧЕСКИЙ ПРОЦЕССУАЛЬНЫЙ КОДЕКС.docx"] == "ЭПК РТ"


def test_matched_codes_names_only_areas_the_description_mentions():
    """Страница УК показывает смежные нормы лишь из названных областей."""
    health = lr.matched_codes("врач по неосторожности причинил вред пациенту")
    assert "Кодекс здравохранение.docx" in health
    assert "Лесной кодекс.docx" in lr.matched_codes("срубил деревья в лесу без разрешения")
    assert "налоговый кодекс.docx" in lr.matched_codes("директор не платил налоги")
    # Описание без названной области не даёт ни одного смежного кодекса.
    for text in ("вырвал сумку у женщины и убежал", "какая погода в Душанбе"):
        foreign = {s for s in lr.matched_codes(text) if s != "574_ru.doc"}
        assert not foreign, "%s -> %s" % (text, foreign)
    assert lr.matched_codes("") == {}


def test_typo_article_heading_is_repaired_but_cross_references_are_not():
    """«Статьи 55. Виды…» в исходнике — заголовок; «Статьи 12 и 13 …» — ссылка."""
    text = lr.normalize_document_text(
        "Статья 5. Название\nТекст.\nСтатьи 12 и 13 настоящего Кодекса применяются иначе.\n"
        "Статьи 55. Виды процедур\nТело статьи."
    ).split("\n")
    assert text[2].startswith("Статьи 12 и 13"), text[2]
    assert text[3] == "Статья 55. Виды процедур", text[3]


def test_matched_codes_orders_by_number_of_markers():
    hits = lr.matched_codes("врач и пациент: медицинская помощь, лечение, диагноз")
    assert next(iter(hits)) == "Кодекс здравохранение.docx"


def main() -> int:
    tests = [value for name, value in sorted(globals().items()) if name.startswith("test_")]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILURES.append(f"{test.__name__}: исключение\n{traceback.format_exc()}")

    if FAILURES:
        print(f"ПРОВАЛЕНО {len(FAILURES)} проверок из {len(tests)} тестов:")
        for failure in FAILURES:
            print("  ✗", failure)
        return 1
    print(f"OK: {len(tests)} тестов пройдено")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
