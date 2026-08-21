import os
print('analysis_cache exists:', os.path.exists(r'c:\OSPanel\home\LegalAI_Pro\analysis_cache'))
print('.ollama_cache.json exists:', os.path.exists(r'c:\OSPanel\home\LegalAI_Pro\.ollama_cache.json'))
if os.path.exists(r'c:\OSPanel\home\LegalAI_Pro\analysis_cache'):
    print('analysis_cache files:', len([name for name in os.listdir(r'c:\OSPanel\home\LegalAI_Pro\analysis_cache') if os.path.isfile(os.path.join(r'c:\OSPanel\home\LegalAI_Pro\analysis_cache', name))]))
