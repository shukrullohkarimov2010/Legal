# -*- coding: utf-8 -*-
"""Каталог кодексов для страницы /contract/codex.

    python tests/test_codex.py

Проверяется то, что код обязан гарантировать сам: статья собирается из фрагментов
индекса без повторённых заголовков и жёстких переносов, оглавление идёт в порядке
документа, поиск различает номер, название и текст. Индекс синтетический, чтобы тест
не зависел от содержимого настоящих кодексов.
"""

from __future__ import annotations

import os
import sys
import traceback

HERE = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, HERE)
os.environ.setdefault("OPENAI_API_KEY", "test-key-not-used")

import codex_catalog  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def chunk(source, article, text, part=1, parts=1, **extra):
    title = text.split("\n")[0]
    base = {
        "id": "%s:%s:%s" % (source, article, part), "source": source, "code": "Тестовый кодекс " + source,
        "code_short": "ТК РТ", "section": "I", "section_title": "ОБЩАЯ ЧАСТЬ", "chapter": "1",
        "chapter_title": "ОСНОВЫ", "article": str(article), "article_title": title.split(". ", 1)[-1],
        "title": title, "part": str(part), "parts": str(parts), "text": text,
    }
    base.update(extra)
    return base


# Старый .doc: заголовок перенесён жёстко (68 символов в строке), текст тоже.
WRAPPED_TITLE = ("Статья 15. Действия уголовного закона в отношении лиц, совершивших\n"
                 "преступление вне пределов Республики Таджикистан\n"
                 "1) Гражданин Республики Таджикистан, а также постоянно проживающее в\n"
                 "ней лицо без гражданства, подлежит ответственности, если")
CONTINUATION = ("Статья 15. Действия уголовного закона в отношении лиц, совершивших\n"
                "иное не предусмотрено международными актами.\n"
                "2) Иностранные граждане подлежат ответственности.")


def fake_index():
    chunks = [
        chunk("574_ru.doc", 14, "Статья 14. Кража\n1) Кража, то есть тайное хищение чужого имущества,\n"
                                "- наказывается штрафом.", code_short="УК РТ", section_title="УГОЛОВНЫЙ ЗАКОН"),
        chunk("574_ru.doc", 15, WRAPPED_TITLE, part=1, parts=2, code_short="УК РТ"),
        chunk("574_ru.doc", 15, CONTINUATION, part=2, parts=2, code_short="УК РТ"),
        chunk("574_ru.doc", 16, "Статья 16. Грабёж\nОткрытое хищение чужого имущества.", code_short="УК РТ",
              chapter="2", chapter_title="ПРЕСТУПЛЕНИЯ ПРОТИВ СОБСТВЕННОСТИ"),
        chunk("1918_ru_1.docx", 14, "Статья 14. Недвижимость\nК недвижимости относится земля.", code_short="ГК РТ"),
        chunk("новый_кодекс.docx", 1, "Статья 1. Общие положения\nТекст.", code_short="НК"),
    ]
    return {"ready": True, "built_at": 1700000000.0, "chunks": chunks}


def fake_lexical(query, limit):
    return [(10.0, fake_index()["chunks"][4]), (1.0, fake_index()["chunks"][3])]


def test_wrapped_title_is_restored_and_removed_from_body():
    c = fake_index()["chunks"][1]
    title, body = codex_catalog.split_header(c)
    check("wrapped .doc title is read to the end",
          title == "Действия уголовного закона в отношении лиц, совершивших преступление вне пределов Республики Таджикистан", title)
    check("body starts after the title", body[0].startswith("1) Гражданин"), str(body[:1]))
    docx = fake_index()["chunks"][4]
    title, body = codex_catalog.split_header(docx)
    check("docx title is kept as is", title == "Недвижимость", title)
    check("docx body has no header", body == ["К недвижимости относится земля."], str(body))


def test_parts_are_joined_without_repeated_header():
    parts = [c for c in fake_index()["chunks"] if c["article"] == "15"]
    blocks = codex_catalog.assemble_article(list(reversed(parts)))     # порядок частей не важен
    text = " ".join(b["text"] for b in blocks)
    check("repeated header of part 2 is dropped", text.count("Действия уголовного закона") == 0, text)
    check("part 2 continues the sentence", "если иное не предусмотрено" in text, text)
    check("list items stay separate", [b["kind"] for b in blocks[:2]] == ["li", "li"], str(blocks))


def test_dash_sanction_continues_the_sentence():
    blocks = codex_catalog.assemble_article([fake_index()["chunks"][0]])
    check("sanction dash is not a new item", len(blocks) == 1 and "имущества, - наказывается" in blocks[0]["text"], str(blocks))
    reflowed = codex_catalog.reflow(["1) Первый пункт.", "- вторая строка списка", "а) подпункт;"])
    check("dash after a full stop is a list item", [b["kind"] for b in reflowed] == ["li", "li", "li"], str(reflowed))


def test_hard_wrapped_lines_are_merged():
    blocks = codex_catalog.reflow(["Лицо, совершившее деяние в", "пределах государства,", "несёт ответственность.", "Примечание. Текст."])
    check("wrapped lines become one paragraph", blocks[0]["text"] == "Лицо, совершившее деяние в пределах государства, несёт ответственность.", str(blocks))
    check("note is its own block", blocks[1]["kind"] == "note", str(blocks))


