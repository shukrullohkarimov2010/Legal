# -*- coding: utf-8 -*-
"""Точность чата: названия статей в поиске, привязка к кодексу и язык для человека.

    python tests/test_chat_accuracy.py

Модель подменена. Проверяется то, что код обязан гарантировать сам: запросы
«как в кодексе» доходят до поиска, статьи чужого кодекса не вытесняют нужный, а
служебные слова промпта не попадают к пользователю.
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
import chat_modes  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def item(source, article):
    return {"source": source, "article": article, "title": "Статья %s" % article, "text": "x"}


def test_own_code_goes_first():
    family, civil = "семейный кодекс.docx", "1918_ru_1.docx"
    results = [item(civil, "326"), item(family, "171"), item(civil, "329"), item(family, "38")]
    out = app._prefer_domain_codes(results, ["При разводе как делится имущество, нажитое в браке?",
                                             "раздел имущества брак"], 6)
    check("family code first", [r["article"] for r in out[:2]] == ["171", "38"], str([r["article"] for r in out]))
    check("foreign code kept after", [r["article"] for r in out[2:]] == ["326", "329"])


def test_cross_code_dispute_keeps_both():
    labour, civil = "трудовой кодекс.docx", "1918_ru_1.docx"
    results = [item(civil, "381"), item(labour, "45"), item(civil, "214")]
    out = app._prefer_domain_codes(results, ["Уволили без предупреждения, хочу взыскать неустойку по договору"], 6)
    check("no result lost", len(out) == 3)


def test_no_domain_leaves_order_alone():
    results = [item("1918_ru_1.docx", "10"), item("налоговый кодекс.docx", "20")]
    out = app._prefer_domain_codes(results, ["что-то совсем общее"], 6)
    check("order untouched without a domain", [r["article"] for r in out] == ["10", "20"])


def test_limit_applies():
    results = [item("семейный кодекс.docx", str(i)) for i in range(10)]
    check("limit respected", len(app._prefer_domain_codes(results, ["алименты развод брак"], 4)) == 4)


def test_understand_returns_code_style_queries():
    reply = json.dumps({"topic": "алименты", "gap": "none", "ru": "Муж не платит алименты", "search": "алименты взыскание",
                        "queries": ["Размер алиментов на несовершеннолетних детей", "Взыскание алиментов в судебном порядке",
                                    "Размер алиментов на несовершеннолетних детей", "Alimony size", "Салом ӣ"],
                        "domain": "Семейное"}, ensure_ascii=False)
    old = app.llm_once
    app.llm_once = lambda *a, **k: reply
    try:
        out = app._understand_question("m", "Муж не платит алименты", "", "ru", [], allow_clarify=False)
    finally:
        app.llm_once = old
    check("two clean russian queries", out["queries"] == ["Размер алиментов на несовершеннолетних детей",
                                                         "Взыскание алиментов в судебном порядке"], str(out["queries"]))
    check("domain lower-cased", out["domain"] == "семейное")


def test_humanize_replaces_internal_words():
    cases = {
        "Точная ссылка не подтверждена RAG": "Точная статья в нашей базе законов не найдена.",
        "в подборке норм этого нет — проверьте": "в нашей базе законов этого нет — проверьте",
        "В приведённых фрагментах нет нормы": "В найденных нормах нет нормы",
        "по фрагментам ниже отсутствуют": "по найденным нормам отсутствуют",
    }
    for source, expected in cases.items():
        got = chat_modes.humanize(source)
        check("humanize: " + source[:30], got == expected, repr(got))
        check("idempotent: " + source[:30], chat_modes.humanize(got) == got)
    check("clean text untouched", chat_modes.humanize("Статья 214 устанавливает три года.") == "Статья 214 устанавливает три года.")


def test_prompts_speak_human_and_pick_by_subject():
    for role in ("lawyer", "advocate"):
        for depth in ("fast", "deep"):
            block = chat_modes.format_block(role, depth)
            check("subject check in %s/%s" % (role, depth), "ПРИМЕНИМОСТЬ НОРМ" in block)
            check("human language rule in %s/%s" % (role, depth), "ЯЗЫК ДЛЯ ЧЕЛОВЕКА" in block)
            check("no 'подборке' in %s/%s" % (role, depth), "в подборке норм" not in block)
    check("todo block is conditional in fast mode", "только если вопрос о" in chat_modes.format_block("lawyer", "fast"))


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
