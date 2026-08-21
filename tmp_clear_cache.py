import os, pathlib, sys
root = pathlib.Path(r'c:\OSPanel\home\LegalAI_Pro')
analysis_cache = root / 'analysis_cache'
ollama_cache = root / '.ollama_cache.json'
removed = []
if analysis_cache.exists():
    for item in analysis_cache.rglob('*'):
        if item.is_file():
            item.unlink()
            removed.append(str(item.relative_to(root)))
    for item in sorted(analysis_cache.rglob('*'), reverse=True):
        if item.is_dir() and not any(item.iterdir()):
            item.rmdir()
            removed.append(str(item.relative_to(root)))
if ollama_cache.exists():
    ollama_cache.unlink()
    removed.append(str(ollama_cache.relative_to(root)))
print('removed:', len(removed))
for r in removed[:50]:
    print(r)
