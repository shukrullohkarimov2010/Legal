# -*- coding: utf-8 -*-
"""Тесты графа связей статей.

    python tests/test_article_graph.py

Модуль article_graph не обращается ни к модели, ни к RAG, поэтому тесты идут
без сервера и без ключей.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import article_graph as ag  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def refs(text, code="ТК РТ", article="10"):
    return [(r["code"], r["article"]) for r in ag.parse_references(text, code, article)]


def test_parse_basic_references():
    check("this code", refs("Порядок определён статьёй 45 настоящего Кодекса.") == [("ТК РТ", "45")])
    check("bare reference stays in own code", refs("В случаях, предусмотренных статьёй 45, работник уведомляется.") == [("ТК РТ", "45")])
    check("named other code", refs("наказание по статье 75 Уголовного кодекса Республики Таджикистан") == [("УК РТ", "75")])
    check("this code wins over an earlier named code",
          refs("Налоговый кодекс Республики Таджикистан не применяется, см. статью 12 настоящего Кодекса") == [("ТК РТ", "12")])
    check("list of articles", refs("статьями 5, 7 и 9 настоящего Кодекса") == [("ТК РТ", "5")] or
          set(refs("статьи 5, 7 и 9 настоящего Кодекса")) == {("ТК РТ", "5"), ("ТК РТ", "7"), ("ТК РТ", "9")})
    check("dotted number", refs("в порядке статьи 12.1 настоящего Кодекса") == [("ТК РТ", "12.1")])


def test_parse_noise_is_dropped():
    check("own heading ignored", refs("Статья 10. Заголовок\nПравила настоящей статьи не применяются.") == [])
    check("self reference ignored", refs("как указано в статье 10 настоящего Кодекса") == [])
    check("law reference ignored", refs("в соответствии со статьёй 5 Закона Республики Таджикистан «О труде»") == [])
    check("amendment note ignored", refs("Работник уведомляется (в редакции Закона РТ от 13.11.2020 №1 ст. 3).") == [])
    check("law before reference ignored", refs("Законом РТ «Об охране труда» (статья 4) установлено") == [])
    check("huge list ignored", refs("статьи 1, 2, 3, 4, 5, 6, 7, 8, 9, 11 настоящего Кодекса") == [])
    check("no reference", refs("Просто текст без ссылок") == [])


def make_chunks():
    def chunk(source, short, article, title, text, part=1):
        return {"source": source, "code_short": short, "code": short + " (полное)", "article": article,
                "article_title": title, "text": text, "part": part}
    return [
        chunk("tk.docx", "ТК РТ", "42", "Расторжение", "Статья 42. Расторжение\nСм. статью 45 настоящего Кодекса и статью 75 Уголовного кодекса Республики Таджикистан."),
        chunk("tk.docx", "ТК РТ", "45", "Предупреждение", "Статья 45. Предупреждение\nПредупреждение даётся письменно."),
        chunk("tk.docx", "ТК РТ", "46", "Срочный", "Статья 46. Срочный\nПо правилам статьи 45 настоящего Кодекса. Также статьи 999 настоящего Кодекса."),
        chunk("uk.docx", "УК РТ", "75", "Давность", "Статья 75. Давность\nТекст."),
    ]


def build():
    ids = {"tk.docx": "tk", "uk.docx": "uk"}
    return ag.ArticleGraph(make_chunks(), lambda source: ids[source])


def test_graph_edges_and_validation():
    g = build()
    check("nodes", g.stats()["articles"] == 4, str(g.stats()))
    check("edges only to existing articles", g.stats()["links"] == 3, str(g.stats()))
    check("cross-code edge", ("uk", "75") in g.out[("tk", "42")])
    check("missing article dropped", ("tk", "999") not in g.out[("tk", "46")])
    check("incoming index", set(g.inn[("tk", "45")]) == {("tk", "42"), ("tk", "46")})
    check("has", g.has("tk", "45") and not g.has("tk", "999"))


def test_subgraph():
    g = build()
    sub = g.subgraph([("tk", "45")])
    ids = {n["id"] for n in sub["nodes"]}
    check("neighbours included", ids == {"tk:45", "tk:42", "tk:46"}, str(ids))
    check("focus flagged", [n["id"] for n in sub["nodes"] if n["focus"]] == ["tk:45"])
    check("edges inside the node set", {(e["s"], e["t"]) for e in sub["edges"]} == {("tk:42", "tk:45"), ("tk:46", "tk:45")}, str(sub["edges"]))
    check("snippet kept", all(e["snippet"] for e in sub["edges"]))
    check("unknown focus ignored", g.subgraph([("zz", "1")])["nodes"] == [])
    check("limit respected", len(g.subgraph([("tk", "42"), ("tk", "45"), ("tk", "46")], limit=2)["nodes"]) == 2)
    check("duplicates in focus", len(g.subgraph([("tk", "45"), ("tk", "45")])["nodes"]) == 3)


def test_article_links():
    g = build()
    info = g.article_links("tk", "45")
    check("incoming listed", [r["id"] for r in info["in"]] == ["tk:42", "tk:46"] or {r["id"] for r in info["in"]} == {"tk:42", "tk:46"})
    check("outgoing empty", info["out"] == [])
    check("unknown article", g.article_links("tk", "999") is None)
    check("details carried", info["in"][0]["title"] and info["in"][0]["snippet"])


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
