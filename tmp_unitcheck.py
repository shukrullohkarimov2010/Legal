import sys, os, json, io, copy
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), "python"))
import app as A

OUT = io.StringIO()
def log(*args):
    print(*args)
    OUT.write(" ".join(str(a) for a in args) + "\n")

CONTRACT_TEXT = """
ДОГОВОР ВОЗМЕЗДНОГО ОКАЗАНИЯ УСЛУГ

1. Предмет договора.
1.1. Исполнитель обязуется оказать Заказчику юридические консультации, а Заказчик обязуется оплатить эти услуги.
2. Стоимость услуг.
2.1. Стоимость услуг составляет 10000 сомони.
2.2. Оплаченные суммы возврату не подлежат.
3. Права и обязанности сторон.
3.1. Заказчик вправе расторгнуть договор в одностороннем порядке без объяснения причин в любое время. Исполнитель не вправе расторгнуть договор.
3.2. Исполнитель обязуется оказать услуги качественно.
4. Конфиденциальность.
4.1. Любая информация по проекту считается конфиденциальной.
5. Ответственность сторон.
5.1. За неисполнение обязательств по настоящему договору стороны несут ответственность в соответствии с законодательством Республики Таджикистан.
6. Прочие условия.
6.1. Настоящий договор вступает в силу с момента подписания.
"""

results = {}

# 1) matcher
q = "Заказчик вправе расторгнуть соглашение без объяснения причин в любой момент"
r1 = A._quote_matches_contract(A._normalize_text(q), A._normalize_text(CONTRACT_TEXT))
results["1_quote_reformulation"] = r1
log("1) quote reformulation match:", r1)

# 2) evidence
f = [{
    "title": "Одностороннее расторжение",
    "severity": "medium",
    "point": "3.1",
    "fragment": "Заказчик вправе расторгнуть договор в одностороннем порядке без объяснения причин в любое время",
    "norm": "Гражданский кодекс РТ, Статья 450",
    "why": "Ущемляет права исполнителя",
    "fix": "Добавить взаимное право расторжения",
}]
kept = A._filter_findings_by_contract_evidence(f, CONTRACT_TEXT)
results["2_evidence_kept"] = len(kept)
log("2) contract evidence kept:", len(kept))

# 3) checklist
heuristic = A.check_essential_conditions(CONTRACT_TEXT, {"type": "возмездное оказание услуг"})
results["3_checklist_count"] = len(heuristic)
log("3) heuristic missing clauses count:", len(heuristic))

# 4) severity escalation
f2 = copy.deepcopy(f)
f2[0]["severity"] = "medium"
f2 = A._apply_severity_heuristics(f2)
results["4_severity_termination"] = f2[0]["severity"]
log("4) one-sided termination severity:", f2[0]["severity"])

f3 = [{
    "title": "Депозитный риск",
    "severity": "low",
    "point": "7.2",
    "fragment": "Оплаченные суммы возврату не подлежат",
    "norm": "ГК РТ ст 450",
    "why": "Потеря денег",
    "fix": "Вернуть",
}]
f3 = A._apply_severity_heuristics(f3)
results["4b_severity_refund"] = f3[0]["severity"]
log("   non-refundable severity:", f3[0]["severity"])

# 5) full metrics
metrics = A.estimate_risk_metrics(
    "[Высокий] Одностороннее расторжение\nПункт договора: 3.1\nФрагмент договора: Заказчик вправе расторгнуть договор в одностороннем порядке без объяснения причин в любое время\nНорма РТ: Гражданский кодекс РТ, Статья 450\nПочему риск: Ущемляет права исполнителя, нет взаимного права\nЧто исправить: добавить взаимное право расторжения\n",
    contract_text=CONTRACT_TEXT,
    contract_type_info=A.classify_contract_type(CONTRACT_TEXT),
)
results["5_score"] = metrics["score"]
results["5_verdict"] = metrics["verdict"]
results["5_findings"] = [(x["title"], x["severity"]) for x in metrics["findings"]]
results["5_norm_present"] = any("Статья 450" in x.get("norm_text", "") for x in metrics["findings"])
log("5) metrics score/verdict:", metrics["score"], metrics["verdict"], "findings:", len(metrics["findings"]))
log("   norm_text contains Статья 450:", results["5_norm_present"])

