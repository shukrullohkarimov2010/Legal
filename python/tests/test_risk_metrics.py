# -*- coding: utf-8 -*-
"""Тесты сведения оценки риска договора в вердикт.

    python tests/test_risk_metrics.py

Проверяется summarize_contract_risk — та часть анализа, где легче всего
ошибиться в знаке и где ошибка не видна ни в логах, ни на глаз. Модуль app
целиком не импортируется: он тянет Flask, RAG-индекс и Ollama, а вся логика
вердикта чистая и от них не зависит.
"""

from __future__ import annotations

import ast
import os
import sys
import traceback

APP_PATH = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "app.py")

FAILURES: list[str] = []


def _load_from_app(*names):
    """Достаёт функции и таблицы из app.py, не выполняя модуль целиком."""
    import re as _re

    with open(APP_PATH, encoding="utf-8") as handle:
        tree = ast.parse(handle.read())

    namespace: dict = {"re": _re}
    wanted = set(names)
    for node in tree.body:
        picked = (
            isinstance(node, ast.FunctionDef) and node.name in wanted
        ) or (
            isinstance(node, ast.Assign)
            and getattr(node.targets[0], "id", "") in wanted
        )
        if picked:
            exec(compile(ast.Module([node], []), APP_PATH, "exec"), namespace)

    missing = [name for name in names if name not in namespace]
    if missing:
        raise AssertionError("не найдено в app.py: %s" % ", ".join(missing))
    return [namespace[name] for name in names]


(summarize, raw_risk_score, severity_series, check_essential_conditions, classify_contract_type,
 ESSENTIAL_CLAUSE_PATTERNS, CLAUSES_NOT_APPLICABLE, CONTRACT_TYPE_KEYWORDS,
 detect_wrong_jurisdiction, check_wrong_jurisdiction_in_contract, _title_patterns, _title_chars,
 _title_score) = _load_from_app(
    "summarize_contract_risk", "_raw_risk_score", "_severity_series", "check_essential_conditions",
    "classify_contract_type", "ESSENTIAL_CLAUSE_PATTERNS", "CLAUSES_NOT_APPLICABLE", "CONTRACT_TYPE_KEYWORDS",
    "detect_wrong_jurisdiction", "check_wrong_jurisdiction_in_contract",
    "TITLE_TYPE_PATTERNS", "TITLE_TYPE_SCAN_CHARS", "TITLE_TYPE_SCORE",
)

GIFT_CONTRACT = (
    "ДОГОВОР ДАРЕНИЯ. Даритель безвозмездно передает Одаряемому в собственность "
    "квартиру. Даритель гарантирует, что дар не обременен правами третьих лиц. "
    "Одаряемый принимает дар. Предмет договора: передача квартиры в дар. "
) * 3


def check(condition, message):
    if not condition:
        FAILURES.append(message)


def test_score_maps_to_level():
    """Уровень обязан следовать за оценкой, иначе в отчёте «Высокий риск — 35/100»."""
    for raw, level in [(0, "Низкий"), (24, "Низкий"), (25, "Средний"),
                       (64, "Средний"), (65, "Высокий"), (99, "Высокий")]:
        _, got, _, _ = summarize(raw, findings_count=3, contract_len=2000,
                                 wrong_jurisdiction_count=0)
        check(got == level, "оценка %s дала уровень %s вместо %s" % (raw, got, level))


def test_score_never_reaches_hundred():
    """100 зарезервировано за подтверждённой человеком ситуацией."""
    score, _, _, _ = summarize(400, findings_count=20, contract_len=5000,
                               wrong_jurisdiction_count=0)
    check(score == 95, "оценка не ограничена сверху: %s" % score)


