import json, re
raw = open("c:\\OSPanel\\home\\LegalAI_Pro\\tmp_stream_v14.txt", "r", encoding="utf-8").read()
rows = []
data = None
n_summaries = 0
last = None
for line in raw.splitlines():
    if line.startswith("data: "):
        try:
            o = json.loads(line[6:])
        except Exception:
            continue
        if o.get("step") == "summary_complete":
            n_summaries += 1
            last = o
            m = o.get("metrics", {})
            rows.append(f"#summary {n_summaries}: score={m.get('score')} level={m.get('level')} verdict={m.get('verdict')} high={m.get('high')} med={m.get('medium')} low={m.get('low')} len={len(o.get('content',''))}")
        elif o.get("step") in ("complete", "chunk_done", "status"):
            rows.append(f"#{o.get('step')} {o.get('status','')} ts={o.get('timestamp')}")

out = []
out.append("NUM summary_complete events: " + str(n_summaries))
out.extend(rows)
if last:
    m = last.get("metrics", {})
    c = last.get("content", "")
    open("c:\\OSPanel\\home\\LegalAI_Pro\\tmp_report_v14.md", "w", encoding="utf-8").write(c)
    out.append("FINAL score=%s level=%s verdict=%s H/M/L=%s/%s/%s" % (m.get("score"), m.get("level"), m.get("verdict"), m.get("high"), m.get("medium"), m.get("low")))
    out.append("HAS_NORM_TEXT_BLOCK=%s" % ("Текст нормы из подключённого RAG" in c))
    out.append("HAS_ARTICLE_450=%s" % ("Статья 450" in c))
    out.append("NUM_RISK_SECTIONS=%s" % c.count("#### "))
    out.append("HAS_ONE_SIDED=%s" % ("односторонн" in c.lower()))
    out.append("HAS_GK_RF=%s" % ("ГК РФ" in c))
    out.append("VERDICT_LINE=%s" % m.get("verdict"))
open("c:\\OSPanel\\home\\LegalAI_Pro\\tmp_parse_out.txt", "w", encoding="utf-8").write("\n".join(out))
print("wrote tmp_parse_out.txt")
