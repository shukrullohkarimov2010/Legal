# -*- coding: utf-8 -*-
"""Язык документа: отчёт отвечает на языке договора.

    python tests/test_doc_lang.py

Модель подменена. Проверяется то, что код обязан гарантировать сам: как определяется таджикский
текст, что русский договор не получает ни одного лишнего вызова, что подписи отчёта на двух языках
согласованы по ключам и что сбой перевода не ломает отчёт.
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
import doc_lang  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


RU = ("Арендодатель передаёт Арендатору помещение площадью 80 кв.м для торговли. Арендная плата составляет 8000 "
      "сомони в месяц и вносится авансом. Срок аренды — пять лет. Все споры разрешаются в суде по месту нахождения "
      "Арендодателя. Арендатор производит капитальный ремонт за свой счёт без возмещения. ") * 3
TG = ("Иҷорадеҳ ба Иҷорагир бинои масоҳаташ 80 кв.м-ро барои тиҷорат месупорад. Пардохти иҷора дар як моҳ 8000 сомониро "
      "ташкил медиҳад ва пешакӣ пардохт карда мешавад. Мӯҳлати иҷора панҷ сол аст. Ҳамаи баҳсҳо дар суд аз рӯи ҷойи "
      "ҷойгиршавии Иҷорадеҳ ҳал карда мешаванд. Иҷорагир таъмири асосиро аз ҳисоби худ бе ҷуброн иҷро мекунад. ") * 3


def test_language_detection():
    check("tajik text is tajik", doc_lang.is_tajik_text(TG))
    check("russian text is not tajik", not doc_lang.is_tajik_text(RU))
    check("short text is never tajik", not doc_lang.is_tajik_text("Шартнома"))
    check("russian with a few tajik letters stays russian", not doc_lang.is_tajik_text(RU + " Рахимов Ҷамшед Қосимович"))
    check("empty is not tajik", not doc_lang.is_tajik_text(""))


def test_split_keeps_order_and_limit():
    text = "\n\n".join("Абзац %d. " % i + "слово " * 200 for i in range(12))
    parts = doc_lang.split_paragraphs(text, 1500)
    check("every part within the limit", all(len(p) <= 1500 for p in parts), str([len(p) for p in parts]))
    check("paragraph order kept", [p for p in parts if "Абзац 0." in p][0] == parts[0])
    glued = "\n\n".join(parts)
    check("nothing lost", all(("Абзац %d." % i) in glued for i in range(12)))
    check("one huge paragraph is cut", all(len(p) <= 800 for p in doc_lang.split_paragraphs("слово. " * 3000, 800)))


def test_translate_text_runs_parts_in_order_and_fails_loudly():
    calls = []

    def llm(prompt, num_predict, json_mode=False):
        calls.append(prompt)
        part = prompt.split("ТЕКСТ:\n", 1)[1].rsplit("\n\nПЕРЕВОД:", 1)[0]
        return "<" + part + ">"

    text = "\n\n".join("Пункт %d. " % i + "а" * 300 for i in range(8))
    out = doc_lang.translate_text(text, "tg", "ru", llm, limit=900)
    check("several calls for a long text", len(calls) > 1, str(len(calls)))
    check("order preserved", out.index("Пункт 0") < out.index("Пункт 7"))
    check("prompt names the direction", "с таджикского" in calls[0] and "русский" in calls[0])

    def broken(prompt, num_predict, json_mode=False):
        return ""

    try:
        doc_lang.translate_text(text, "tg", "ru", broken, limit=900)
        check("empty translation raises", False)
    except RuntimeError:
        check("empty translation raises", True)


def test_translate_items_falls_back_to_the_original():
    items = ["Риск один", "", "Риск два"]

    def good(prompt, num_predict, json_mode=False):
        return json.dumps({"items": [{"i": 0, "text": "Хавфи як"}, {"i": 2, "text": "Хавфи ду"}]}, ensure_ascii=False)

    check("translated in place", doc_lang.translate_items(items, "ru", "tg", good) == ["Хавфи як", "", "Хавфи ду"])

    def garbage(prompt, num_predict, json_mode=False):
        return "это не json"

    check("garbage keeps the originals", doc_lang.translate_items(items, "ru", "tg", garbage) == items)

    def partial(prompt, num_predict, json_mode=False):
        return json.dumps({"items": [{"i": 0, "text": "Хавфи як"}]}, ensure_ascii=False)

    check("missing item keeps its original", doc_lang.translate_items(items, "ru", "tg", partial)[2] == "Риск два")
    check("no strings -> no model call", doc_lang.translate_items(["", " "], "ru", "tg", lambda *a, **k: 1 / 0) == ["", " "])


def test_report_labels_cover_the_same_keys():
    check("same label keys in both languages", set(doc_lang.REPORT_LABELS["ru"]) == set(doc_lang.REPORT_LABELS["tg"]),
          str(set(doc_lang.REPORT_LABELS["ru"]) ^ set(doc_lang.REPORT_LABELS["tg"])))
    check("same severity keys", set(doc_lang.REPORT_LABELS["ru"]["severity"]) == set(doc_lang.REPORT_LABELS["tg"]["severity"]))
    for key, value in doc_lang.REPORT_LABELS["tg"].items():
        if key in ("severity", "translated"):
            continue
        for placeholder in ("{source}", "{type}", "{confidence}", "{types}"):
            check("placeholder %s kept in %s" % (placeholder, key),
                  (placeholder in doc_lang.REPORT_LABELS["ru"][key]) == (placeholder in value))


METRICS = {
    "score": 49, "level": "Средний", "high": 1, "medium": 1, "low": 0, "verdict": "⚠️ Требует правок",
    "findings": [{"title": "Одностороннее повышение платы", "severity": "high", "point": "2",
                  "fragment": "Арендодатель вправе повышать плату", "norm": "Гражданский кодекс Республики Таджикистан, Статья 680",
                  "why": "Неопределённость платежей", "fix": "Установить порядок пересмотра", "norm_text": "Статья 680. Арендная плата ..."}],
    "checklist_findings": [{"title": "Отсутствует условие: форс-мажор", "severity": "low", "fix": "Добавить раздел"}],
}
TYPE_INFO = {"type": "аренда", "confidence": 1.0, "types_with_scores": [{"type": "аренда", "score": 1.0}, {"type": "подряд", "score": 0.3}]}


def fake_translator(prompt, num_predict, json_mode=False):
    payload = json.loads(prompt.split("Строки: ", 1)[1].split("\n\nОтвет", 1)[0])
    return json.dumps({"items": [{"i": x["i"], "text": "TG[" + x["text"] + "]"} for x in payload]}, ensure_ascii=False)


def test_localized_report_has_tajik_labels_and_translated_texts():
    metrics, info = doc_lang.localize_report_inputs(METRICS, TYPE_INFO, fake_translator)
    check("input not mutated", METRICS["findings"][0]["title"] == "Одностороннее повышение платы")
    check("title translated", metrics["findings"][0]["title"].startswith("TG["))
    check("quote has no double guillemets", not metrics["findings"][0]["fragment"].startswith("«"))
    check("statute text stays russian", metrics["findings"][0]["norm_text"] == "Статья 680. Арендная плата ...")
    check("point number untouched", metrics["findings"][0]["point"] == "2")
    check("verdict from the fixed table", metrics["verdict"] == "⚠️ Таҳрир лозим аст")
    check("level from the fixed table", metrics["level"] == "Миёна")
    check("type translated", info["type"].startswith("TG["))

    report = app.build_grounded_contract_report(metrics, contract_type_info=info, lang="tg")
    for label in ("Таҳлили санҷидашудаи шартнома", "Хавфҳои ошкоршуда", "Дараҷа: **Баланд**", "Иқтибос аз шартнома",
                  "Рӯйхати шартҳои ҳатмӣ", "Хулоса", "Холи хавф: **49/100**", "Эзоҳ: шартнома барои таҳлил ба забони русӣ"):
        check("report has: " + label, label in report, report[:200])
    check("no russian labels left", "Выявленные риски" not in report and "Цитата из договора" not in report)
    check("statute text kept verbatim", "Статья 680. Арендная плата ..." in report)


def test_russian_report_is_unchanged():
    report = app.build_grounded_contract_report(METRICS, contract_type_info=TYPE_INFO)
    for label in ("## Проверенный RAG-анализ договора", "### Выявленные риски", "- Уровень: **Высокий**",
                  "- Цитата из договора: «Арендодатель вправе повышать плату»", "**Текст нормы из подключённого RAG:**",
                  "### Чек-лист обязательных условий", "(уровень значимости: Низкий)", "### Резюме", "- Балл риска: **49/100**"):
        check("russian report keeps: " + label, label in report)
    check("no translation note in russian", "Эзоҳ" not in report)


def test_compare_gets_a_tajik_rule_only_for_tajik_documents():
    seen = {}

    def fake_once(model, prompt, num_predict, **kwargs):
        seen["prompt"], seen["kwargs"] = prompt, kwargs
        return json.dumps({"summary": "x", "compatibilityScore": 90, "findings": [], "recommendations": []})

    old = app.llm_once
    app.llm_once = fake_once
    try:
        client = app.app.test_client()
        client.post("/api/contract/compare", json={"textA": TG, "textB": RU})
        tajik_prompt, tajik_kwargs = seen["prompt"], seen["kwargs"]
        client.post("/api/contract/compare", json={"textA": RU, "textB": RU})
        russian_prompt, russian_kwargs = seen["prompt"], seen["kwargs"]
    finally:
        app.llm_once = old
    check("tajik rule added", "ЯЗЫК ОТВЕТА: ТАДЖИКСКИЙ" in tajik_prompt)
    check("tajik uses the stronger model", tajik_kwargs.get("openai_model") == app.DRAFT_GPT_MODEL)
    check("russian untouched", "ЯЗЫК ОТВЕТА: ТАДЖИКСКИЙ" not in russian_prompt and "openai_model" not in russian_kwargs)


def test_crime_answer_language_follows_the_description():
    check("tajik description", app._crime_source_lang({"description": "Касе аз ҷайбам телефонамро дуздид"}) == "tg")
    check("russian description", app._crime_source_lang({"description": "У меня украли телефон из кармана"}) == "ru")
    check("empty description", app._crime_source_lang({}) == "ru")
    check("tajik note", "RAG тасдиқ накардааст" in app._crime_unverified_note(["999"], "tg"))
    check("russian note", "Не подтверждено RAG" in app._crime_unverified_note(["999"]))
    check("no note without unverified", app._crime_unverified_note([], "tg") == "")

    old = app._analysis_llm
    app._analysis_llm = lambda prompt, n, json_mode=False: ""
    try:
        check("failed translation returns the original", app._crime_translate_answer("1. КВАЛИФИКАЦИЯ\n   • ст. 244") == "1. КВАЛИФИКАЦИЯ\n   • ст. 244")
    finally:
        app._analysis_llm = old


def test_translation_label_artifacts_are_removed():
    check("leading and trailing label stripped",
          doc_lang.clean_translation("ПЕРЕВОД: Салом\n\nҷавоб\n\nПЕРЕВОД:") == "Салом\n\nҷавоб")
    check("extra blank lines collapsed", doc_lang.clean_translation("а\n\n\n\nб") == "а\n\nб")
    check("normal text untouched", doc_lang.clean_translation("Моддаи 214") == "Моддаи 214")


def main():
    tests = [v for k, v in sorted(globals().items()) if k.startswith("test_") and callable(v)]
    for test in tests:
        try:
            test()
        except Exception:  # noqa: BLE001
            FAILS.append("%s: исключение\n%s" % (test.__name__, traceback.format_exc()))
    if FAILS:
        print("FAIL: %d из %d проверок" % (len(FAILS), CHECKS))
        for entry in FAILS:
            print("  -", entry)
        sys.exit(1)
    print("OK: %d проверок пройдено" % CHECKS)


if __name__ == "__main__":
    main()
