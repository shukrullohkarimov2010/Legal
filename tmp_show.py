import io, sys
io.open = io.open
src = open('c:\\OSPanel\\home\\LegalAI_Pro\\python\\app.py', 'r', encoding='utf-8').read().splitlines()
out = []
for n in range(1675, 1742):
    if src[n].startswith('    # Парсер') or src[n].startswith('    print(f\"  [PARSER]'):
        mark = '>>'
    else:
        mark = '  '
    out.append('{:5d} {}| {}'.format(n+1, mark, repr(src[n])))
open('c:\\OSPanel\\home\\LegalAI_Pro\\tmp_splice_show.txt', 'w', encoding='utf-8').write('\n'.join(out))
print('wrote', len(out))