def test_raw_score_does_not_saturate_for_different_profiles():
    """Регрессия: разные профили находок давали ОДИНАКОВЫЙ балл 95.

    Обнаружено на пяти реальных тестовых договорах с разным числом и составом
    находок (3/8/1, 5/6/2, 2/6/3, 5/4/3, 5/5/1 высоких/средних/низких) —
    прежняя модель (накопление вероятностей: score = 100 * (1 - произведение
    (1 - impact/100))) для всех пяти давала 95/100. Пользователь получал один
    и тот же «балл риска» независимо от того, насколько на самом деле
    отличались договоры, — то есть фейковый, недостоверный балл.
    """
    profiles = [(3, 8, 1), (5, 6, 2), (2, 6, 3), (5, 4, 3), (5, 5, 1)]
    scores = [raw_risk_score(h, m, l) for h, m, l in profiles]
    check(len(set(scores)) > 1,
          "разные профили находок дали один и тот же балл: %s -> %s" % (profiles, scores))
    # Меньше и менее серьёзных находок обязано давать меньший балл.
    check(raw_risk_score(2, 6, 3) < raw_risk_score(5, 6, 2),
          "договор с 2 высокими рисками получил балл не ниже, чем с 5")


def test_raw_score_zero_findings_is_zero():
    check(raw_risk_score(0, 0, 0) == 0, "договор без находок получил ненулевой балл")


def test_wrong_jurisdiction_in_contract_text_is_caught_deterministically():
    """Регрессия: договор, явно ссылающийся на право РФ, иногда пропускался.

    detect_wrong_jurisdiction проверяет только вывод модели. Строгий протокол
    доказательности запрещает ей заявлять риск без цитируемой статьи RAG, а
    гражданский кодекс РТ не обязан содержать норму о выборе иностранного
    права для конкретного фрагмента. На практике один и тот же тестовый
    договор со ссылкой на "Гражданский кодекс Российской Федерации" был
    пойман моделью в одном прогоне и полностью пропущен в другом — сам текст
    не менялся. check_wrong_jurisdiction_in_contract проверяет ДОГОВОР
    регексом напрямую, независимо от того, что решит процитировать модель.
    """
    contract = (
        "5.1. Настоящий договор регулируется нормами Гражданского кодекса "
        "Российской Федерации. Споры подлежат рассмотрению в Арбитражном "
        "суде города Москвы."
    )
    finding = check_wrong_jurisdiction_in_contract(contract)
    check(finding is not None, "ссылка на ГК РФ в тексте договора не обнаружена")
    if finding:
        check(finding["severity"] == "high", "ссылка на чужое право получила не высокий приоритет")
        check("Российской Федерации" in finding["fragment"] or "Москвы" in finding["fragment"],
              "цитата находки не содержит найденный текст: %r" % finding["fragment"])

    clean_contract = "5.1. Споры разрешаются в судебном порядке по законодательству Республики Таджикистан."
    check(check_wrong_jurisdiction_in_contract(clean_contract) is None,
          "договор без ссылок на чужое право получил находку")


def test_foreign_law_does_not_lower_risk():
    """Главная регрессия: ссылки на чужое право снижали оценку риска.

    Было `score -= count * 20`. Три упоминания РФ превращали 70/100 и вердикт
    «Не подписывать» в 10/100 и «Можно подписывать» — чем хуже работала модель,
    тем безопаснее выглядел договор.
    """
    clean_score, _, _, _ = summarize(70, findings_count=4, contract_len=3000,
                                     wrong_jurisdiction_count=0)
    dirty_score, _, verdict, manual = summarize(70, findings_count=4, contract_len=3000,
                                                wrong_jurisdiction_count=3)
    check(dirty_score == clean_score,
          "юрисдикция изменила оценку риска: %s против %s" % (dirty_score, clean_score))
    check(verdict == "❌ Не подписывать",
          "высокий риск потерял вердикт при ссылках на чужое право: %s" % verdict)
    check(manual is True, "ссылки на чужое право не потребовали ручной проверки")


def test_foreign_law_blocks_green_verdict():
    """Скомпрометированный разбор не вправе выдать «Можно подписывать»."""
    _, _, verdict, manual = summarize(5, findings_count=2, contract_len=3000,
                                      wrong_jurisdiction_count=1)
    check(verdict == "⚠️ Требуется ручная проверка",
          "разбор со ссылкой на чужое право дал вердикт %s" % verdict)
    check(manual is True, "флаг ручной проверки не выставлен")


def test_foreign_law_overrides_minor_fixes():
    """В средней зоне «требует правок» тоже уступает ручной проверке."""
    _, _, verdict, _ = summarize(40, findings_count=2, contract_len=3000,
                                 wrong_jurisdiction_count=2)
    check(verdict == "⚠️ Требуется ручная проверка",
          "средний риск со ссылками на чужое право дал вердикт %s" % verdict)


