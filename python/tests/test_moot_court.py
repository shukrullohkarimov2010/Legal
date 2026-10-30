# -*- coding: utf-8 -*-
"""Тесты симулятора суда: дела, разбор ответа модели и подсчёт баллов.

    python tests/test_moot_court.py

Модуль moot_court не обращается ни к модели, ни к RAG, поэтому тесты идут
без сервера и без ключей.
"""

from __future__ import annotations

import os
import sys
import traceback

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import moot_court as mc  # noqa: E402

FAILS = []
CHECKS = 0


def check(name, condition, detail=""):
    global CHECKS
    CHECKS += 1
    if not condition:
        FAILS.append("%s %s" % (name, detail))


def test_cases():
    check("three cases", set(mc.case_ids()) == {"theft", "debt", "dismissal"})
    for cid in mc.case_ids():
        case = mc.get_case(cid)
        check("case has all fields " + cid, all(case.get(f) for f in mc.CARD_FIELDS + ("kind", "query")))
    check("unknown case", mc.get_case("nope") is None and mc.public_case("nope") is None)
    card = mc.public_case("theft", {"title": "Сарлавҳа", "facts": ""})
    check("translated field used", card["title"] == "Сарлавҳа")
    check("empty translation falls back", card["facts"] == mc.CASES["theft"]["facts"])
    check("query not exposed", "query" not in card)
    check("genders exposed for the scene", card["opponent_gender"] in ("m", "f") and card["witness_gender"] in ("m", "f"))


def test_sanitize_history():
    raw = [{"speaker": "user", "text": "  довод  "}, {"speaker": "hacker", "text": "x"}, "bad", {"speaker": "judge", "text": ""},
           {"speaker": "judge", "text": "а" * 5000}]
    clean = mc.sanitize_history(raw)
    check("unknown speakers and empties dropped", [h["speaker"] for h in clean] == ["user", "judge"], str(clean))
    check("text trimmed", clean[0]["text"] == "довод" and len(clean[1]["text"]) == mc.MAX_TEXT)
    many = mc.sanitize_history([{"speaker": "user", "text": "реплика %d" % i} for i in range(40)])
    check("tail only", len(many) == mc.MAX_HISTORY and many[-1]["text"] == "реплика 39")
    check("not a list", mc.sanitize_history(None) == [] and mc.sanitize_history("x") == [])
    kept = mc.sanitize_history([{"speaker": "user", "text": "q", "action": "witness"}, {"speaker": "user", "text": "r", "action": "hack"},
                                {"speaker": "judge", "text": "s", "action": "witness"}])
    check("action kept only for user and only if known", kept[0].get("action") == "witness" and "action" not in kept[1] and "action" not in kept[2], str(kept))
    check("action normalized", mc.normalize_action("OBJECT") == "object" and mc.normalize_action("x") == "argue")


def test_prompts():
    case = mc.get_case("debt")
    prompt = mc.build_turn_prompt(case, [{"speaker": "judge", "text": "Слушаем."}], "Прошу взыскать долг", "object", "ГК РТ ст. 1", 2)
    check("prompt carries case and message", "Взыскание долга" in prompt and "Прошу взыскать долг" in prompt and "ВОЗРАЖЕНИЕ" in prompt)
    check("prompt carries rag and history", "ГК РТ ст. 1" in prompt and "СУДЬЯ: Слушаем." in prompt)
    empty = mc.build_turn_prompt(case, [], "довод", "argue", "", 1)
    check("empty history and rag handled", "только началось" in empty and "Фрагменты не найдены" in empty)
    ask = mc.build_turn_prompt(mc.get_case("theft"), [], "Видели ли вы это?", "witness", "", 1)
    check("witness question names the witness", "Дилором Назарова" in ask and "адресован именно ему" in ask)
    check("witness answers first", "Первой репликой отвечает СВИДЕТЕЛЬ" in ask)
    check("no objection to unclear addressee", "Не требуй уточнить" in ask)
    marked = mc.build_turn_prompt(mc.get_case("theft"), [{"speaker": "user", "text": "Видели?", "action": "witness"},
                                                           {"speaker": "user", "text": "Возражаю", "action": "object"}], "ещё", "argue", "", 2)
    check("history remembers the action", "вопрос свидетелю: Дилором Назарова" in marked and "[возражение]" in marked)
    verdict = mc.build_verdict_prompt(case, [], 64, ["ГК РТ, ст. 384"])
    check("verdict prompt", "64 из 100" in verdict and "ГК РТ, ст. 384" in verdict)
    check("outcome defined by user's goal", "по цели пользователя" in verdict and "оправдание" in verdict)


