import re, pathlib, py_compile, sys
p = pathlib.Path(r'c:\OSPanel\home\LegalAI_Pro\python\app.py')
try:
    py_compile.compile(str(p), doraise=True)
    print('PYCOMPILE: OK')
except Exception as e:
    print('COMPILE_ERROR:', e)
    sys.exit(1)
s = p.read_text(encoding='utf-8')
print('ANALYSIS_VERSION =', re.search(r'ANALYSIS_VERSION = "(.*?)"', s).group(1))
print('RAG_TOP_K found =', 'RAG_TOP_K' in s)
print('RAG_SNIPPET_MAX found =', 'RAG_SNIPPET_MAX' in s)
print('ESSENTIAL_CLAUSE_PATTERNS keys =', len(re.findall(r'^\s+"[^"]+": \{', s, re.M)))
print('severity rules found =', 'Отсутствие гарантий' in s)
