# -*- coding: utf-8 -*-
"""Тесты диалога перед составлением документа: какой договор и с кем.

    python tests/test_doc_dialog.py

Модель и поиск не нужны: планировщик чистый, состояние диалога передаётся
явно, как его передаёт страница.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import doc_dialog as dd  # noqa: E402
import chat_modes as cm  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def plan(message, state=None, **kw):
    return dd.plan_document(message, state=state, **kw)


def asked(p):
    return p is not None and p.ask is not None


def drafted(p):
    return p is not None and p.doc is not None and p.ask is None


# -------------------------------------------------------------- первое сообщение

def test_bare_contract_asks_which_contract():
    p = plan("Составь договор")
    check("asks", asked(p))
    check("about the type", p.state["awaiting"] == "type" and "Какой договор" in p.ask["question"])
    check("eight types offered", len(p.ask["options"]) == 8, repr(p.ask["options"]))
    for wanted in ("Купли-продажи", "Аренды", "Поставки", "Подряда", "Оказания услуг", "Займа", "Дарения"):
        check("offers " + wanted, wanted in p.ask["options"])
    check("history text is Russian and lists the options",
          p.ask["source"].startswith("Какой договор вам нужен?") and "- Аренды" in p.ask["source"])
    check("state remembers the request", p.state["request"] == "Составь договор" and p.state["kind"] == "contract")
    check("state keeps the shown options for matching the click", len(p.state["options"]) == 8)


def test_contract_type_known_asks_only_about_the_party():
    for text in ("Составь договор аренды", "Составь договор аренды квартиры", "составь договор купли-продажи автомобиля",
                 "Составь Договор Аренды", "составь договор оказания услуг для меня", "составь договор на ремонт квартиры"):
        p = plan(text)
        check("party question: " + text, asked(p) and p.state["awaiting"] == "party"
              and "С кем" in p.ask["question"], repr(p and p.ask))


def test_contract_with_type_and_party_is_drafted_at_once():
    for text, subtype in (("Составь договор аренды квартиры с Ахмедовым", "аренды"),
                          ("составь договор аренды квартиры с ООО Мир на 11 месяцев", "аренды"),
                          ("составь договор купли-продажи между Ахмедовым и Каримовым", "купли-продажи"),
                          ("составь договор поставки, мой поставщик ООО Мир", "поставки"),
                          ("составь договор займа с моим братом", "займа"),
                          ("Составь договор аренды Ахмедов Карим Иванович", "аренды")):
        p = plan(text)
        check("drafted: " + text, drafted(p) and p.doc.subtype == subtype, repr(p))
    check("request text is kept", "Ахмедовым" in plan("Составь договор аренды квартиры с Ахмедовым").request_text)


def test_party_without_type_still_asks_the_type_then_stops():
    p = plan("составь договор с Каримом")
    check("asks the type", asked(p) and p.state["awaiting"] == "type")
    p2 = plan("Поставки", p.state)
    check("the party was already named: drafted", drafted(p2) and p2.doc.subtype == "поставки", repr(p2))


def test_custom_contract_type_in_the_request():
    p = plan("Составь договор совместной деятельности")
    check("own words are accepted as the type", asked(p) and p.state["awaiting"] == "party"
          and p.state["custom"] == "совместной деятельности", repr(p and p.state))
    p2 = plan("ООО Мир", p.state)
    check("title carries the custom type", drafted(p2) and p2.doc.subtype == "" and p2.doc.custom == "совместной деятельности")
    check("prompt names it", "совместной деятельности" in cm.document_block(p2.doc, "lawyer"))


def test_not_about_documents():
    for text in ("Какой срок исковой давности?", "привет", "Как расторгнуть договор аренды?", "проверь договор на риски",
                 "Сколько дней отпуска положено работнику"):
        check("not a document: " + text, plan(text) is None)


# ---------------------------------------------------------------- полный диалог

def test_full_contract_dialog_with_buttons():
    p1 = plan("Составь договор")
    p2 = plan("Купли-продажи", p1.state)
    check("second question is about the party", asked(p2) and p2.state["awaiting"] == "party")
    check("type stored", p2.state["subtype"] == "купли-продажи")
    p3 = plan("С организацией (юридическим лицом)", p2.state)
    check("drafted", drafted(p3))
    check("subtype", p3.doc.subtype == "купли-продажи")
    check("party described", "организация" in p3.doc.party and "заглушки" in p3.doc.party, p3.doc.party)
    check("request collects everything",
          "Вид договора: купли-продажи" in p3.request_text and "Вторая сторона" in p3.request_text, p3.request_text)
    check("prompt tells the model who the other side is", "Вторая сторона (контрагент)" in cm.document_block(p3.doc, "lawyer"))


def test_party_answered_in_own_words():
    p = plan("ООО Мир", plan("Аренды", plan("Составь договор").state).state)
    check("free text party", drafted(p) and p.doc.party == "ООО Мир", repr(p and p.doc))
    p = plan("с моим соседом Ахмедом Каримовым", plan("Аренды", plan("Составь договор").state).state)
    check("free sentence party", drafted(p) and "соседом" in p.doc.party)


def test_skipping_is_an_answer_and_never_loops():
    base = plan("Аренды", plan("Составь договор").state).state
    for answer in ("Стороны пока не определены", "не знаю", "потом", "Пока не знаю"):
        p = plan(answer, base)
        check("skip: " + answer, drafted(p) and p.doc.party == "", repr(p))
        check("skip is passed to the model", "оставь заглушками" in p.request_text or "заглушк" in p.request_text, p.request_text)
    # Каждый вопрос задаётся один раз, даже если состояние пришло «как есть».
    state = dict(base, awaiting="party", asked=["type", "party"])
    p = plan("ну", state)
    check("no second party question", drafted(p), repr(p))


def test_type_answered_in_own_words():
    start = plan("Составь договор").state
    p = plan("на ремонт", start)
    check("recognised by meaning", asked(p) and p.state["subtype"] == "подряда", repr(p and p.state))
    p = plan("аренда гаража", start)
    check("recognised: rent", asked(p) and p.state["subtype"] == "аренды")
    p = plan("договор мены", start)
    check("unknown type is kept in own words without duplicating the word contract",
          asked(p) and p.state["custom"] == "мены", repr(p and p.state))
    p = plan("аренды с Ахмедовым", start)
    check("type and party in one answer", drafted(p) and p.doc.subtype == "аренды", repr(p))


def test_answer_matching_is_done_by_button_text_not_by_translation():
    p1 = plan("Составь договор")
    labels = {o["text"]: o["key"] for o in p1.state["options"]}
    for text, key in labels.items():
        p = plan(text, p1.state)
        check("button %r -> %s" % (text, key), asked(p) and p.state["subtype"] == key, repr(p and p.state))


# ---------------------------------------------------- другие виды документов

def test_claim_dialog():
    p1 = plan("напиши исковое заявление")
    check("claim asks what it is about", asked(p1) and p1.state["awaiting"] == "topic" and "О чём иск" in p1.ask["question"])
    p2 = plan("Взыскание долга (займ, расписка)", p1.state)
    check("then asks who the defendant is", asked(p2) and p2.state["awaiting"] == "party" and "ответчик" in p2.ask["question"])
    p3 = plan("Физическое лицо", p2.state)
    check("drafted", drafted(p3) and p3.doc.kind == "claim" and "долг" in p3.doc.topic.lower(), repr(p3 and p3.doc))
    check("defendant described", "физическое лицо" in p3.doc.party)
    check("claim with topic and defendant is drafted at once",
          drafted(plan("напиши исковое заявление против Ахмедова о взыскании долга")))
    check("claim with a topic still asks the defendant",
          asked(plan("напиши исковое заявление о взыскании долга по расписке")))


def test_statement_and_complaint_need_only_the_topic():
    for text in ("напиши заявление на увольнение", "напиши жалобу на решение суда"):
        check("drafted at once: " + text, drafted(plan(text)))
    p = plan("составь заявление")
    check("bare statement asks what it is", asked(p) and p.state["awaiting"] == "topic")
    p2 = plan("О расторжении брака", p.state)
    check("no party question for a statement", drafted(p2) and p2.doc.kind == "statement", repr(p2))
    check("bare complaint asks", asked(plan("напиши жалобу")))


def test_power_of_attorney_receipt_and_claim_letter():
    p = plan("нужна доверенность")
    check("poa asks purpose", asked(p) and p.state["awaiting"] == "topic")
    p = plan("Получение денег в долг", plan("составь расписку").state)
    check("receipt asks the other side", asked(p) and "вторая сторона" in p.ask["question"].lower())
    check("receipt with everything is drafted",
          drafted(plan("составь расписку о получении 5000 сомони от Каримова")))
    check("poa with a representative is drafted",
          drafted(plan("нужна доверенность на продажу автомобиля на моего брата")))
    check("letter of claim to a supplier is drafted", drafted(plan("подготовь претензию поставщику о задержке товара")))
    check("letter of claim with no addressee asks", asked(plan("составь претензию о задержке доставки")))


# ------------------------------------------------------ защита от зависания

def test_a_new_question_or_request_leaves_the_dialog():
    awaiting_party = plan("Аренды", plan("Составь договор").state).state
    check("a question about the law leaves the dialog", plan("Какой срок исковой давности?", awaiting_party) is None)
    p = plan("Составь договор аренды с Каримом", awaiting_party)
    check("a complete new request replaces the old state", drafted(p) and p.doc.subtype == "аренды")
    p = plan("напиши заявление на увольнение", plan("Составь договор").state)
    check("another document starts fresh", drafted(p) and p.doc.kind == "statement", repr(p))
    check("a huge reply is not an answer", plan("слово " * 100, awaiting_party) is None)


def test_broken_or_hostile_state_is_ignored():
    for bad in (None, "строка", 5, [], {}, {"v": 2, "kind": "contract"}, {"v": 1, "kind": "evil"},
                {"v": 1, "kind": "contract", "awaiting": "sql"}, {"v": 1, "kind": "contract", "options": "x"}):
        p = plan("Составь договор", bad)
        check("fresh start with %r" % (bad,), asked(p) and p.state["awaiting"] == "type", repr(p))
        check("no plan for a plain question with %r" % (bad,), plan("Какой срок давности?", bad) is None)
    state = {"v": 1, "kind": "contract", "awaiting": "party", "subtype": "аренды" * 100, "party": "x" * 5000,
             "request": "y" * 5000, "options": [{"text": "z" * 999, "key": "k" * 999}] * 50, "asked": ["type"] * 9}
    clean = dd.sanitize_state(state)
    check("sanitised", clean is not None and len(clean["party"]) <= dd.MAX_TEXT and len(clean["request"]) <= dd.MAX_TEXT
          and len(clean["options"]) <= dd.MAX_OPTIONS and len(clean["subtype"]) <= 40, repr(clean and {k: len(str(v)) for k, v in clean.items()}))
    check("unknown keys are dropped", "evil" not in dd.sanitize_state(dict(state, evil="x")))


def test_editing_a_finished_document_never_asks():
    body = "**ДОГОВОР АРЕНДЫ КВАРТИРЫ**\n" + "1. Арендодатель передаёт квартиру. " * 30
    p = dd.plan_document("добавь пункт о неустойке за просрочку платежа", prev_doc=body)
    check("edit goes straight through", drafted(p) and p.doc.edit)
    stale = plan("Аренды", plan("Составь договор").state).state
    p = dd.plan_document("добавь пункт о неустойке", prev_doc=body, state=stale)
    check("stale state does not turn an edit into a question", p is not None and (p.doc is None or p.doc.edit) or p is None)


# ------------------------------------------------------------- по-таджикски

def test_tajik_cards_use_tajik_labels_and_still_match_the_click():
    p1 = dd.plan_document("Шартнома тартиб диҳед", lang="tg")
    check("asks about the type in Tajik", asked(p1) and p1.ask["question"] == "Кадом шартнома ба шумо лозим аст?", repr(p1 and p1.ask))
    check("Tajik labels", "Иҷора" in p1.ask["options"] and "Хариду фурӯш" in p1.ask["options"], repr(p1.ask["options"]))
    check("history stays Russian", p1.ask["source"].startswith("Какой договор вам нужен?"))
    p2 = dd.plan_document("Иҷора", state=p1.state, lang="tg")
    check("click on a Tajik label is understood", asked(p2) and p2.state["subtype"] == "аренды", repr(p2 and p2.state))
    check("party question in Tajik", p2.ask["question"] == "Шартномаро бо кӣ мебандед?")
    check("party options in Tajik", p2.ask["options"][0] == "Бо шахси воқеӣ", repr(p2.ask["options"]))
    p3 = dd.plan_document("Бо ташкилот (шахси ҳуқуқӣ)", state=p2.state, lang="tg")
    check("drafted with the organisation", drafted(p3) and "организация" in p3.doc.party, repr(p3 and p3.doc))
    p4 = dd.plan_document("Тарафҳо ҳоло муайян нашудаанд", state=p2.state, lang="tg")
    check("skip in Tajik", drafted(p4) and p4.doc.party == "")


def test_tajik_topic_cards_are_translated_with_a_fallback():
    calls = []

    def fake(texts):
        calls.append(texts)
        return ["тг:" + t for t in texts]

    p = dd.plan_document("Даъвои судӣ тартиб диҳед", translated="Составь исковое заявление", lang="tg", translate=fake)
    check("translated question and options", asked(p) and p.ask["question"].startswith("тг:") and calls, repr(p and p.ask))
    click = p.ask["options"][0]
    p2 = dd.plan_document(click, state=p.state, lang="tg", translate=fake)
    check("the click maps back to the Russian topic", asked(p2) and "Взыскание долга" in p2.state["topic"], repr(p2 and p2.state))
    p = dd.plan_document("Даъвои судӣ тартиб диҳед", translated="Составь исковое заявление", lang="tg", translate=lambda t: None)
    check("no translator: Russian card, not a crash", asked(p) and p.ask["question"] == "О чём иск?")

    def broken(texts):
        raise RuntimeError("model is down")

    p = dd.plan_document("Даъвои судӣ тартиб диҳед", translated="Составь исковое заявление", lang="tg", translate=broken)
    check("translator failure is survived", asked(p) and p.ask["question"] == "О чём иск?")


# ----------------------------------------------------- вспомогательные признаки

def test_helpers():
    for text in ("договор аренды с Ахмедовым", "договор с ООО «Мир»", "между Ахмедовым и Каримовым", "мой поставщик",
                 "для моего брата", "против Каримова", "от Каримова", "с индивидуальным предпринимателем"):
        check("counterparty: " + text, dd.has_counterparty("составь " + text))
    for text in ("договор аренды", "Договор Аренды", "договор оказания услуг", "договор для меня", "договор на год"):
        check("no counterparty: " + text, not dd.has_counterparty("составь " + text), text)
    check("topic present", dd.has_topic("напиши иск о взыскании долга") and dd.has_topic("заявление на увольнение"))
    check("topic absent", not dd.has_topic("составь иск") and not dd.has_topic("напиши мне жалобу пожалуйста срочно"))
    check("custom type", dd.contract_custom("составь договор совместной деятельности") == "совместной деятельности")
    check("no custom type before the party", dd.contract_custom("составь договор с Ахмедовым") == "")
    check("no custom type in a bare request", dd.contract_custom("составь договор") == "")
    check("custom type stops at the party", dd.contract_custom("составь договор мены с Ахмедовым") == "мены")


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
