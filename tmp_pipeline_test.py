import sys, os, io, json
sys.path.insert(0, os.path.join(os.path.dirname(os.path.abspath(__file__)), "python"))
import app as A

CONTRACT_PATH = "tmp_uploads/test_contract.txt"
contract_text = open(CONTRACT_PATH, encoding="utf-8").read()

CANNED = '''Риск 1
[Высокий] Одностороннее расторжение договора
Пункт договора: 3.1
Фрагмент договора: "Заказчик вправе расторгнуть договор в одностороннем порядке без объяснения причин в любое время"
Норма РТ: Глава 27, Статья 450
Почему риск: Отсутствие взаимного права расторжения ущемляет позиции исполнителя.
Что исправить: Добавить взачное право расторжения.

Риск 2
[Средний] Невозврат оплаченных сумм
Пункт договора: 2.2
Фрагмент договора: "Оплаченные суммы возврату не подлежат"
Норма РТ: Глава 27, Статья 450
Почему риск: Заказчик теряет деньги при досрочном прекращении.
Что исправить: Установить порядок возврата.

Риск 3
[Низкий] Ссылка на иностранную юрисдикцию
Пункт договора: 5.1
Фрагмент договора: "стороны несут ответственность согласно ГК РФ"
Норма РТ: Глава 1, Статья 104
Почему риск: Упоминание российского законодательства.
Что исправить: заменить на ГК РТ.
'''

def fake_send(prompt, stream_callback=None, num_predict=2048, temperature=0.5):
    if stream_callback:
        stream_callback(CANNED)
    return CANNED
A.send_to_ollama = fake_send

events = list(A.generate_sse(CONTRACT_PATH, "unit-pipeline-test", original_filename="test_contract.txt"))
complete = [e for e in events if '"step": "summary_complete"' in e or '"step":"summary_complete"' in e]
if not complete:
    print("NO SUMMARY EVENT")
    print(events[-1][:300] if events else "empty")
    raise SystemExit(1)

obj = json.loads(complete[0].replace("data: ", "").strip().split("\n")[0])
content = obj["content"]
metrics = obj["metrics"]
open("tmp_pipeline_report.md", "w", encoding="utf-8").write(content)

print("SCORE:", metrics["score"])
print("LEVEL:", metrics["level"])
print("VERDICT:", metrics["verdict"])
print("HIGH/MEDIUM/LOW:", metrics["high"], metrics["medium"], metrics["low"])
print("manual_review_required:", obj.get("metrics", {}).get("manual_review_required"))

# Check norm text attached
print("HAS_NORM_TEXT_BLOCK:", "Текст нормы из подключённого RAG" in content)
# Check the one-sided termination escalated to high and refund to high
findings = metrics  # not present fully in streamed metrics; parse content
risk_titles = content.split("#### ")
print("NUM RISK SECTIONS:", len([r for r in risk_titles if r.strip()]))
print("HAS_ARTICLE_450:", "Статья 450" in content)
print("HAS_WRONG_JURISDICTION_PENALTY (ГК РФ removed):", "ГК РФ" not in content and "ГК РТ" in content)

# Assert key behaviors of final pipeline
assert metrics["score"] > 0
assert metrics["verdict"] == "⚠️ Требует правок"
assert "Текст нормы из подключённого RAG" in content
assert "Глава 27, Статья 450" in content  # verified article present
assert "ГК РФ" not in content  # wrong jurisdiction stripped / not present
# The one-sided termination risk should be high (escalated or kept high)
assert content.count("**Высокий**") >= 1
# manual_review off for this long-but-having-risks contract
# Test a long contract that yields 0 findings -> manual review required
import copy
A2 = A
long_clean = ("ДОГОВОР ПОСТАВКИ\n" + "Предмет договора и цена и срок и ответственность и расторжение и споры и форс-мажор и уведомления и срок действия. " * 50)
def fake_send_empty(prompt, stream_callback=None, **kw):
    if stream_callback:
        stream_callback("Не выявлено рисков.")
    return "Не выявлено рисков."
A.send_to_ollama = fake_send_empty
events2 = list(A.generate_sse(CONTRACT_PATH, "unit-clean-test", original_filename="test_contract.txt"))  # reuse file path; contract is the same risky one, so better test manual_review directly via metrics func
