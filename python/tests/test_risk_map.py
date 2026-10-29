# -*- coding: utf-8 -*-
"""Тесты карты рисков: привязка находок к абзацам договора.

    python tests/test_risk_map.py

Модуль risk_map не обращается ни к модели, ни к RAG, поэтому тесты идут
без сервера и без ключей.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import risk_map as rm  # noqa: E402

FAILS = []
CHECKS = 0

CONTRACT = """ДОГОВОР ПОСТАВКИ

1. Поставщик обязуется передать товар Покупателю в течение 10 дней.

2. За просрочку оплаты Покупатель уплачивает неустойку в размере 1% от суммы за каждый день просрочки.

3. Споры разрешаются в суде по месту нахождения Поставщика.
"""


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def test_split_paragraphs():
    paragraphs, truncated = rm.split_paragraphs(CONTRACT)
    check("blank lines dropped", len(paragraphs) == 4, str(paragraphs))
    check("not truncated", truncated is False)
    many, cut = rm.split_paragraphs("\n".join("пункт номер %d договора" % i for i in range(rm.MAX_PARAGRAPHS + 5)))
    check("long contract truncated", cut is True and len(many) == rm.MAX_PARAGRAPHS)
    long_one, _ = rm.split_paragraphs("слово " * 1000)
    check("long paragraph cut", len(long_one[0]) <= rm.MAX_PARAGRAPH_CHARS + 1)


def test_exact_location_and_span():
    paragraphs, _ = rm.split_paragraphs(CONTRACT)
    found = rm.locate(paragraphs, "«неустойку   в размере 1% от суммы»")
    check("exact quote found in paragraph 2", found is not None and found[0] == 2, str(found))
    start, end = found[1]
    check("span covers the quote", paragraphs[2][start:end].lower().startswith("неустойку"), paragraphs[2][start:end])
    check("quote edges ignored", rm.locate(paragraphs, "«Споры разрешаются в суде по месту нахождения Поставщика»")[0] == 3)


def test_fuzzy_location_without_span():
    paragraphs, _ = rm.split_paragraphs(CONTRACT)
    found = rm.locate(paragraphs, "Покупатель платит неустойку за каждый день просрочки оплаты")
    check("paraphrase located by stems", found is not None and found[0] == 2 and found[1] is None, str(found))
    check("unrelated quote not located", rm.locate(paragraphs, "Арендатор обязан вносить арендную плату ежемесячно") is None)
    check("empty quote not located", rm.locate(paragraphs, "") is None)
    check("short quote not located", rm.locate(paragraphs, "суд") is None)


def test_build_map():
    findings = [
        {"severity": "high", "title": "Чрезмерная неустойка", "fragment": "неустойку в размере 1% от суммы за каждый день просрочки",
         "why": "Выше допустимого", "fix": "Снизить", "norm": "ГК РТ, статья 384", "norm_text": "Текст статьи"},
        {"severity": "low", "title": "Подсудность", "fragment": "Споры разрешаются в суде по месту нахождения Поставщика"},
        {"severity": "medium", "title": "Нет такого пункта", "fragment": "Гарантийный срок составляет два года"},
    ]
    result = rm.build_risk_map(CONTRACT, findings)
    rows = {row["i"]: row for row in result["paragraphs"]}
    check("high finding marks paragraph 2", rows[2].get("severity") == "high" and rows[2]["findings"] == [0], str(rows[2]))
    check("exact span passed", rows[2]["spans"][0]["f"] == 0 and rows[2]["spans"][0]["e"] > rows[2]["spans"][0]["s"])
    check("low finding marks paragraph 3", rows[3].get("severity") == "low")
    check("clean paragraph untouched", "severity" not in rows[1])
    check("unplaced finding kept", result["unplaced"] == [2] and result["findings"][2]["para"] is None)
    check("counts", result["counts"] == {"high": 1, "medium": 1, "low": 1}, str(result["counts"]))
    check("norm text carried", result["findings"][0]["norm_text"] == "Текст статьи")


def test_worst_severity_wins_and_display():
    findings = [
        {"severity": "low", "title": "a", "fragment": "Поставщик обязуется передать товар Покупателю в течение 10 дней"},
        {"severity": "high", "title": "b", "fragment": "передать товар Покупателю в течение 10 дней"},
    ]
    shown = [{"title": "А-тг", "why": "почему", "fix": "", "fragment": "цитата-тг"}, {"title": "Б-тг"}]
    result = rm.build_risk_map(CONTRACT, findings, display=shown)
    row = next(r for r in result["paragraphs"] if r.get("findings"))
    check("worst severity wins", row["severity"] == "high" and row["findings"] == [0, 1], str(row))
    check("display texts used", result["findings"][0]["title"] == "А-тг" and result["findings"][0]["fragment"] == "цитата-тг")
    bad = rm.build_risk_map(CONTRACT, findings, display=[{"title": "x"}])
    check("mismatched display ignored", bad["findings"][0]["title"] == "a")


def test_robust_to_garbage():
    result = rm.build_risk_map("", [None, "x", {"fragment": None, "severity": "weird"}])
    check("garbage findings tolerated", len(result["findings"]) == 1 and result["findings"][0]["severity"] == "medium", str(result))
    check("empty text gives empty map", result["paragraphs"] == [] and result["unplaced"] == [0])
    check("no findings", rm.build_risk_map(CONTRACT, None)["findings"] == [])


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
