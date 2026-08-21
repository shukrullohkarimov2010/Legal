p = r"c:\OSPanel\home\LegalAI_Pro\python\app.py"
lines = open(p, encoding="utf-8").read().splitlines()
for i, l in enumerate(lines, 1):
    if ("@app.route" in l) or ("if __name__" in l) or ("run(" in l) or ("PORT" in l) or ("0.0.0.0" in l) or ("generate_sse" in l) or ("/analyze" in l) or ("sse" in l.lower()):
        print(i, "|", l)
print("TOTAL", len(lines))