def test_long_contract_without_findings_needs_review():
    """Длинный договор без единого риска — признак сбоя фильтров, а не чистоты."""
    _, _, verdict, manual = summarize(0, findings_count=0, contract_len=4000,
                                      wrong_jurisdiction_count=0)
    check(manual is True, "длинный договор без рисков не потребовал проверки")
    check(verdict == "⚠️ Требуется ручная проверка", "неверный вердикт: %s" % verdict)


def test_short_clean_contract_can_be_signed():
    """Короткий договор без рисков — единственный случай зелёного вердикта."""
    score, level, verdict, manual = summarize(0, findings_count=0, contract_len=200,
                                              wrong_jurisdiction_count=0)
    check((score, level) == (0, "Низкий"), "неверная оценка: %s/%s" % (score, level))
    check(verdict == "✅ Можно подписывать", "неверный вердикт: %s" % verdict)
    check(manual is False, "ручная проверка потребована напрасно")


def test_gift_contract_is_not_asked_for_price():
    """Дарение безвозмездно по определению: риск «нет цены» здесь ложный.

    Регрессия: contract_type_info принимался и не использовался, поэтому
    договор дарения получал severity=high «отсутствует цена и порядок
    расчётов» — одного такого пункта хватало, чтобы оценка ушла в «Средний».
    """
    info = classify_contract_type(GIFT_CONTRACT)
    check(info["type"] == "дарение", "классификатор не распознал дарение: %s" % info["type"])

    titles = lambda fs: {f["title"].replace("Отсутствует условие: ", "") for f in fs}
    without_type = titles(check_essential_conditions(GIFT_CONTRACT, None))
    with_type = titles(check_essential_conditions(GIFT_CONTRACT, info))

    check("цена и порядок расчётов" in without_type,
          "исходное поведение изменилось, тест потерял смысл")
    check("цена и порядок расчётов" not in with_type,
          "дарение всё ещё требует цену")
    check("предмет договора" not in with_type,
          "предмет найден в тексте, но помечен отсутствующим")
    check(without_type - with_type == {"цена и порядок расчётов"},
          "тип договора убрал лишние пункты: %s" % (without_type - with_type))


def test_weak_classification_keeps_full_checklist():
    """При слабом совпадении тип не применяется: пропустить условие опаснее."""
    weak = {"type": "дарение", "confidence": 0.2}
    titles = {f["title"] for f in check_essential_conditions(GIFT_CONTRACT, weak)}
    check(any("цена и порядок расчётов" in t for t in titles),
          "неуверенная классификация отключила проверку цены")


def test_exceptions_reference_real_clauses_and_types():
    """Опечатка в таблице исключений молча отключила бы проверку впустую."""
    for contract_type, clauses in CLAUSES_NOT_APPLICABLE.items():
        check(contract_type in CONTRACT_TYPE_KEYWORDS,
              "неизвестный тип договора в исключениях: %s" % contract_type)
        for clause in clauses:
            check(clause in ESSENTIAL_CLAUSE_PATTERNS,
                  "неизвестное условие в исключениях: %s" % clause)


polish_findings, = _load_from_app("_ARTICLE_MENTION_RE", "_ARTICLE_MENTION_FORMS", "_cited_article_numbers",
                                  "_align_article_mentions", "_polish_findings")[-1:]


def _finding(**fields):
    base = {"title": "риск", "norm": "Гражданский кодекс Республики Таджикистан, Глава 39, Статья 675",
            "why": "", "fix": ""}
    base.update(fields)
    return base


def test_foreign_article_in_free_text_is_replaced_by_verified_norm():
    found = polish_findings([_finding(fix="обеспечить регистрацию, если объект подпадает под статью 718.",
                                      why="Согласно статье 675 договор подлежит регистрации.")])[0]
    check("718" not in found["fix"] and "под указанную выше статью" in found["fix"],
          "статья вне проверенной нормы осталась в правке: %s" % found["fix"])
    check("статье 675" in found["why"], "проверенная статья не должна меняться: %s" % found["why"])