# 6) manual review warning
long_text = "\n".join([
    "ДОГОВОР ПОСТАВКИ",
    "1. Предмет договора: Поставщик обязуется передать товар Покупателю, а Покупатель обязуется принять и оплатить товар.",
    "2. Цена и порядок расчётов: цена договора составляет 50000 сомони, оплата производится в течение 10 банковских дней после поставки.",
    "3. Срок исполнения: поставка осуществляется в течение 30 дней с момента подписания настоящего договора.",
    "4. Качество товара: поставляемый товар должен соответствовать сертификатам качества. Порядок приёмки товара по количеству и качеству установлен в течение 5 дней.",
    "5. Ответственность сторон: за просрочку поставки Поставщик уплачивает неустойку в размере 0,1% за каждый день просрочки.",
    "6. Порядок расторжения: договор может быть расторгнут по соглашению сторон либо в судебном порядке.",
    "7. Разрешение споров: споры разрешаются в суде Республики Таджикистан.",
    "8. Форс-мажор: стороны освобождаются от ответственности при наступлении обстоятельств непреодолимой силы.",
    "9. Уведомления: стороны обязуются направлять уведомления на указанные адреса.",
    "10. Срок действия договора: договор действует до 31 декабря 2026 года.",
])
m2 = A.estimate_risk_metrics("=== Часть 1 ===\nАнализатор не выявил подтверждённых рисков в данной части.",
                            contract_text=long_text)
results["6_manual_review"] = m2.get("manual_review_required")
results["6_verdict"] = m2["verdict"]
log("6) long clean contract: score=%s verdict=%s manual_review=%s n_findings=%s" % (m2["score"], m2["verdict"], m2.get("manual_review_required"), len(m2["findings"])))

# 7) report render
report = A.build_grounded_contract_report(metrics, contract_type_info=A.classify_contract_type(CONTRACT_TEXT))
results["7_report_has_norm"] = "Текст нормы из подключённого RAG" in report
log("7) report includes norm text block:", results["7_report_has_norm"])

assert results["1_quote_reformulation"] is True
assert results["2_evidence_kept"] == 1
assert results["4_severity_termination"] == "high", results["4_severity_termination"]
assert results["4b_severity_refund"] == "high", results["4b_severity_refund"]
assert results["5_norm_present"] is True
assert results["6_manual_review"] is True, results["6_manual_review"]
assert results["7_report_has_norm"] is True

# 8) REGRESSION: модельный вывод БЕЗ [severity] (формат drift v14) — парсер
#    обязан вернуть реальные риски из контракта и НЕ выдумывать отсутствующие.
braceless = (
    "Риск одностороннего расторжения договора\n"
    "Пункт договора: 3.1\n"
    "Фрагмент договора: Заказчик вправе расторгнуть договор в одностороннем порядке без объяснения причин в любое время\n"
    "Норма РТ: Гражданский кодекс РТ, Статья 450\n"
    "Почему риск: Ущемляет права исполнителя, нет взаимного права расторжения\n"
    "Что исправить: Добавить взаимное право расторжения сторонами\n\n"
    "Риск отсутствия возврата уплаченных сумм\n"
    "Пункт договора: 2.2\n"
    "Фрагмент договора: Оплаченные суммы возврату не подлежат\n"
    "Норма РТ: ГК РТ ст 451\n"
    "Почему риск: Удержание средств без компенсации\n"
    "Что исправить: Ввести условие возврата при расторжении договора\n\n"
    "Риск иностранного разрешения споров\n"
    "Пункт договора: 5.1\n"
    "Фрагмент договора: Споры разрешаются иностранным судом по государству X\n"
    "Норма РТ: ГК РТ ст 12\n"
    "Почему риск: Утрата прав перед российским судом\n"
    "Что исправить: Перевести разрешение споров в российскую юрисдикцию\n"
)
m8 = A.estimate_risk_metrics(braceless, contract_text=CONTRACT_TEXT,
                            contract_type_info=A.classify_contract_type(CONTRACT_TEXT))
results["8_recovered_count"] = len(m8["findings"])
results["8_severity_term"] = next((x["severity"] for x in m8["findings"] if "расторж" in x["title"].lower()), None)
results["8_severity_refund"] = next((x["severity"] for x in m8["findings"] if "возврат" in x["title"].lower()), None)
results["8_has_article450"] = any("Статья 450" in x.get("norm_text", "") for x in m8["findings"])
results["8_no_hallucination"] = not any("иностранн" in x["title"].lower() for x in m8["findings"])
log("8) bracket-less format recovered:", len(m8["findings"]), "findings; no_hallucination:",
    results["8_no_hallucination"])
log("   term=%s refund=%s art450=%s" % (results["8_severity_term"], results["8_severity_refund"],
                                        results["8_has_article450"]))

assert results["8_recovered_count"] >= 2, results
assert results["8_severity_term"] == "high", results["8_severity_term"]
assert results["8_severity_refund"] == "high", results["8_severity_refund"]
assert results["8_has_article450"] is True, results["8_has_article450"]
assert results["8_no_hallucination"] is True, results["8_no_hallucination"]

log("\nALL ASSERTS PASSED")

with open(os.path.join(os.path.dirname(os.path.abspath(__file__)), "tmp_unitcheck_out.txt"), "w", encoding="utf-8") as fh:
    fh.write(OUT.getvalue())