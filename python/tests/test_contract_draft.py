# -*- coding: utf-8 -*-
"""Тесты проверки правки договора на /contract/create.

    python tests/test_contract_draft.py

Проверяется _verify_draft — сторож, решающий, запускать ли повторную правку.
Цена ошибки здесь измеряется минутами: ложное срабатывание на замере стоило
360 секунд лишней генерации, а пропущенный сбой отдаёт пользователю договор
без условия, которое он явно просил.

Модуль app целиком не импортируется: он тянет Flask, RAG-индекс и Ollama,
а логика сверки чистая и от них не зависит.
"""

from __future__ import annotations

import ast
import os
import re
import sys
import traceback

APP_PATH = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "app.py")

FAILURES: list[str] = []


def _load_from_app(*names):
    """Достаёт функции и таблицы из app.py, не выполняя модуль целиком."""
    with open(APP_PATH, encoding="utf-8") as handle:
        tree = ast.parse(handle.read())

    namespace: dict = {"re": re}
    wanted = set(names)
    for node in tree.body:
        picked = (
            isinstance(node, ast.FunctionDef) and node.name in wanted
        ) or (
            isinstance(node, ast.Assign) and getattr(node.targets[0], "id", "") in wanted
        )
        if picked:
            exec(compile(ast.Module([node], []), APP_PATH, "exec"), namespace)

    missing = [name for name in names if name not in namespace]
    if missing:
        raise AssertionError("не найдено в app.py: %s" % ", ".join(missing))
    return [namespace[name] for name in names]


(verify_draft, significant_numbers, number_present, criteria_numbers) = _load_from_app(
    "_verify_draft", "_significant_numbers", "_number_present", "_criteria_numbers",
    "_CLAUSE_NUMBER_RE", "_grouped",
)[:4]

FORM = ["Номер договора: LA-042/26", "Сумма: 120000", "Срок оплаты, дней: 5"]
CRITERIA = ["Предоплата 30% в течение 3 рабочих дней", "Гарантийный срок 12 месяцев"]
ORIGINALS = {
    "payment": "3. ОПЛАТА\n3.1. Стоимость услуг составляет 120000 сомони.\n3.2. Оплата в течение 5 дней.",
    "liability": "4. ОТВЕТСТВЕННОСТЬ\n4.1. Стороны несут ответственность по закону.",
    "termination": "5. РАСТОРЖЕНИЕ\n5.1. Договор действует до 31.12.2026.",
}


def check(condition, message):
    if not condition:
        FAILURES.append(message)


def test_clause_numbering_is_not_a_fact():
    """«5.1» — нумерация пункта, а не сумма: следить за ней нельзя."""
    check(significant_numbers("5.1. Договор действует до 31.12.2026") == ["31.12", "2026"],
          "нумерация пункта попала в числа-факты: %s"
          % significant_numbers("5.1. Договор действует до 31.12.2026"))
    check(significant_numbers("3.1. Сумма 120000 сомони") == ["120000"],
          "лишние числа в фактах: %s" % significant_numbers("3.1. Сумма 120000 сомони"))


def test_number_present_respects_boundaries():
    """Регрессия: «12» находилось подстрокой внутри «120000».

    Из-за этого пропущенное требование «гарантия 12 месяцев» считалось
    выполненным только потому, что рядом стояла сумма договора.
    """
    check(not number_present("12", "Стоимость 120000 сомони"),
          "число найдено внутри другого числа")
    check(number_present("12", "Гарантия 12 месяцев"), "самостоятельное число не найдено")
    check(number_present("120000", "Стоимость 120 000 сомони"),
          "не распознан разделитель разрядов")
    check(not number_present("26", "Договор до 31.12.2026"),
          "«26» из номера договора найдено внутри года")


def test_renumbering_does_not_trigger_retry():
    """Регрессия: раздел переехал с пятого места на третье и перенумеровался.

    Первая редакция сверки считала это утратой данных и запускала повтор,
    который на замере стоил 72 секунды и ничего не исправлял.
    """
    edited = {"payment": "7.1. Стоимость 120 000 сомони.\n7.2. Оплата 5 дней. "
                         "Предоплата 30% за 3 дня, гарантия 12 месяцев."}
    lost, unapplied = verify_draft(FORM, CRITERIA, ORIGINALS, edited)
    check(lost == [], "перенумерация принята за потерю: %s" % lost)
    check(unapplied == [], "требования сочтены неприменёнными: %s" % unapplied)


def test_fact_is_required_only_in_its_own_section():
    """Регрессия: сумма требовалась в разделах, где её никогда не было.

    Вторая редакция искала факты во всём выводе разом, и правка разделов об
    ответственности и расторжении уходила на повтор из-за отсутствия суммы
    договора — она живёт в разделе об оплате, который не редактировался.
    Этот случай стоил 360 секунд на замере.
    """
    edited = {"liability": "1.1. Односторонний отказ Заказчика без компенсации запрещён."}
    lost, _ = verify_draft(FORM, ["Запретить односторонний отказ"], ORIGINALS, edited)
    check(lost == [], "факт затребован в чужом разделе: %s" % lost)


def test_lost_amount_is_detected():
    """Сумма, исчезнувшая из своего же раздела, обязана быть замечена."""
    edited = {"payment": "1.1. Оплата в течение 5 дней. Предоплата 30% за 3 дня. Гарантия 12 месяцев."}
    lost, _ = verify_draft(FORM, CRITERIA, ORIGINALS, edited)
    check(lost == ["120000"], "потеря суммы не замечена: %s" % lost)


def test_ignored_criteria_are_detected():
    """Требование пользователя, не попавшее в текст, — повод для повтора."""
    edited = {"payment": "1.1. Стоимость 120000 сомони.\n1.2. Оплата в течение 5 дней."}
    _, unapplied = verify_draft(FORM, CRITERIA, ORIGINALS, edited)
    check(unapplied == ["30", "3", "12"], "пропуск требований не замечен: %s" % unapplied)


def test_criteria_without_numbers_never_block():
    """Нечисловое требование машинно не проверяется и повтор не запускает."""
    edited = {"liability": "1.1. Односторонний отказ запрещён без компенсации."}
    _, unapplied = verify_draft(FORM, ["Запретить односторонний отказ"], ORIGINALS, edited)
    check(unapplied == [], "нечисловое требование вызвало повтор: %s" % unapplied)


def test_empty_edit_is_not_a_loss():
    """Пустая правка не должна выглядеть как потеря фактов."""
    lost, unapplied = verify_draft(FORM, [], ORIGINALS, {})
    check(lost == [] and unapplied == [], "пустая правка дала претензии: %s / %s" % (lost, unapplied))


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