def test_catalog_groups_and_order():
    catalog = codex_catalog.Catalog(fake_index())
    data = catalog.catalog()
    ids = [c["id"] for c in data["codes"]]
    check("civil before criminal, unknown code last", ids[0] == "gk" and ids[1] == "uk" and ids[-1].startswith("x"), str(ids))
    check("unknown code goes to 'other'", data["codes"][-1]["group"] == "other")
    check("groups follow the fixed order", [g["id"] for g in data["groups"]] == ["civil", "criminal", "other"], str(data["groups"]))
    uk = next(c for c in data["codes"] if c["id"] == "uk")
    check("article count is unique articles", uk["articles"] == 3 and uk["chapters"] == 2, str(uk))
    check("total is the sum", data["total_articles"] == sum(c["articles"] for c in data["codes"]))
    check("built_at comes from the index", data["built_at"] == 1700000000.0)


def test_toc_and_article_navigation():
    catalog = codex_catalog.Catalog(fake_index())
    toc = catalog.toc("uk")
    chapters = toc["sections"][0]["chapters"]
    check("toc keeps document order", [a["n"] for ch in chapters for a in ch["articles"]] == ["14", "15", "16"], str(chapters))
    check("toc uses the full title", chapters[0]["articles"][1]["t"].endswith("Республики Таджикистан"))
    article = catalog.article("uk", "15")
    check("prev and next are neighbours", article["prev"]["n"] == "14" and article["next"]["n"] == "16")
    check("citation names the code", article["citation"] == "УК РТ, ст. 15", article["citation"])
    check("first article has no prev", catalog.article("uk", "14")["prev"] is None)
    check("unknown code or article is None", catalog.toc("nope") is None and catalog.article("uk", "999") is None)


def test_search_number_title_text():
    catalog = codex_catalog.Catalog(fake_index())
    by_number = catalog.search("ст. 14", fake_lexical)
    check("number finds the article in every code", sorted(r["short"] for r in by_number["results"]) == ["ГК РТ", "УК РТ"], str(by_number))
    check("number search reports its kind", by_number["kind"] == "number")
    hinted = catalog.search("УК 14", fake_lexical)
    check("code prefix narrows number search", [r["short"] for r in hinted["results"]] == ["УК РТ"], str(hinted))
    word = catalog.search("кража", fake_lexical)
    check("title match comes first", word["results"][0]["n"] == "14" and word["results"][0]["match"] == "title", str(word))
    check("text hits are added after titles", any(r["match"] == "text" for r in word["results"]), str(word))
    check("weak text hits are dropped", all(r["t"] != "Грабёж" for r in word["results"]), str(word))
    scoped = catalog.search("кража", fake_lexical, code_id="gk")
    check("code filter limits results", all(r["code"] == "gk" for r in scoped["results"]), str(scoped))
    check("empty query is empty", catalog.search("  ", fake_lexical)["results"] == [])
    check("limit is respected", len(catalog.search("ст. 14", fake_lexical, limit=1)["results"]) == 1)


def test_endpoints():
    import app
    index = fake_index()
    old = app.load_chat_rag_index
    app.load_chat_rag_index = lambda force=False: index
    app._codex_state.update({"key": None, "catalog": None})
    try:
        client = app.app.test_client()
        catalog = client.get("/api/codex/catalog").get_json()
        check("catalog endpoint lists codes", [c["id"] for c in catalog["codes"]][:2] == ["gk", "uk"], str(catalog)[:200])
        check("toc endpoint", client.get("/api/codex/toc?code=uk").get_json()["code"]["short"] == "УК РТ")
        check("article endpoint", client.get("/api/codex/article?code=uk&n=16").get_json()["title"] == "Грабёж")
        check("unknown code is 404", client.get("/api/codex/toc?code=zz").status_code == 404)
        check("unknown article is 404", client.get("/api/codex/article?code=uk&n=999").status_code == 404)
        found = client.get("/api/codex/search?q=ст.%2016&limit=5").get_json()
        check("search endpoint finds by number", found["results"] and found["results"][0]["n"] == "16", str(found))
        check("bad limit does not crash", client.get("/api/codex/search?q=кража&limit=abc").status_code == 200)
        check("russian query is not rewritten", "query_ru" not in client.get("/api/codex/search?q=кража").get_json())
        old_translate = app._crime_text_to_russian
        app._crime_text_to_russian = lambda text: "ст. 16" if "Моддаи" in text else text
        try:
            tajik = client.get("/api/codex/search?q=Моддаи%2016").get_json()
        finally:
            app._crime_text_to_russian = old_translate
        check("tajik query is translated before search", tajik["query"] == "Моддаи 16" and tajik["query_ru"] == "ст. 16"
              and tajik["results"] and tajik["results"][0]["n"] == "16", str(tajik))
        check("translate alias exists", client.post("/api/codex/translate", json={}).status_code == 400)
        index_down = {"ready": False, "chunks": []}
        app.load_chat_rag_index = lambda force=False: index_down
        app._codex_state.update({"key": None, "catalog": None})
        check("not ready index is 503", client.get("/api/codex/catalog").status_code == 503)
    finally:
        app.load_chat_rag_index = old
        app._codex_state.update({"key": None, "catalog": None})


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