def test_parse_turn():
    good = {"lines": [{"speaker": "Opponent", "text": " Возражаю. "}, {"speaker": "bailiff", "text": "x"}, {"speaker": "judge", "text": "Принято."}],
            "assessment": {"legal_basis": 7, "logic": "8", "evidence": 99, "procedure": -3, "comment": "Хорошо", "hint": "Ссылайтесь на статью"}}
    parsed = mc.parse_turn(good)
    check("lines filtered", [l["speaker"] for l in parsed["lines"]] == ["opponent", "judge"], str(parsed))
    check("scores clipped", parsed["parts"] == {"legal_basis": 7, "logic": 8, "evidence": 10, "procedure": 0}, str(parsed["parts"]))
    check("texts kept", parsed["comment"] == "Хорошо" and parsed["hint"])
    check("no usable lines", mc.parse_turn({"lines": [{"speaker": "x", "text": "y"}]}) is None)
    check("not a dict", mc.parse_turn("text") is None and mc.parse_turn(None) is None)
    many = {"lines": [{"speaker": "judge", "text": "р%d" % i} for i in range(9)]}
    check("lines capped", len(mc.parse_turn(many)["lines"]) == mc.MAX_LINES)
    check("missing assessment is zeros", mc.parse_turn({"lines": [{"speaker": "judge", "text": "ok"}]})["parts"] == {n: 0 for n in mc.CRITERIA})


def test_turn_score():
    parts = {"legal_basis": 6, "logic": 6, "evidence": 6, "procedure": 6}
    check("base score", mc.turn_score(parts, []) == 60)
    check("verified citations add", mc.turn_score(parts, [{"status": "verified"}]) == 65)
    check("existing but off-topic article is neutral", mc.turn_score(parts, [{"status": "exists"}]) == 60)
    check("bonus capped at two", mc.turn_score(parts, [{"status": "verified"}] * 5) == 70)
    check("invented article subtracts", mc.turn_score(parts, [{"status": "missing"}]) == 52)
    check("wrong code subtracts", mc.turn_score(parts, [{"status": "wrong_code"}]) == 52)
    check("penalty capped at two", mc.turn_score(parts, [{"status": "missing"}] * 5) == 44)
    check("clamped low", mc.turn_score({n: 0 for n in mc.CRITERIA}, [{"status": "missing"}] * 2) == 0)
    check("clamped high", mc.turn_score({n: 10 for n in mc.CRITERIA}, [{"status": "verified"}] * 2) == 100)


def test_verdict():
    data = {"verdict": " Иск удовлетворить частично. ", "outcome": "WON", "overall": 130,
            "breakdown": {"legal_basis": 70, "logic": "80", "evidence": -5},
            "strengths": ["a", "", "b", "c", "d"], "mistakes": "bad", "study": ["тема"]}
    parsed = mc.parse_verdict(data)
    check("verdict parsed", parsed["verdict"] == "Иск удовлетворить частично." and parsed["outcome"] == "won", str(parsed))
    check("overall clipped", parsed["overall"] == 100)
    check("breakdown clipped with default", parsed["breakdown"] == {"legal_basis": 70, "logic": 80, "evidence": 0, "procedure": 50}, str(parsed["breakdown"]))
    check("lists cleaned", parsed["strengths"] == ["a", "b", "c"] and parsed["mistakes"] == [] and parsed["study"] == ["тема"])
    check("unknown outcome", mc.parse_verdict({"verdict": "x", "outcome": "??"})["outcome"] == "partial")
    check("no verdict", mc.parse_verdict({"outcome": "won"}) is None and mc.parse_verdict(None) is None)


def test_relevant_titles():
    items = [{"title": "Кража"}, {"title": "Разглашение тайны усыновления"}, {"title": "Мелкое хищение"}, {"title": ""}]
    got = [i["title"] for i in mc.relevant_titles("кража тайное хищение чужого имущества", items)]
    check("off-topic article dropped", got == ["Кража", "Мелкое хищение"], str(got))
    check("limit", len(mc.relevant_titles("кража хищение", [{"title": "Кража"}] * 9, limit=3)) == 3)
    check("nothing relevant", mc.relevant_titles("кража", [{"title": "Брак"}]) == [])


