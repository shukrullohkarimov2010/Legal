# -*- coding: utf-8 -*-
"""Тесты режимов чата: роль, глубина, распознавание документов.

    python tests/test_chat_modes.py

Модуль chat_modes не обращается ни к модели, ни к RAG, поэтому тесты идут
без сервера и без ключей.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import chat_modes as cm  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def _doc(message, translated="", prev=""):
    return cm.detect_document(message, translated, prev)


def test_normalize_and_profiles():
    check("role default", cm.normalize_role(None) == "lawyer")
    check("role advocate", cm.normalize_role(" Advocate ") == "advocate")
    check("role junk", cm.normalize_role("судья") == "lawyer")
    check("depth default", cm.normalize_depth("") == "fast")
    check("depth deep", cm.normalize_depth("DEEP") == "deep")

    fast = cm.depth_profile("fast", 4, 5200, 420)
    check("fast keeps env top_k", fast.top_k == 4)
    check("fast keeps env chars", fast.max_chars == 5200)
    check("fast keeps env tokens", fast.max_tokens == 420)
    check("fast no subqueries", fast.subqueries is False)
    check("fast doc room", fast.doc_tokens >= 2600)

    deep = cm.depth_profile("deep", 4, 5200, 420)
    check("deep wider search", deep.top_k >= 10 and deep.top_k > fast.top_k)
    check("deep more context", deep.max_chars > fast.max_chars and deep.max_chars >= 14000)
    check("deep longer answer", deep.max_tokens >= 2600 and deep.max_tokens > fast.max_tokens)
    check("deep longer doc", deep.doc_tokens > fast.doc_tokens)
    check("deep subqueries", deep.subqueries is True)
    check("deep remembers more", deep.history_turns > fast.history_turns
          and deep.history_chars > fast.history_chars)

    # Большой .env не должен быть урезан режимом «глубина».
    big = cm.depth_profile("deep", 20, 40000, 3000)
    check("deep never below env", big.top_k >= 20 and big.max_chars >= 40000 and big.max_tokens >= 3000)


def test_prompts():
    lawyer = cm.system_line("lawyer")
    advocate = cm.system_line("advocate")
    check("lawyer line", "юрист" in lawyer and "Таджикистан" in lawyer)
    check("advocate line", "адвокат" in advocate and "интерес" in advocate)
    check("advocate ethics", "подделыва" in advocate)
    check("no RF in both", "не упоминайте РФ" in lawyer and "не упоминайте РФ" in advocate)
    check("tone applied", "предельно сжато" in cm.system_line("lawyer", "concise"))
    check("professional has no tone", cm.system_line("lawyer", "professional") == lawyer)

    blocks = {(r, d): cm.format_block(r, d) for r in cm.ROLES for d in cm.DEPTHS}
    check("four distinct formats", len(set(blocks.values())) == 4)
    for key, block in blocks.items():
        check("no-invention rule %s" % (key,), "не выдумывай" in block)
    check("advocate deep has counterarguments", "Доводы другой стороны" in blocks[("advocate", "deep")])
    check("lawyer deep has analysis", "Анализ" in blocks[("lawyer", "deep")])
    # быстрый формат стал полноценной консультацией: он по-прежнему короче глубокого по числу разделов
    check("fast has fewer sections", blocks[("lawyer", "fast")].count("**") < blocks[("lawyer", "deep")].count("**"))
    check("length rule differs", cm.length_rule("fast") != cm.length_rule("deep"))
    check("disclaimer mentions lawyer", "юриста или адвоката" in cm.DISCLAIMER)


def test_detect_document_positive():
    cases = [
        ("Составь договор аренды квартиры на 11 месяцев", "contract", "аренды"),
        ("составь мне договор купли-продажи автомобиля", "contract", "купли-продажи"),
        ("мне нужен договор подряда на ремонт", "contract", "подряда"),
        ("Подготовь договор оказания услуг", "contract", "оказания услуг"),
        ("Напиши исковое заявление о взыскании долга по расписке", "claim", ""),
        ("напиши заявление на увольнение по собственному желанию", "statement", ""),
        ("нужна доверенность на продажу автомобиля", "poa", ""),
        ("подготовь претензию поставщику о задержке товара", "claim_letter", ""),
        ("напиши жалобу на решение суда", "complaint", ""),
        ("составь расписку о получении денег", "receipt", ""),
        ("дай образец договора займа", "contract", "займа"),
    ]
    for text, kind, subtype in cases:
        doc = _doc(text)
        check("doc: " + text, doc is not None and doc.kind == kind and doc.subtype == subtype
              and doc.edit is False, repr(doc))

    # Перевод с таджикского может потерять слово в оригинале, но не в переводе.
    doc = _doc("Шартномаи иҷораро нависед", "Напишите договор аренды")
    check("tg via translation", doc is not None and doc.kind == "contract" and doc.subtype == "аренды",
          repr(doc))
    doc = _doc("Ба ман шартномаи хариду фурӯш тартиб диҳед")
    check("tg original only", doc is not None and doc.kind == "contract", repr(doc))


def test_detect_document_negative():
    for text in [
        "Какой срок исковой давности по договору займа?",
        "нужен ли договор для аренды гаража?",
        "проверь договор на риски",
        "составь анализ договора поставки",
        "как расторгнуть договор аренды",
        "Какие пункты должны быть в договоре аренды?",
        "сколько дней отпуска положено работнику",
        "можно ли оспорить договор дарения",
        "привет",
    ]:
        check("not a doc: " + text, _doc(text) is None, repr(_doc(text)))
    check("empty", _doc("") is None)
    check("pasted text is analysis", _doc("составь договор " + "текст договора " * 200) is None)


def test_edit_document():
    body = "**ДОГОВОР АРЕНДЫ КВАРТИРЫ**\n" + "1. Предмет договора. Арендодатель передаёт квартиру. " * 20
    history = [
        {"role": "user", "content": "составь договор аренды"},
        {"role": "ai", "content": body},
    ]
    prev = cm.previous_document(history)
    check("previous doc found", prev.startswith("**ДОГОВОР АРЕНДЫ"))

    doc = _doc("добавь пункт о неустойке за просрочку платежа", prev=prev)
    check("edit detected", doc is not None and doc.edit and doc.kind == "contract", repr(doc))
    check("edit keeps subtype", doc is not None and doc.subtype == "аренды", repr(doc))

    check("question is not an edit",
          _doc("можно ли добавить пункт о неустойке?", prev=prev) is None)
    check("no doc no edit", _doc("добавь пункт о неустойке") is None)

    short = [{"role": "ai", "content": "ДОГОВОР"}]
    check("short reply is not a doc", cm.previous_document(short) == "")
    answered = history + [{"role": "user", "content": "спасибо"},
                          {"role": "ai", "content": "Пожалуйста, обращайтесь."}]
    check("only the last reply counts", cm.previous_document(answered) == "")
    check("consultation is not a doc", cm.previous_document(
        [{"role": "ai", "content": "**Ответ:** срок исковой давности три года. " * 40}]) == "")
    long_doc = [{"role": "ai", "content": "ДОГОВОР\n" + "x" * 30000}]
    check("doc capped", len(cm.previous_document(long_doc)) == cm.PREVIOUS_DOC_MAX_CHARS)


def test_document_prompt_and_queries():
    doc = _doc("составь договор аренды квартиры")
    queries = cm.document_queries(doc)
    check("subtype query first", queries and "аренд" in queries[0], repr(queries))
    check("generic queries added", any("форма договора" in q for q in queries))
    check("no doc no queries", cm.document_queries(None) == [])
    claim = _doc("напиши исковое заявление о взыскании долга")
    check("claim queries", any("исков" in q for q in cm.document_queries(claim)))

    text = cm.document_block(doc, "lawyer")
    check("placeholders rule", "квадратных скобках" in text and "Ничего не выдумывай" in text)
    check("what to fill section", "Что заполнить и проверить" in text)
    check("contract structure", "Предмет" in text and "Ответственность" in text)
    check("balanced for lawyer", "баланс интересов" in text)
    check("advocate side", "как адвокат" in cm.document_block(doc, "advocate"))
    check("citations only verified", "ПРОВЕРЕННАЯ ССЫЛКА RAG" in text)

    edit = cm.DocRequest("contract", "договор", "аренды", edit=True)
    edit_text = cm.document_block(edit, "lawyer", "ТЕКУЩИЙ-ТЕКСТ-123")
    check("edit prompt has document", "ТЕКУЩИЙ-ТЕКСТ-123" in edit_text)
    check("edit asks full text", "ПОЛНЫЙ исправленный текст" in edit_text)
    check("new doc has no previous text", "ТЕКУЩИЙ ТЕКСТ ДОКУМЕНТА" not in text)

    for kind in cm._DOC_STRUCTURE:
        block = cm.document_block(cm.DocRequest(kind, kind, "", False), "lawyer")
        check("structure for " + kind, "Структура" in block)


def test_subqueries():
    check("prompt has the situation", "уволили без расчёта" in cm.subquery_prompt("уволили без расчёта"))
    data = {"queries": [
        "основания увольнения работника",
        "расчёт при увольнении",
        "срок обращения в суд по трудовому спору",
        "защита чести и достоинства",
        "пятый запрос лишний",
    ]}
    result = cm.parse_subqueries(data, base="меня уволили")
    check("limit four", len(result) == 4, repr(result))
    check("the prompt asks for article-like titles", "как называется статья" in cm.subquery_prompt("x"))
    bad = cm.parse_subqueries({"queries": [
        "ҳуқуқи меҳнатӣ", "labour law", "", None, "расчёт при увольнении",
        "Расчёт при увольнении", "меня уволили",
    ]}, base="меня уволили")
    check("filters tajik, latin, dupes, base", bad == ["расчёт при увольнении"], repr(bad))
    check("junk input", cm.parse_subqueries(None) == [] and cm.parse_subqueries({"queries": "x"}) == [])


def test_prompts_forbid_abbreviations():
    check("rule text", "НЕ СОКРАЩАЙ" in cm.NO_ABBREVIATIONS and "ПРОВЕРЕННАЯ ССЫЛКА RAG" in cm.NO_ABBREVIATIONS)
    for role in cm.ROLES:
        for depth in cm.DEPTHS:
            block = cm.format_block(role, depth)
            check("rule in %s/%s" % (role, depth), "НЕ СОКРАЩАЙ" in block)
            check("no abbreviated example in %s/%s" % (role, depth), "(кодекс, ст" not in block and "«кодекс, ст" not in block)
    doc = cm.document_block(cm.DocRequest("contract", "договор", "", False), "lawyer")
    check("rule in documents", "НЕ СОКРАЩАЙ" in doc)
    check("document example is not abbreviated", "(ст. N ГК РТ)" not in doc and "Гражданского кодекса Республики Таджикистан" in doc)
    check("every code has a full name", set(cm.CODE_FULL_NAMES) == set(cm._CODE_NAMES))


def test_abbreviations_are_expanded_with_the_right_case():
    ex = cm.expand_abbreviations

    def is_(source, expected):
        got = ex(source)
        check("expand: %s" % source, got == expected, "%r != %r" % (got, expected))

    is_("Ответ: ГК РТ, ст. 214.", "Ответ: Гражданский кодекс Республики Таджикистан, статья 214.")
    is_("(ГК РТ, ст. 214)", "(Гражданский кодекс Республики Таджикистан, статья 214)")
    is_("Согласно ст. 81 СК РТ алименты.", "Согласно статье 81 Семейного кодекса Республики Таджикистан алименты.")
    is_("Это в ТК РТ и по ГК РТ.",
        "Это в Трудовом кодексе Республики Таджикистан и по Гражданскому кодексу Республики Таджикистан.")
    is_("в соответствии с ГК РТ", "в соответствии с Гражданским кодексом Республики Таджикистан")
    is_("См. ч. 1 ст. 327 ГПК РТ и п. 3 ч. 2 ст. 5 УК РТ.",
        "См. часть 1 статьи 327 Гражданского процессуального кодекса Республики Таджикистан и пункт 3 части 2 "
        "статьи 5 Уголовного кодекса Республики Таджикистан.")
    is_("Требования ст. 675 и ст. 718 ГК РТ.", "Требования статьи 675 и статьи 718 Гражданского кодекса Республики Таджикистан.")
    is_("Ст. 5 говорит иное.", "Статья 5 говорит иное.")
    is_("статья 129 КАТ РТ", "статья 129 Кодекса автомобильного транспорта Республики Таджикистан")
    is_("статья 5 ПКоАП РТ", "статья 5 Процессуального кодекса Республики Таджикистан об административных правонарушениях")
    is_("статья 7 КЗ РТ", "статья 7 Кодекса Республики Таджикистан о здравоохранении")
    is_("Ответ по праву РТ. Живёт в РТ. РТ — страна.",
        "Ответ по праву Республики Таджикистан. Живёт в Республике Таджикистан. Республика Таджикистан — страна.")
    # Чужое не трогаем.
    for untouched in ("На стр. 5 и ФОРТ, ТРТ.", "Статья 5 Гражданского кодекса Республики Таджикистан.", "", "ст. без номера"):
        check("untouched: %r" % untouched, ex(untouched) == untouched, ex(untouched))
    for text in ("ГК РТ, ст. 214, ст. 215.", "по праву РТ и ч. 1 ст. 5 СК РТ", "Нормы ст. 675, ст. 718 ГК РТ"):
        once = ex(text)
        check("idempotent: %s" % text, ex(once) == once, once)
    check("all codes covered", set(cm._CODE_PARTS) == set(cm._CODE_NAMES))
    for short in cm._CODE_PARTS:
        check("nominative equals the full name: %s" % short, cm.decline_code(short) == cm.CODE_FULL_NAMES[short],
              "%r != %r" % (cm.decline_code(short), cm.CODE_FULL_NAMES[short]))
        for case in ("gen", "dat", "prep", "instr"):
            check("%s/%s keeps the country" % (short, case), "Таджикистан" in cm.decline_code(short, case))
    check("no abbreviation left after expansion",
          not cm._CODE_ABBR_RE.search(ex(" ".join(cm._CODE_NAMES))), ex(" ".join(cm._CODE_NAMES))[:120])

def test_stage_events():
    check("ru label", cm.stage_event("search")["label"] == "Ищу нормы в кодексах РТ")
    check("stage key", cm.stage_event("search")["stage"] == "search")
    check("count is formatted", "7" in cm.stage_event("found", "ru", n=7)["label"])
    check("tajik label", cm.stage_event("write", "tg")["label"] == "Ҷавобро таҳия мекунам")
    check("tajik count", "7" in cm.stage_event("found", "tg", n=7)["label"])
    check("unknown language falls back to ru", cm.stage_event("write", "xx")["label"] == "Составляю ответ")
    for key, labels in cm.STAGES.items():
        check("both languages for " + key, labels.get("ru") and labels.get("tg"))
        check("no Serbian letters in tajik " + key, not any(ch in labels["tg"] for ch in "љњћђ"))


_RESULTS = [
    {"code_short": "ТК РТ", "article": "158", "text": "Статья 158. Сроки выплаты заработной платы"},
    {"code_short": "ТК РТ", "article": "39", "text": "Статья 39. Прекращение трудового договора"},
    {"code_short": "ГК РТ", "article": "214", "text": "Статья 214. Общий срок исковой давности устанавливается в три года."},
    {"code_short": "ГК РТ", "article": "39", "text": "Статья 39. Что-то из ГК"},
    {"code_short": "ГПК РТ", "article": "134", "text": "Статья 134. Форма и содержание искового заявления"},
    {"code_short": "КЗ РТ", "article": "58", "text": "Статья 58. Права пациента"},
]


def test_citations_belong_to_the_named_code():
    def bad(text):
        return cm.misattributed_citations(text, _RESULTS)

    check("full name ok", bad("Трудовой кодекс РТ, ст. 158: сроки выплаты") == [])
    check("abbreviation ok", bad("ТК РТ, ст. 158") == [])
    check("code after number ok", bad("(ст. 214 ГК РТ)") == [])
    check("wrong code flagged", len(bad("ГК РТ, ст. 158")) == 1, repr(bad("ГК РТ, ст. 158")))
    check("message names the right code", "Трудовой кодекс Республики Таджикистан" in bad("ГК РТ, ст. 158")[0])
    check("message uses no abbreviations", "ТК РТ" not in bad("ГК РТ, ст. 158")[0]
          and "ГК РТ" not in bad("ГК РТ, ст. 158")[0], bad("ГК РТ, ст. 158")[0])
    check("wrong code after number flagged", len(bad("ст. 214 ТК РТ")) == 1)
    check("list inherits the code", len(bad("ГК РТ, ст. 214, ст. 158")) == 1, repr(bad("ГК РТ, ст. 214, ст. 158")))
    check("new line resets the code", bad("ГК РТ, ст. 214\n- ст. 158 ТК РТ") == [], repr(bad("ГК РТ, ст. 214\n- ст. 158 ТК РТ")))
    check("same number in two codes is fine", bad("ГК РТ, ст. 39") == [] and bad("ТК РТ, ст. 39") == [])
    check("unknown article left to the number check", bad("ГК РТ, ст. 999") == [])
    check("no code named, nothing to say", bad("ст. 158") == [])
    check("procedural code is not the civil code",
          bad("Гражданский процессуальный кодекс РТ, ст. 134") == [], repr(bad("Гражданский процессуальный кодекс РТ, ст. 134")))
    check("civil code name",
          bad("Гражданский кодекс Республики Таджикистан, ст. 158") != [])
    check("health code by name",
          bad("Кодекс Республики Таджикистан о здравоохранении, ст. 58") == [])
    check("duplicates collapse", len(bad("ГК РТ, ст. 158. Ещё раз ГК РТ, ст. 158")) == 1)


def test_quotes_must_come_from_the_articles():
    fragments = [r["text"] for r in _RESULTS]

    def bad(text, *known):
        return cm.unverified_quotes(text, fragments, *known)

    good = "ГК РТ, ст. 214 — «Общий срок исковой давности устанавливается в три года»"
    check("exact quote ok", bad(good) == [])
    check("case and punctuation do not matter",
          bad("ГК РТ, ст. 214 — «общий срок исковой давности, устанавливается в три года.»") == [])
    check("invented quote flagged",
          bad("ГК РТ, ст. 214 — «срок давности составляет пять лет по всем требованиям»")
          == ["срок давности составляет пять лет по всем требованиям"])
    check("quote outside a citation line is ignored",
          bad("Работодатель сказал: «мы вам ничего не должны по закону»") == [])
    check("words of the user are not laws",
          bad("ТК РТ, ст. 158 — вы пишете «зарплата за три месяца не выплачена»",
              "Мне зарплата за три месяца не выплачена") == [])
    check("ellipsis splits the quote", bad("ГК РТ, ст. 214 — «Общий срок … в три года»") == [])
    check("wrong part after ellipsis flagged",
          len(bad("ГК РТ, ст. 214 — «Общий срок … в пять лет»")) == 1)
    check("short quotes ignored", bad("ГК РТ, ст. 214 — термин «исковая давность» здесь") == [])
    check("yo normalisation", cm.unverified_quotes("ст. 5 — «всё в порядке здесь»", ["Всё в порядке здесь"]) == [])
    check("nothing to check", bad("Просто текст без цитат и ссылок") == [])


def test_deep_prompts_demand_quotes_and_applicability():
    for role in cm.ROLES:
        block = cm.format_block(role, "deep")
        check("quote rule in deep " + role, "дословную цитату" in block)
        check("applicability rule in deep " + role, "ПРИМЕНИМОСТЬ НОРМ" in block)
        fast = cm.format_block(role, "fast")
        check("fast does not demand quotes " + role, "дословную цитату" not in fast)
    doc = cm.document_block(cm.DocRequest("contract", "договор", "", False), "lawyer")
    check("applicability in documents", "ПРИМЕНИМОСТЬ НОРМ" in doc)

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
