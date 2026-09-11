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
 detect_wrong_jurisdiction, check_wrong_jurisdiction_in_contract) = _load_from_app(
    "summarize_contract_risk", "_raw_risk_score", "_severity_series", "check_essential_conditions",
    "classify_contract_type", "ESSENTIAL_CLAUSE_PATTERNS", "CLAUSES_NOT_APPLICABLE", "CONTRACT_TYPE_KEYWORDS",
    "detect_wrong_jurisdiction", "check_wrong_jurisdiction_in_contract",
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
