import json
out = []
raw = open("c:\\OSPanel\\home\\LegalAI_Pro\\tmp_stream_v14.txt", "r", encoding="utf-8").read()
events = 0
chunk_preview = ""
chunk_len = 0
for line in raw.splitlines():
    if not line.startswith("data: "):
        continue
    try:
        o = json.loads(line[6:])
    except Exception:
        continue
    events += 1
    s = o.get("step")
    if s in ("chunk_done", "summary_start", "error"):
        out.append("event: %s keys=%s" % (s, ",".join(sorted(o.keys()))))
    if s == "chunk_done":
        chunk_len = len(o.get("content", "") or "")
        chunk_preview = (o.get("content", "") or "").replace("\n", " ")[:400]
out.append("CHUNK_DONE len=%s" % chunk_len)
out.append("CHUNK_PREVIEW=%s" % chunk_preview)
out.append("TOTAL_EVENTS=%s" % events)
open("c:\\OSPanel\\home\\LegalAI_Pro\\tmp_chunkcheck.txt", "w", encoding="utf-8").write("\n".join(out))
