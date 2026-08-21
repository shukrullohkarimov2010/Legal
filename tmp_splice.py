import sys

path = "c:\\OSPanel\\home\\LegalAI_Pro\\python\\app.py"
frag = "c:\\OSPanel\\home\\LegalAI_Pro\\tmp_new_parser.txt"

with open(path, "r", encoding="utf-8") as f:
    lines = f.readlines()

# Замена строк 1680..1735 (1-индекс, вкл.) = индексы 1679..1734
START = 1679  # 0-based index of line 1680
END = 1735    # 1-based last line -> 0-based exclusive = 1735

# sanity checks (must match expected boundaries)
assert lines[START].lstrip().startswith("# Парсер рисков"), "start mismatch: " + repr(lines[START])
assert lines[END-1].lstrip().startswith('print(f"  [PARSER]'), "end mismatch: " + repr(lines[END-1])

with open(frag, "r", encoding="utf-8") as f:
    new_lines = f.readlines()

# ensure each new line keeps its trailing newline (readlines includes \n; last may lack)
if new_lines and not new_lines[-1].endswith("\n"):
    new_lines[-1] = new_lines[-1] + "\n"

before_len = len(lines)
new_file = lines[:START] + new_lines + lines[END:]
after_len = len(new_file)

with open(path, "w", encoding="utf-8") as f:
    f.writelines(new_file)

print("BEFORE_LINES:", before_len, "REPLACED_OLD:", END-START, "NEW:", len(new_lines), "AFTER_LINES:", after_len)
print("delta:", after_len - before_len)