def test_list_of_articles_is_kept_only_when_every_number_is_verified():
    found = polish_findings([_finding(why="см. статьи 675 и 676", norm="Статья 675, Статья 676")])[0]
    check("статьи 675 и 676" in found["why"], found["why"])
    found = polish_findings([_finding(why="см. статьи 675 и 999")])[0]
    check("999" not in found["why"] and "указанной выше статьи" in found["why"], found["why"])


def test_finding_without_article_is_not_touched_and_title_gets_capital():
    found = polish_findings([_finding(title="отсутствия регистрации аренды", norm="", why="по статье 5")])[0]
    check(found["why"] == "по статье 5", "без нормы нечего сверять: %s" % found["why"])
    check(found["title"] == "Отсутствия регистрации аренды", found["title"])


is_duplicate_risk, = _load_from_app("_normalize_text", "_calculate_text_similarity", "_stems", "_fragment_overlap",
                                    "_is_duplicate_risk")[-1:]


def test_same_clause_same_norm_is_one_finding_but_other_clause_is_not():
    norm = "Трудовой кодекс Республики Таджикистан, Глава 11, Статья 158"
    pay_a = {"title": "Выплата заработной платы реже установленного срока и по усмотрению работодателя",
             "point": "3.2", "norm": norm,
             "fragment": "Заработная плата выплачивается один раз в два месяца, по усмотрению Работодателя"}
    pay_b = {"title": "Выплата зарплаты реже установленного срока и недопустимыми способами", "point": "3.2",
             "norm": norm,
             "fragment": "один раз в два месяца, по усмотрению Работодателя, наличными или товарами предприятия."}
    check(is_duplicate_risk(pay_a, pay_b), "один пункт, одна норма, общая цитата — должен быть дублем")
    ban = {"title": "Запрет работнику расторгать договор", "point": "4.2", "norm": "Статья 41",
           "fragment": "не вправе расторгать договор по собственной инициативе в течение первых трёх лет"}
    fine = {"title": "Неустойка за расторжение договора работником", "point": "4.2", "norm": "Статья 41",
            "fragment": "в противном случае он выплачивает Работодателю неустойку в размере 500 000 сомони."}
    check(not is_duplicate_risk(ban, fine), "запрет и неустойка — разные условия, склеивать нельзя")
    other_norm = dict(pay_b, norm="Трудовой кодекс Республики Таджикистан, Глава 11, Статья 159")
    check(not is_duplicate_risk(pay_a, other_norm), "разные нормы — разные находки")


clean_quote, repair_quote, quote_matches = _load_from_app(
    "_stems", "_QUOTE_EDGES", "_clean_quote", "_repair_quote_from_contract", "_quote_matches_contract")[2:]

PAY_CONTRACT = ("3.1. Должностной оклад Работника составляет 20 000 рублей в месяц.\n"
                "3.2. Заработная плата выплачивается один раз в два месяца, по усмотрению Работодателя, "
                "наличными или товарами предприятия.\n4.1. Работодатель вправе расторгнуть договор в любое время.")


def test_quote_edges_are_stripped_so_short_quoted_clause_is_found():
    check(clean_quote("«наличными или товарами предприятия»") == "наличными или товарами предприятия",
          "кавычки по краям должны сниматься")
    check(clean_quote("««дважды»»") == "дважды", "двойные кавычки тоже")
    check(quote_matches(clean_quote("«наличными или товарами предприятия»").lower(), PAY_CONTRACT.lower()),
          "короткая цитата без кавычек находится в договоре")


def test_paraphrased_quote_is_replaced_by_the_verbatim_sentence():
    finding = {"fragment": "Зарплата выплачивается наличными либо товарами предприятия по усмотрению работодателя"}
    check(repair_quote(finding, PAY_CONTRACT), "пересказ реального пункта должен восстановиться")
    check(finding["fragment"].startswith("Заработная плата выплачивается один раз в два месяца"),
          "в отчёт идёт дословное предложение: %s" % finding["fragment"])
    check(finding["fragment"] in PAY_CONTRACT, "восстановленная цитата обязана быть подстрокой договора")


def test_fabricated_quote_is_not_repaired():
    finding = {"fragment": "Арендатор обязан выплатить штраф за курение в помещении склада"}
    check(not repair_quote(finding, PAY_CONTRACT), "выдуманная цитата не должна находить пункт")
    check(finding["fragment"] == "Арендатор обязан выплатить штраф за курение в помещении склада",
          "выдуманная цитата не должна меняться")