def test_evidence_cards():
    card = mc.public_case("theft")
    check("evidence on card", len(card["evidence"]) == 4 and all(e["id"] and e["icon"] and e["title"] for e in card["evidence"]))
    tr = mc.public_case("theft", {"evidence": [{"title": "Сабти видео", "detail": "..."}]})
    check("evidence translated by index", tr["evidence"][0]["title"] == "Сабти видео" and tr["evidence"][1]["title"] == card["evidence"][1]["title"])
    prompt = mc.build_turn_prompt(mc.get_case("theft"), [], "Прошу приобщить", "argue", "", 1,
                                  exhibit={"title": "Журнал павильона", "detail": "Запись в 13:10"})
    check("materials in prompt", "МАТЕРИАЛЫ ДЕЛА" in prompt and "Видеозапись с камеры" in prompt)
    check("exhibit in prompt", "ПРЕДЪЯВЛЯЕТ СУДУ ДОКАЗАТЕЛЬСТВО: «Журнал павильона»" in prompt and "ruling" in prompt)
    check("no exhibit block without exhibit", "ПРЕДЪЯВЛЯЕТ" not in mc.build_turn_prompt(mc.get_case("theft"), [], "x", "argue", "", 1))
    hist = mc.sanitize_history([{"speaker": "user", "text": "вот", "exhibit": "Расписка"}])
    check("history keeps exhibit", hist[0].get("exhibit") == "Расписка")
    check("dialogue marks exhibit", "предъявлено доказательство: Расписка" in mc.build_turn_prompt(mc.get_case("debt"), hist, "x", "argue", "", 2))
    check("exhibit sanitized", mc.sanitize_exhibit({"title": " Расписка ", "detail": ""}) == {"id": "", "title": "Расписка", "detail": "Расписка"})
    check("bad exhibit", mc.sanitize_exhibit({"title": ""}) is None and mc.sanitize_exhibit("x") is None)


def test_parse_turn_ruling_and_new_evidence():
    base = {"lines": [{"speaker": "judge", "text": "Приобщить."}]}
    got = mc.parse_turn(dict(base, ruling="ADMITTED", new_evidence=[{"title": "Показания продавца", "detail": "Не видела"}, {"title": "x", "detail": "y"}]))
    check("ruling parsed", got["ruling"] == "admitted")
    check("one new evidence", got["new_evidence"] == [{"title": "Показания продавца", "detail": "Не видела"}], str(got["new_evidence"]))
    junk = mc.parse_turn(dict(base, ruling="maybe", new_evidence=[{"title": "", "detail": "y"}, "x"]))
    check("junk ignored", junk["ruling"] == "none" and junk["new_evidence"] == [])


def test_custom_case():
    raw = {"kind": "LABOR", "title": "Невыплата зарплаты", "role": "представитель работника", "opponent": "",
           "opponent_gender": "x", "witness": "Сино Раҳимова, бухгалтер", "witness_gender": "f",
           "goal": "Взыскать задолженность по зарплате.", "facts": "Работник три месяца не получал заработную плату. " * 3,
           "query": "сроки выплаты заработной платы ответственность работодателя",
           "evidence": [{"icon": "📄", "title": "Трудовой договор", "detail": "Оклад 3000 сомони."}, {"title": "", "detail": "x"}] + [
               {"title": "Док %d" % i, "detail": "Суть"} for i in range(9)]}
    case = mc.sanitize_custom(raw)
    check("custom kind normalized", case["kind"] == "labor")
    check("default opponent by kind", case["opponent"] == "Представитель работодателя")
    check("genders", case["opponent_gender"] == "m" and case["witness_gender"] == "f")
    check("evidence capped and cleaned", len(case["evidence"]) == mc.MAX_EVIDENCE and case["evidence"][0]["icon"] == "📄" and case["evidence"][1]["icon"] == "📎")
    check("resolve custom", mc.resolve_case("custom", raw)["title"] == "Невыплата зарплаты")
    check("resolve builtin", mc.resolve_case("theft", None)["title"] == mc.CASES["theft"]["title"])
    check("too short facts rejected", mc.sanitize_custom(dict(raw, facts="Коротко.")) is None)
    check("missing role rejected", mc.sanitize_custom(dict(raw, role="")) is None)
    check("not a dict", mc.sanitize_custom("x") is None and mc.resolve_case("custom", None) is None)
    check("weak query replaced", len(mc.sanitize_custom(dict(raw, query="зарплата"))["query"].split()) >= 3)
    card = mc.card_of("custom", case)
    check("custom card", card["id"] == "custom" and len(card["evidence"]) == mc.MAX_EVIDENCE)
    prompt = mc.build_custom_prompt("Меня уволили", "представитель работника")
    check("custom prompt", "Меня уволили" in prompt and "представитель работника" in prompt and "evidence" in prompt)


def test_final_score_and_grade():
    check("no turns uses judge", mc.final_score([], 72) == 72)
    check("half and half", mc.final_score([60, 60, 60], 80) == 70)
    check("later turns weigh more", mc.final_score([40, 90], 60) > mc.final_score([90, 40], 60))
    check("ignores junk", mc.final_score(["x", None, 50], 50) == 50)
    check("grades", [mc.grade(s) for s in (95, 75, 55, 10)] == ["excellent", "good", "fair", "weak"])


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