def test_minimum_wage_topic_is_matched_for_a_salary_clause():
    sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    import contract_topics

    names = {t["name"] for t in contract_topics.match_topics(
        "Должностной оклад Работника составляет 20 000 рублей в месяц.", "трудовой договор", labor=True)}
    check({"минимальная заработная плата", "форма и валюта оплаты труда"} <= names, "темы: %s" % sorted(names))
    civil = {t["name"] for t in contract_topics.match_topics(
        "Арендная плата составляет 3 000 долларов США в месяц.", "аренда", labor=False)}
    check("минимальная заработная плата" not in civil, "трудовая тема попала в гражданский договор")


JOINT_ACTIVITY_CONTRACT = (
    "ДОГОВОР СУБПОДРЯДА И СОВМЕСТНОЙ ОРГАНИЗАЦИОННОЙ ДЕЯТЕЛЬНОСТИ. Заказчик передаёт Исполнителю денежные "
    "средства в качестве безотзывных инвестиций на реализацию Проекта. Исполнитель вправе самостоятельно "
    "изменять направление использования средств без предварительного уведомления Заказчика. "
)


def test_joint_activity_contract_is_not_classified_as_contracting():
    """Слово «субподряд» в названии не должно делать из договора совместной деятельности подряд."""
    info = classify_contract_type(JOINT_ACTIVITY_CONTRACT)
    check(info["type"] == "простой товарищества", "тип: %s" % info["type"])


def test_joint_activity_clauses_get_their_own_norm_topics():
    """Без этих тем кандидатами были статьи о подряде и НИОКР, и каждый вывод получал «статья не найдена»."""
    sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    import contract_topics

    def topics(text, contract_type="простой товарищества"):
        return {t["name"] for t in contract_topics.match_topics(text, contract_type, labor=False)}

    cases = {
        "ответственность Исполнителя ограничивается символической суммой в 1 сомони": "ограничение ответственности",
        "Отчёт предоставляется только по желанию Исполнителя": "отчётность и информация",
        "Обращение в суды допускается только после 12 месяцев претензионного процесса": "судебная защита",
        "Заказчик передаёт средства в качестве безотзывных инвестиций": "совместная деятельность",
        "Заказчик не вправе требовать возврата средств": "выход из совместной деятельности",
        "Исполнитель вправе изменять направление использования средств без предварительного уведомления":
            "односторонние действия",
    }
    for text, expected in cases.items():
        check(expected in topics(text), "«%s»: нет темы «%s», есть %s" % (text, expected, sorted(topics(text))))
    check("выход из совместной деятельности" not in topics("Заказчик не вправе требовать возврата средств", "аренда"),
          "тема товарищества применилась к другому типу договора")


def test_foreign_court_liability_and_housing_clauses_get_their_topics():
    """Контрольные договоры: суд в Нью-Йорке, «не несёт никакой ответственности», вход в квартиру без уведомления."""
    sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
    import contract_topics

    def topics(text, contract_type):
        return {t["name"] for t in contract_topics.match_topics(text, contract_type, labor=False)}

    cases = [
        ("исключительно в суде г. Нью-Йорк (США) по законодательству штата Нью-Йорк", "возмездное оказание услуг",
         {"применимое право", "иностранный суд"}),
        ("Исполнитель не несет никакой ответственности за качество услуг", "возмездное оказание услуг",
         {"ограничение ответственности"}),
        ("Арендодатель вправе в любое время входить в квартиру без предварительного уведомления", "аренда",
         {"неприкосновенность жилища"}),
        ("Арендатор не вправе принимать гостей без согласия Арендодателя", "аренда", {"проживание других лиц"}),
        ("Обеспечительный платеж ни при каких обстоятельствах не возвращается", "аренда", {"обеспечительный платёж"}),
    ]
    for text, contract_type, expected in cases:
        found = topics(text, contract_type)
        check(expected <= found, "«%s»: ждали %s, есть %s" % (text, sorted(expected), sorted(found)))
    check("неприкосновенность жилища" not in topics("входить в квартиру без уведомления", "поставка"),
          "жилищная тема применилась к договору поставки")


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
