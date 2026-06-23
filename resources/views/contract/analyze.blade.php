<!DOCTYPE html>
<html lang="ru" class="scroll-smooth dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="LegalAI — анализ юридических документов">
    <title>LegalAI Pro — Analyze</title>

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://unpkg.com/docx@8.2.3/build/index.umd.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body { font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }

        .glass { background: rgba(255,255,255,0.75); backdrop-filter: blur(12px); border: 1px solid rgba(226,232,240,0.6); }
        .dark .glass { background: rgba(30,41,59,0.75); border-color: rgba(51,65,85,0.6); }

        .terminal { background: linear-gradient(180deg,#1e293b 0%,#0f172a 100%); font-family: 'JetBrains Mono',monospace; box-shadow: inset 0 2px 4px rgba(0,0,0,0.3); }
        .terminal-line { animation: slideIn 0.2s ease-out; }
        @keyframes slideIn { from { opacity:0; transform:translateX(-8px); } to { opacity:1; transform:translateX(0); } }

        .progress-animated { background: linear-gradient(90deg,#3b82f6 0%,#60a5fa 50%,#3b82f6 100%); background-size: 200% 100%; animation: shimmer 2s linear infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

        .drop-zone { transition: all 0.25s cubic-bezier(0.4,0,0.2,1); border: 2px dashed #cbd5e1; }
        .dark .drop-zone { border-color: #475569; }
        .drop-zone:hover, .drop-zone.dragover { border-color: #3b82f6; background: rgba(59,130,246,0.06); transform: scale(1.01); }
        .dark .drop-zone:hover, .dark .drop-zone.dragover { background: rgba(59,130,246,0.12); }

        .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); }

        .toast { transform: translateX(120%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .toast.show { transform: translateX(0); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        .markdown-body { line-height: 1.7; color: inherit; }
        .markdown-body h1,.markdown-body h2,.markdown-body h3 { margin-top: 1.5em; margin-bottom: 0.75em; font-weight: 600; }
        .markdown-body h1 { font-size: 1.5rem; }
        .markdown-body h2 { font-size: 1.25rem; color: #1e40af; }
        .dark .markdown-body h2 { color: #60a5fa; }
        .markdown-body ul { list-style-type: disc; padding-left: 1.5em; margin: 0.5em 0; }
        .markdown-body li { margin: 0.25em 0; }
        .markdown-body code { background: rgba(148,163,184,0.15); padding: 0.2em 0.4em; border-radius: 0.25rem; font-family: 'JetBrains Mono',monospace; font-size: 0.9em; }
        .markdown-body pre { background: #1e293b; color: #e2e8f0; padding: 1em; border-radius: 0.5rem; overflow-x: auto; margin: 1em 0; }
        .markdown-body blockquote { border-left: 4px solid #3b82f6; padding-left: 1em; margin: 1em 0; color: #64748b; font-style: italic; }

        :focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }

        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover, .sidebar-item.active { background: rgba(59,130,246,0.15); color: #3b82f6; }
        .dark .sidebar-item:hover, .dark .sidebar-item.active { color: #60a5fa; }

        .risk-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; }
        .risk-low-badge { background: rgba(34,197,94,0.15); color: #16a34a; }
        .dark .risk-low-badge { color: #22c55e; }
        .risk-medium-badge { background: rgba(245,158,11,0.15); color: #d97706; }
        .dark .risk-medium-badge { color: #f59e0b; }
        .risk-high-badge { background: rgba(239,68,68,0.15); color: #dc2626; }
        .dark .risk-high-badge { color: #ef4444; }

        .risk-critical { background: rgba(239,68,68,0.12); color: #dc2626; }
        .dark .risk-critical { background: rgba(239,68,68,0.2); color: #fca5a5; }
        .risk-medium { background: rgba(245,158,11,0.12); color: #d97706; }
        .dark .risk-medium { background: rgba(245,158,11,0.2); color: #fcd34d; }
        .risk-low { background: rgba(34,197,94,0.12); color: #16a34a; }
        .dark .risk-low { background: rgba(34,197,94,0.2); color: #86efac; }

        .modal-overlay { opacity:0; visibility:hidden; transition: all 0.3s ease; }
        .modal-overlay.active { opacity:1; visibility:visible; }
        .modal-overlay .modal-content { transform: scale(0.95) translateY(10px); transition: all 0.3s cubic-bezier(0.4,0,0.2,1); }
        .modal-overlay.active .modal-content { transform: scale(1) translateY(0); }

        .filter-btn.active { background: #2563eb; color: white; }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes fadeIn { 0% { opacity:0; transform:translateY(8px); } 100% { opacity:1; transform:translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }

        .risk-gauge { position: relative; width: 130px; height: 130px; }
        .risk-gauge svg { transform: rotate(-90deg); }
        .risk-gauge circle { fill: none; stroke-width: 10; stroke-linecap: round; transition: stroke-dashoffset 1s ease, stroke 0.5s ease; }
        .risk-gauge .bg-circle { stroke: #e2e8f0; }
        .dark .risk-gauge .bg-circle { stroke: #334155; }

        .issue-row { border-left: 4px solid transparent; padding: 12px 16px; border-radius: 0 12px 12px 0; transition: all 0.2s ease; }
        .issue-row:hover { transform: translateX(4px); }
        .issue-row.critical { border-color: #ef4444; background: rgba(239,68,68,0.06); }
        .dark .issue-row.critical { background: rgba(239,68,68,0.12); }
        .issue-row.warning { border-color: #f59e0b; background: rgba(245,158,11,0.06); }
        .dark .issue-row.warning { background: rgba(245,158,11,0.12); }
        .issue-row.info { border-color: #3b82f6; background: rgba(59,130,246,0.06); }
        .dark .issue-row.info { background: rgba(59,130,246,0.12); }

        .dl-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; transition: all 0.2s ease; cursor: pointer; border: none; }
        .dl-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .dl-btn:active { transform: translateY(0); }
        .dl-pdf { background: #dc2626; color: white; }
        .dl-pdf:hover { background: #b91c1c; }
        .dl-docx { background: #2563eb; color: white; }
        .dl-docx:hover { background: #1d4ed8; }
        .dl-html { background: #64748b; color: white; }
        .dl-html:hover { background: #475569; }

        .doc-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .doc-table thead th {
            padding: 14px 20px; text-align: left; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em; color: #64748b;
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 2;
        }
        .dark .doc-table thead th { background: #0f172a; color: #94a3b8; border-bottom-color: #1e293b; }
        .doc-table tbody tr { transition: background 0.15s ease; cursor: pointer; }
        .doc-table tbody tr:hover { background: #f1f5f9; }
        .dark .doc-table tbody tr:hover { background: rgba(51,65,85,0.35); }
        .doc-table tbody td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .dark .doc-table tbody td { border-bottom-color: #1e293b; }
        .doc-table tbody tr:last-child td { border-bottom: none; }

        .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .status-completed { background: rgba(34,197,94,0.12); color: #16a34a; }
        .dark .status-completed { background: rgba(34,197,94,0.18); color: #4ade80; }
        .status-processing { background: rgba(59,130,246,0.12); color: #2563eb; }
        .dark .status-processing { background: rgba(59,130,246,0.18); color: #60a5fa; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-completed .status-dot { background: #22c55e; }
        .status-processing .status-dot { background: #3b82f6; animation: pulse-dot 1.5s ease-in-out infinite; }
        @keyframes pulse-dot { 0%,100% { opacity:1; } 50% { opacity:0.4; } }

        .risk-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .risk-pill-low { background: rgba(34,197,94,0.12); color: #16a34a; }
        .dark .risk-pill-low { background: rgba(34,197,94,0.18); color: #4ade80; }
        .risk-pill-medium { background: rgba(245,158,11,0.12); color: #d97706; }
        .dark .risk-pill-medium { background: rgba(245,158,11,0.18); color: #fbbf24; }
        .risk-pill-critical { background: rgba(239,68,68,0.12); color: #dc2626; }
        .dark .risk-pill-critical { background: rgba(239,68,68,0.18); color: #f87171; }
        .risk-pill-dot { width: 6px; height: 6px; border-radius: 50%; }
        .risk-pill-low .risk-pill-dot { background: #22c55e; }
        .risk-pill-medium .risk-pill-dot { background: #f59e0b; }
        .risk-pill-critical .risk-pill-dot { background: #ef4444; }

        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 10px; border: none; cursor: pointer; transition: all 0.15s ease; background: transparent; color: #94a3b8; }
        .action-btn:hover { background: #f1f5f9; color: #475569; }
        .dark .action-btn:hover { background: rgba(51,65,85,0.5); color: #e2e8f0; }
        .action-btn.action-delete:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
        .dark .action-btn.action-delete:hover { background: rgba(239,68,68,0.15); color: #f87171; }
        .action-btn.action-download:hover { background: rgba(59,130,246,0.1); color: #3b82f6; }
        .dark .action-btn.action-download:hover { background: rgba(59,130,246,0.15); color: #60a5fa; }

        .activity-bar-wrap { display: flex; flex-direction: column; align-items: center; gap: 6px; flex: 1; position: relative; }
        .activity-bar-track { width: 100%; flex: 1; display: flex; align-items: flex-end; justify-content: center; min-height: 80px; }
        .activity-bar { width: 70%; max-width: 40px; border-radius: 8px 8px 4px 4px; transition: all 0.5s cubic-bezier(0.4,0,0.2,1); position: relative; min-height: 6px; }
        .activity-bar:hover { filter: brightness(1.15); transform: scaleY(1.04); }
        .activity-bar-inner { width: 100%; height: 100%; border-radius: inherit; background: linear-gradient(180deg, #60a5fa 0%, #2563eb 100%); }
        .dark .activity-bar-inner { background: linear-gradient(180deg, #3b82f6 0%, #1d4ed8 100%); }
        .activity-bar.is-zero .activity-bar-inner { background: #e2e8f0; }
        .dark .activity-bar.is-zero .activity-bar-inner { background: #334155; }
        .activity-trend { display: inline-flex; align-items: center; gap: 2px; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 6px; line-height: 1; }
        .trend-up { background: rgba(34,197,94,0.12); color: #16a34a; }
        .dark .trend-up { background: rgba(34,197,94,0.18); color: #4ade80; }
        .trend-down { background: rgba(239,68,68,0.12); color: #dc2626; }
        .dark .trend-down { background: rgba(239,68,68,0.18); color: #f87171; }
        .trend-neutral { background: rgba(148,163,184,0.12); color: #94a3b8; }
        .dark .trend-neutral { background: rgba(148,163,184,0.15); color: #64748b; }
        .activity-day { font-size: 12px; font-weight: 600; color: #64748b; }
        .dark .activity-day { color: #94a3b8; }
        .activity-day.is-today { color: #2563eb; }
        .dark .activity-day.is-today { color: #60a5fa; }
        .activity-count { font-size: 14px; font-weight: 700; color: #1e293b; }
        .dark .activity-count { color: #f1f5f9; }
        .activity-count.is-zero { color: #94a3b8; font-weight: 500; }
        .dark .activity-count.is-zero { color: #475569; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased transition-colors duration-300">

@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : 'П';
@endphp

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col flex-shrink-0 transition-colors duration-300">
        <div class="p-6 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-xl font-bold text-slate-900 dark:text-white">LegalAI Pro</span>
        </div>
        <nav class="flex-1 px-4 space-y-1">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 px-3">Основное</div>
            <button onclick="showSection('dashboard')" class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400" id="nav-dashboard"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg><span>Дашборд</span></button>
            <button onclick="showSection('analysis')" class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400" id="nav-analysis"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg><span>Новый анализ</span></button>
            <button onclick="showSection('documents')" class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400" id="nav-documents"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>Документы</span></button>
            <button onclick="showSection('analytics')" class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400" id="nav-analytics"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg><span>Аналитика</span></button>
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3 mt-6 px-3">Система</div>
            <button onclick="showToast('Настройки в разработке','info')" class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 dark:text-slate-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Настройки</span></button>
        </nav>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg">{{ $userInitials }}</div>
                <div class="flex-1 min-w-0"><div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ $userName }}</div><div class="text-xs text-slate-500">Pro план</div></div>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white/60 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 backdrop-blur-xl flex-shrink-0 transition-colors">
            <div class="flex items-center gap-2">
                <button onclick="showSection('dashboard')" class="px-3 py-2 text-slate-600 dark:text-slate-400 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">Дашборд</button>
                <button onclick="showSection('analysis')" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">Анализ</button>
                <button onclick="showSection('documents')" class="px-3 py-2 text-slate-600 dark:text-slate-400 hover:text-blue-600 rounded-lg text-sm font-medium transition-colors">Документы</button>
            </div>
            <div class="flex items-center gap-3">
                <button id="themeToggle" class="p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" aria-label="Тема">
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-lg">{{ $userInitials }}</div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-slate-950 transition-colors">

            <!-- ===== DASHBOARD ===== -->
            <div id="dashboard-section" class="space-y-6">
                <div class="mb-8"><h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Дашборд</h1><p class="text-slate-500">Обзор юридических документов и аналитика рисков</p></div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all"><div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div class="text-3xl font-bold text-slate-900 dark:text-white" id="stat-total">0</div><div class="text-sm text-slate-500">Всего документов</div></div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all"><div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="text-3xl font-bold text-emerald-600 dark:text-emerald-400" id="stat-low">0</div><div class="text-sm text-slate-500">Низкий риск</div></div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all"><div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-500/20 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div><div class="text-3xl font-bold text-amber-600 dark:text-amber-400" id="stat-medium">0</div><div class="text-sm text-slate-500">Средний риск</div></div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all"><div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center mb-4"><svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="text-3xl font-bold text-red-600 dark:text-red-400" id="stat-critical">0</div><div class="text-sm text-slate-500">Высокий риск</div></div>
                </div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                <div><h3 class="text-lg font-semibold text-slate-900 dark:text-white">Последние документы</h3><p id="dashTableCount" class="text-xs text-slate-500">0 документов</p></div>
                            </div>
                            <button onclick="showSection('documents')" class="px-3 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-lg transition-colors">Смотреть все →</button>
                        </div>
                    </div>
                    <div id="dashTableWrap" class="overflow-x-auto">
                        <table class="doc-table"><thead><tr><th>Document</th><th>Status</th><th>Risk Level</th><th>Date</th><th style="text-align:right">Actions</th></tr></thead><tbody id="dashboardTableBody"></tbody></table>
                    </div>
                    <div id="dashboardEmpty" class="hidden py-12 text-center"><div class="text-5xl mb-4">📄</div><p class="text-slate-500 mb-4">Нет проанализированных документов</p><button onclick="showSection('analysis')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">Начать анализ</button></div>
                </div>
            </div>

            <!-- ===== ANALYSIS ===== -->
            <div id="analysis-section" class="hidden max-w-4xl mx-auto">
                <div class="mb-8"><h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Новый анализ</h1><p class="text-slate-500">Загрузите документ для проверки ИИ</p></div>
                <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 rounded-2xl shadow-xl overflow-hidden card-hover mb-6">
                    <div class="p-8">
                        <form id="uploadForm" class="space-y-6" novalidate>
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">Файл договора</label>
                                <div id="dropZone" class="drop-zone rounded-xl p-8 text-center cursor-pointer bg-slate-50 dark:bg-slate-800/30" role="button" tabindex="0">
                                    <input type="file" id="fileInput" class="sr-only" accept=".pdf,.doc,.docx,.txt" required>
                                    <div id="dropContent" class="space-y-4">
                                        <div class="w-16 h-16 mx-auto bg-blue-100 dark:bg-blue-500/20 rounded-2xl flex items-center justify-center"><svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                                        <div><p class="text-slate-800 dark:text-white font-medium mb-1">Перетащите файл сюда</p><p class="text-slate-500 text-sm">или нажмите для выбора</p></div>
                                        <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium shadow-lg shadow-blue-500/25">Выбрать файл</span>
                                        <p class="text-xs text-slate-500 mt-3">PDF, DOC, DOCX, TXT — Макс. 50 МБ</p>
                                    </div>
                                    <div id="fileSelected" class="hidden space-y-4">
                                        <div class="flex items-center gap-4">
                                            <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center"><svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                            <div class="text-left flex-1"><p id="selectedFileName" class="font-medium text-slate-800 dark:text-white"></p><p id="selectedFileSize" class="text-sm text-slate-500"></p></div>
                                            <button type="button" id="changeFile" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Изменить</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="submitBtn" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg transition-all flex items-center justify-center gap-3 group disabled:opacity-60 disabled:cursor-not-allowed"><span class="text-xl group-hover:scale-110 transition-transform">🚀</span><span>Запустить анализ</span><svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></button>
                        </form>
                    </div>
                </div>

                <section id="analysisProgress" class="space-y-6 hidden">
                    <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 rounded-xl p-4 flex items-center gap-4"><div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0"><svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div><div class="flex-1 min-w-0"><p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Анализируется</p><p id="analysisFileName" class="font-semibold text-slate-900 dark:text-white truncate"></p></div><span id="analysisStatusBadge" class="px-3 py-1.5 bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 rounded-full text-xs font-medium flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>В процессе</span></div>
                    <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 rounded-xl p-5"><div class="flex items-center justify-between mb-4"><span class="text-sm font-medium text-slate-700 dark:text-slate-300">Прогресс</span><span id="progressText" class="text-sm font-bold text-blue-600 dark:text-blue-400 tabular-nums">0%</span></div><div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"><div id="progressBar" class="progress-animated h-full rounded-full transition-all duration-500" style="width:0%"></div></div><div class="flex justify-between mt-4 text-xs text-slate-500"><span class="flex items-center gap-1"><span id="stage-upload" class="w-2 h-2 rounded-full bg-blue-500"></span>Загрузка</span><span class="flex items-center gap-1"><span id="stage-process" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>Обработка</span><span class="flex items-center gap-1"><span id="stage-analyze" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>Анализ</span><span class="flex items-center gap-1"><span id="stage-done" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>Готово</span></div></div>
                    <div class="terminal rounded-xl shadow-lg overflow-hidden"><div class="flex items-center justify-between px-4 py-3 bg-slate-800/80 border-b border-slate-700/50"><div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span><span class="w-3 h-3 rounded-full bg-yellow-500"></span><span class="w-3 h-3 rounded-full bg-green-500"></span></div><span class="text-xs text-slate-400 font-mono">live logs</span><button id="clearLogs" class="text-xs text-slate-400 hover:text-slate-200">✕</button></div><div id="logContainer" class="p-4 h-64 overflow-y-auto font-mono text-sm text-slate-300" style="scroll-behavior:smooth;"></div></div>

                    <article id="finalReport" class="hidden animate-fadeIn space-y-6">
                        <div id="reportPrintArea" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden">
                            <div id="reportHeaderBg" class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6 flex items-center gap-4">
                                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0"><svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                                <div><h3 class="text-white font-bold text-2xl">Анализ завершён</h3><p id="reportSubtitle" class="text-white/80 text-sm mt-1">Документ успешно обработан</p></div>
                            </div>
                            <div class="p-8">
                                <div class="grid md:grid-cols-3 gap-8 mb-8">
                                    <div class="flex flex-col items-center justify-center p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                                        <div class="risk-gauge mb-4">
                                            <svg viewBox="0 0 130 130" class="w-full h-full"><circle cx="65" cy="65" r="52" class="bg-circle"/><circle id="riskGaugeCircle" cx="65" cy="65" r="52" stroke="#22c55e" stroke-dasharray="326.7" stroke-dashoffset="326.7"/></svg>
                                            <div class="absolute inset-0 flex items-center justify-center flex-col"><span id="riskGaugeScore" class="text-4xl font-bold text-slate-900 dark:text-white">0</span><span class="text-xs text-slate-500 font-medium mt-1">Баллов риска</span></div>
                                        </div>
                                        <span id="riskLevelBadge" class="px-4 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">Низкий риск</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>Ключевые выявленные проблемы</h4>
                                        <div id="findingsTable" class="space-y-3 max-h-56 overflow-y-auto pr-2"></div>
                                    </div>
                                </div>
                                <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                                    <h4 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Полный отчёт</h4>
                                    <div id="reportContent" class="markdown-body text-slate-700 dark:text-slate-300 leading-relaxed"></div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm">
                            <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-4 uppercase tracking-wide">Скачать отчёт</h4>
                            <div class="flex flex-wrap gap-3">
                                <button onclick="downloadPDF()" class="dl-btn dl-pdf"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Скачать PDF</button>
                                <button onclick="downloadDOCX()" class="dl-btn dl-docx"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Скачать DOCX</button>
                                <button onclick="downloadHTML()" class="dl-btn dl-html"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>Скачать HTML</button>
                                <button id="newAnalysisBtn" class="dl-btn bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600 ml-auto">🔁 Новый анализ</button>
                            </div>
                        </div>
                    </article>
                </section>
            </div>

            <!-- ===== DOCUMENTS ===== -->
            <div id="documents-section" class="hidden">
                <div class="mb-8"><h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Мои документы</h1><p class="text-slate-500">История всех проанализированных документов</p></div>
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-200 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3"><div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/20 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div><h3 class="text-lg font-semibold text-slate-900 dark:text-white">История анализов</h3><p id="historyCount" class="text-xs text-slate-500">0 документов</p></div></div>
                            <div class="flex items-center gap-2"><div class="relative"><svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" id="historySearch" placeholder="Поиск..." class="pl-9 pr-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 w-48"></div><button id="clearAllHistory" class="p-2 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors" title="Очистить всё"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div>
                        </div>
                        <div class="flex items-center gap-2 mt-4">
                            <button class="filter-btn active px-3 py-1.5 rounded-lg text-xs font-medium" data-filter="all">Все</button>
                            <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400" data-filter="critical">🔴 Критические</button>
                            <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400" data-filter="medium">🟡 Средний</button>
                            <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400" data-filter="low">🟢 Низкий</button>
                        </div>
                    </div>
                    <div id="historyTableWrap" class="overflow-x-auto"><table class="doc-table"><thead><tr><th>Document</th><th>Status</th><th>Risk Level</th><th>Date</th><th style="text-align:right">Actions</th></tr></thead><tbody id="historyTableBody"></tbody></table></div>
                    <div id="historyEmpty" class="hidden py-16 text-center"><div class="text-5xl mb-4">📂</div><h4 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">История пуста</h4><p class="text-sm text-slate-500 max-w-sm mx-auto">Загрузите первый договор для анализа</p><button onclick="showSection('analysis')" class="mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">Начать анализ</button></div>
                </div>
            </div>

            <!-- ===== ANALYTICS ===== -->
            <div id="analytics-section" class="hidden">
                <div class="mb-8"><h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Аналитика</h1><p class="text-slate-500">Статистика по вашим документам</p></div>
                <div id="analyticsContent" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6"><h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Распределение рисков</h3><div id="riskDistributionChart" class="space-y-4"></div></div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6"><h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Активность (7 дней)</h3><div id="activityChart" class="pt-2"></div></div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6"><h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Типы файлов</h3><div id="fileTypesChart" class="space-y-3"></div></div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6"><h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Общая статистика</h3><div id="summaryStats" class="space-y-3"></div></div>
                </div>
                <div id="analyticsEmpty" class="hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center"><div class="text-5xl mb-4">📊</div><h3 class="text-xl font-semibold text-slate-700 dark:text-slate-300 mb-2">Недостаточно данных</h3><p class="text-slate-500">Проанализируйте документ</p><button onclick="showSection('analysis')" class="mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">Начать</button></div>
            </div>
        </main>
    </div>
</div>

<div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

<div id="reportModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" aria-hidden="true">
    <div class="modal-content bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700"><div><h3 id="modal-title" class="text-lg font-bold text-slate-900 dark:text-white"></h3><p id="modal-meta" class="text-xs text-slate-500 mt-0.5"></p></div><button id="modalClose" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
        <div id="modalBody" class="p-6 overflow-y-auto flex-1"><div id="modalReportContent"></div></div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex flex-wrap gap-2">
            <button id="modalPdfBtn" class="dl-btn dl-pdf text-sm !py-2 !px-4"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>PDF</button>
            <button id="modalDocxBtn" class="dl-btn dl-docx text-sm !py-2 !px-4"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>DOCX</button>
            <button id="modalCopyBtn" class="dl-btn bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 text-sm !py-2 !px-4"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>Копировать</button>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" aria-hidden="true">
    <div class="modal-content bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center border border-slate-200 dark:border-slate-700">
        <div class="w-14 h-14 mx-auto mb-4 bg-red-100 dark:bg-red-500/20 rounded-2xl flex items-center justify-center"><svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div>
        <h4 id="confirmTitle" class="text-lg font-bold text-slate-900 dark:text-white mb-2"></h4><p id="confirmMessage" class="text-sm text-slate-500 mb-6"></p>
        <div class="flex gap-3"><button id="confirmCancel" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium">Отмена</button><button id="confirmOk" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium">Удалить</button></div>
    </div>
</div>

<script>
    const PYTHON_API = "{{ $pythonApiUrl ?? 'http://127.0.0.1:5000' }}";
    const MAX_FILE_SIZE = 50*1024*1024;
    const ALLOWED_TYPES = ['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain'];
    const HISTORY_KEY = 'legalai_history';
    const CABINET_ACTIVITY_URL = '{{ route('cabinet.activity.store') }}';
    let eventSource=null,isAnalyzing=false,currentFilter='all',historyItems=[],currentModalReport=null,lastReportRisk='low',lastReportContent='';

    // ===== THEME =====
    function initTheme(){if(localStorage.theme==='dark'||(!('theme'in localStorage)&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark');else document.documentElement.classList.remove('dark');}
    function toggleTheme(){document.documentElement.classList.toggle('dark');localStorage.theme=document.documentElement.classList.contains('dark')?'dark':'light';if(!document.getElementById('analytics-section').classList.contains('hidden'))updateAnalytics();}
    initTheme();

    // ===== UTILS =====
    function showToast(m,t='info',d=4000){const c=document.getElementById('toastContainer'),e=document.createElement('div');const s={info:'bg-slate-800 text-white',success:'bg-emerald-600 text-white',error:'bg-red-600 text-white',warning:'bg-amber-500 text-white'};const i={info:'ℹ️',success:'✅',error:'❌',warning:'⚠️'};e.className=`toast flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl ${s[t]} min-w-[280px] max-w-sm`;e.innerHTML=`<span class="text-lg">${i[t]}</span><span class="text-sm font-medium flex-1">${m}</span><button class="ml-2 opacity-70 hover:opacity-100">&times;</button>`;c.appendChild(e);requestAnimationFrame(()=>e.classList.add('show'));const to=setTimeout(()=>{e.classList.remove('show');setTimeout(()=>e.remove(),300);},d);e.querySelector('button').onclick=()=>{clearTimeout(to);e.classList.remove('show');setTimeout(()=>e.remove(),300);};}
    const formatSize=b=>{if(b===0)return'0 Б';const k=1024,s=['Б','КБ','МБ','ГБ'],i=Math.floor(Math.log(b)/Math.log(k));return parseFloat((b/Math.pow(k,i)).toFixed(2))+' '+s[i];};
    const escapeHtml=s=>{if(!s)return'';const d=document.createElement('div');d.textContent=s;return d.innerHTML;};
    const getFileIcon=f=>{const e=(f||'').split('.').pop().toLowerCase();return{pdf:'📕',doc:'📘',docx:'📘',txt:'📄'}[e]||'📁';};
    async function recordCabinetActivity(payload){const token=document.querySelector('meta[name="csrf-token"]')?.content;if(!token)return;try{await fetch(CABINET_ACTIVITY_URL,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token},body:JSON.stringify(payload)});}catch(e){console.warn('Cabinet history error:',e);}}
    const getFileExtension=f=>{return (f||'').split('.').pop().toUpperCase();};
    const getTimeAgo=d=>{const n=new Date(),s=Math.floor((n-d)/1000);if(s<60)return'только что';if(s<3600)return`${Math.floor(s/60)} мин. назад`;if(s<86400)return`${Math.floor(s/3600)} ч. назад`;if(s<604800)return`${Math.floor(s/86400)} дн. назад`;return d.toLocaleDateString('ru-RU');};
    function log(m,t='info'){const lc=document.getElementById('logContainer');if(!lc)return;const c={info:{color:'#94a3b8',icon:'•'},status:{color:'#7dd3fc',icon:'📌'},success:{color:'#86efac',icon:'✅'},warning:{color:'#fcd34d',icon:'⚠️'},error:{color:'#f87171',icon:'❌'},chunk:{color:'#d8b4fe',icon:'📋'}};const{color,icon}=c[t]||c.info;const d=document.createElement('div');d.className='terminal-line py-1.5 px-2 rounded hover:bg-white/5';d.innerHTML=`<span class="text-slate-500">[${new Date().toLocaleTimeString('ru-RU')}]</span><span style="color:${color}" class="ml-2">${icon}</span><span style="color:${color}" class="ml-1.5">${m}</span>`;lc.appendChild(d);lc.scrollTop=lc.scrollHeight;}

    // ===== RISK HELPERS =====
    function getRiskColor(r){return r==='critical'?'#ef4444':r==='medium'?'#f59e0b':'#22c55e';}
    function getRiskGradient(r){return r==='critical'?'from-red-500 to-rose-600':r==='medium'?'from-amber-500 to-orange-600':'from-green-500 to-emerald-600';}
    function getRiskLabel(r){return r==='critical'?'Высокий риск':r==='medium'?'Средний риск':'Низкий риск';}
    function getRiskBadgeFull(r){return r==='critical'?'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400':r==='medium'?'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400':'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400';}
    function scoreToLevel(s){return s>66?'critical':s>33?'medium':'low';}

    // Получить балл из записи истории (реальный, не фейковый)
    function getItemScore(item){return typeof item.riskScore==='number'?item.riskScore:(item.riskLevel==='critical'?75:item.riskLevel==='medium'?45:15);}

    function buildGaugeSVG(score, size=130, strokeW=10){
        const r = (size/2) - strokeW - 4;
        const circ = 2 * Math.PI * r;
        const pct = Math.min(score,100)/100;
        const offset = circ - (pct * circ);
        const level = scoreToLevel(score);
        const color = getRiskColor(level);
        return `<svg viewBox="0 0 ${size} ${size}" class="w-full h-full" style="transform:rotate(-90deg)"><circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="${document.documentElement.classList.contains('dark')?'#334155':'#e2e8f0'}" stroke-width="${strokeW}" stroke-linecap="round"/><circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="${color}" stroke-width="${strokeW}" stroke-dasharray="${circ}" stroke-dashoffset="${offset}" stroke-linecap="round" style="transition:stroke-dashoffset 1s ease,stroke 0.5s ease"/></svg>`;
    }

    // =================================================================
    //  REAL RISK ANALYSIS ENGINE — анализирует реальный контент отчёта
    // =================================================================

    function analyzeReportRisk(content) {
        if (!content || !content.trim()) return { score: 0, level: 'low', findings: { critical: 0, warning: 0, info: 0 } };

        const lower = content.toLowerCase();

        // ===== 1. EXPLICIT NUMERIC SCORE (самый точный источник) =====
        const scorePatterns = [
            /(?:общий\s+)?(?:балл|оценка|индекс|показатель|уровень)\s*(?:риска|опасности)?[\s:‑–=]*?(\d{1,3})(?:\s*[\/из]\s*100)?/i,
            /(?:итого|итог|всего)[\s:‑–=]*?(\d{1,3})(?:\s*[\/из]\s*100)?\s*(?:балл|очк|point)/i,
            /risk\s*(?:score|level|rating)[\s:‑–=]*?(\d{1,3})/i,
            /(\d{1,3})\s*\/\s*100/i,
            /(\d{1,3})\s*из\s*100/i,
            /(?:риск|risk)[\s:‑–=]*?(\d{1,3})(?:\s*%|\s*процент)/i,
            /(?:оценка|score)[\s:‑–=]*?(\d{1,3})/i,
        ];
        for (const p of scorePatterns) {
            const m = lower.match(p);
            if (m) {
                const s = parseInt(m[1]);
                if (s >= 1 && s <= 100) {
                    log(`📊 Найден явный балл: ${s}/100`, 'status');
                    return { score: s, level: scoreToLevel(s), findings: countFindings(content) };
                }
            }
        }

        // ===== 2. EXPLICIT LEVEL LABEL =====
        const levelMap = [
            { patterns: [/(?:уровень|степень|категория)\s*(?:риска|опасности|угрозы)?[\s:‑–=]*\s*(критическ|высок)/i, /(?:общая\s+)?оценка[\s:‑–=]*\s*(критическ|высок)/i, /risk\s*level[\s:‑–=]*\s*(critical|high)/i, /overall\s*risk[\s:‑–=]*\s*(critical|high)/i], level: 'critical', baseScore: 78 },
            { patterns: [/(?:уровень|степень|категория)\s*(?:риска|опасности|угрозы)?[\s:‑–=]*\s*средн/i, /(?:общая\s+)?оценка[\s:‑–=]*\s*средн/i, /risk\s*level[\s:‑–=]*\s*medium/i, /overall\s*risk[\s:‑–=]*\s*medium/i], level: 'medium', baseScore: 48 },
            { patterns: [/(?:уровень|степень|категория)\s*(?:риска|опасности|угрозы)?[\s:‑–=]*\s*низк/i, /(?:общая\s+)?оценка[\s:‑–=]*\s*низк/i, /risk\s*level[\s:‑–=]*\s*low/i, /overall\s*risk[\s:‑–=]*\s*low/i], level: 'low', baseScore: 12 },
        ];
        for (const lm of levelMap) {
            for (const p of lm.patterns) {
                if (lower.match(p)) {
                    const findings = countFindings(content);
                    // Корректируем балл на основе количества проблем
                    const adj = Math.min(20, findings.critical * 4 + findings.warning * 2);
                    const score = Math.min(100, lm.baseScore + adj);
                    log(`📊 Найден явный уровень: ${lm.level}, расчётный балл: ${score}`, 'status');
                    return { score, level: lm.level, findings };
                }
            }
        }

        // ===== 3. COUNT ISSUES BY SEVERITY =====
        const findings = countFindings(content);

        // ===== 4. KEYWORD FREQUENCY ANALYSIS =====
        const criticalKw = [
            'существенн','критич','груб','серьёзн','серьезн',
            'незакон','противоречи','нарушен','угроз','опасн',
            'ущерб','ответственност','штраф','пен','убытк',
            'расторжен','недействительн','ничтожн','оспорим',
            'принудительн','кабальн','мошенническ','взыска',
            'подложн','фальсифиц','обман','злоупотреблен',
        ];
        const warningKw = [
            'рекоменду','желательн','целесообразн','недостаточн',
            'неопределённ','неопределенн','неясн','размыт','неточн',
            'рискованн','сомнительн','двусмысленн','противоречив',
            'отсутстви','пропущен','не указан','не предусмотрен',
            'дискриминацион','ущемля','ограничива','неравноправн',
            'не регламент','не закреплён','не закреплен','не определён','не определен',
            'может повлечь','может привести','может стать','может вызвать',
            'пробел','упущен','не отражён','не отражен',
        ];
        const safeKw = [
            'соответстви','надёжн','надежн','защищён','защищен','гарантир',
            'безопасн','стандартн','типов','корректн',
            'законн','правомерн','допустим','оптимальн',
        ];

        let critKwCount = 0, warnKwCount = 0, safeKwCount = 0;
        for (const kw of criticalKw) { const m = lower.match(new RegExp(kw, 'g')); critKwCount += m ? m.length : 0; }
        for (const kw of warningKw) { const m = lower.match(new RegExp(kw, 'g')); warnKwCount += m ? m.length : 0; }
        for (const kw of safeKw) { const m = lower.match(new RegExp(kw, 'g')); safeKwCount += m ? m.length : 0; }

        // ===== 5. WEIGHTED SCORE CALCULATION =====
        let score = 0;

        // Проблемы из списков (основной вес)
        score += Math.min(40, findings.critical * 12);
        score += Math.min(25, findings.warning * 5);
        score += Math.min(8, findings.info * 1.5);

        // Ключевые слова (дополнительный вес)
        score += Math.min(18, critKwCount * 2.5);
        score += Math.min(12, warnKwCount * 1.2);

        // Безопасные ключевые слова снижают балл
        score -= Math.min(10, safeKwCount * 1.5);

        // Округляем и ограничиваем
        score = Math.max(0, Math.min(100, Math.round(score)));

        const level = scoreToLevel(score);

        log(`📊 Расчётный балл: ${score}/100 (${level}) | критич: ${findings.critical}, предост: ${findings.warning}, инфо: ${findings.info} | KW: крит=${critKwCount}, пред=${warnKwCount}, безоп=${safeKwCount}`, 'status');

        return { score, level, findings };
    }

    function countFindings(content) {
        let critical = 0, warning = 0, info = 0;
        const lines = content.split('\n');

        for (const line of lines) {
            const trimmed = line.trim();
            // Анализируем только элементы списков
            const isListItem = trimmed.match(/^[-*•]\s/) || trimmed.match(/^\d+[.)]\s/);
            if (!isListItem) continue;

            const t = trimmed.toLowerCase();

            // ---- CRITICAL ----
            if (t.match(/🔴/)) { critical++; continue; }
            if (t.match(/❗|‼️/)) { critical++; continue; }
            if (t.match(/критическ(?:ий|ая|ое|ие|им|их|ую|ого)/)) { critical++; continue; }
            if (t.match(/существенн(?:ый|ая|ое|ые|ым|ыми|ых|ую|ого)/)) { critical++; continue; }
            if (t.match(/серьёзн(?:ый|ая|ое|ые|ым|ую|ого)/)) { critical++; continue; }
            if (t.match(/серьезн(?:ий|ая|ое|ые|ым|ую|ого)/)) { critical++; continue; }
            if (t.match(/груб(?:ое|ая|ые|ым|ого|ыми)?\s*(?:нарушен|ошибк|искажен|отклонен)/)) { critical++; continue; }
            if (t.match(/незаконн(?:ый|ая|ое|ые|ым|ыми|ая|ого)/)) { critical++; continue; }
            if (t.match(/противоречи\w+\s*(?:закону|законодательств|норматив|гк|кодекс)/)) { critical++; continue; }
            if (t.match(/срочно\s*(?:требу|исправлен|вниман|необходим|рекоменду)/)) { critical++; continue; }
            if (t.match(/влечёт?\s+(?:расторжен|признан\s+недейств|аннулирован|односторонн)/)) { critical++; continue; }
            if (t.match(/(?:может\s+)?повлечь\s+(?:существенн|значительн|критич|серьёзн|серьезн)/)) { critical++; continue; }
            if (t.match(/(?:может\s+)?привести\s+к\s+(?:расторжен|признан|аннулирован|судебн)/)) { critical++; continue; }
            if (t.match(/(?:прямо\s+)?нарушен(?:ие|ия|ию|ием)\s+(?:закона|прав|гк|кодекс|норматив)/)) { critical++; continue; }
            if (t.match(/полностью?\s+(?:отсутству|исключ|устранен|игнорир)/)) { critical++; continue; }
            if (t.match(/никакой?\s+(?:ответственност|гаранти|защит|компенсац)/)) { critical++; continue; }

            // ---- WARNING ----
            if (t.match(/🟡/)) { warning++; continue; }
            if (t.match(/⚠️/)) { warning++; continue; }
            if (t.match(/средн(?:ий|яя|ее|ие|им|их|ая)\s*(?:риск|степен|уровен|тяжесть|категор)/)) { warning++; continue; }
            if (t.match(/рекоменду\w+\s*(?:исправлен|изменен|добавлен|уточнен|включен|предусмотр|закрепл)/)) { warning++; continue; }
            if (t.match(/недостаточн(?:ый|ая|ое|ые|ым|ыми|ая|ого)\s*(?:защит|услов|гаранти|регламент|оговор|основан)/)) { warning++; continue; }
            if (t.match(/не\s+(?:указан|определён|определен|прописан|предусмотрен|оговорён|оговорен|установлен|закреплён|закреплен|регламентирован|урегулирован)/)) { warning++; continue; }
            if (t.match(/отсутстви(?:е|и|ю|ем|я)\s*(?:услов|оговор|гаранти|ответственност|штраф|пен|санкц|компенсац|регламент|положен)/)) { warning++; continue; }
            if (t.match(/размыт|неясн|неточн|двусмысленн|противоречив|сомнительн|неоднозначн/)) { warning++; continue; }
            if (t.match(/может\s+(?:повлечь|привести|стать|вызвать|создать|спровоциров)\s+(?:риск|проблем|недоразумени|спор|ситуац|сложност|конфликт)/)) { warning++; continue; }
            if (t.match(/рискованн|осторожн|внимательн|осмотительн/)) { warning++; continue; }
            if (t.match(/пробел|упущен|не\s+достаточн|не\s+полност/)) { warning++; continue; }
            if (t.match(/желательн\s+(?:включ|добав|уточн|измен|предусмотр)/)) { warning++; continue; }
            if (t.match(/(?:не\s+)?достаточн(?:о|ый|ая|ое|ые)/)) { if (!t.match(/недостаточн/)) { warning++; continue; } }

            // ---- INFO ----
            if (t.match(/🔵/)) { info++; continue; }
            if (t.match(/🟢|✅|✔️|☑️/)) { info++; continue; }
            if (t.match(/рекоменду/i)) { info++; continue; }
            if (t.match(/желательн|целесообразн|полезн|предпочтительн|оптимальн/)) { info++; continue; }
            if (t.match(/соответству\w+\s*(?:закону|стандарт|требовани|норматив|гк|кодекс)/)) { info++; continue; }
            if (t.match(/допустим|приемлем|корректн|правомерн/)) { info++; continue; }
        }

        return { critical, warning, info };
    }

    function parseFindings(content){
        const lines=content.split('\n').filter(l=>l.trim().match(/^[-*•]\s/)||l.trim().match(/^\d+[.)]\s/));
        return lines.slice(0,8).map(f=>{
            const text=f.replace(/^[-*•\d.)]\s*/,'').trim();
            const tl=text.toLowerCase();
            // Классификация — используем те же правила что и в countFindings
            let type='info';
            const isCritical=tl.match(/🔴/)||tl.match(/❗|‼️/)||tl.match(/критическ/)||tl.match(/существенн/)||tl.match(/серьёзн|серьезн/)||tl.match(/груб(?:ое|ая|ые|ым|ого|ыми)?\s*(?:нарушен|ошибк)/)||tl.match(/незаконн/)||tl.match(/срочно\s*(?:требу|исправлен|вниман|необходим)/)||tl.match(/влечёт?\s+(?:расторжен|признан)/)||tl.match(/(?:может\s+)?повлечь\s+(?:существенн|значительн|критич|серьёзн|серьезн)/);
            const isWarning=tl.match(/🟡/)||tl.match(/⚠️/)||tl.match(/средн\s*(?:риск|степен|уровен)/)||tl.match(/рекоменду\w+\s*(?:исправлен|изменен|добавлен|уточнен|включен|предусмотр)/)||tl.match(/недостаточн\s*(?:защит|услов|гаранти|регламент)/)||tl.match(/не\s+(?:указан|определён|определен|прописан|предусмотрен|оговорён|оговорен|установлен|закреплён|закреплен|регламентирован)/)||tl.match(/отсутстви(?:е|и|ю|ем|я)\s*(?:услов|оговор|гаранти|ответственност|штраф|пен|санкц|компенсац|регламент)/)||tl.match(/размыт|неясн|неточн|двусмысленн|противоречив|сомнительн|неоднозначн/)||tl.match(/может\s+(?:повлечь|привести|стать|вызвать|создать)\s+(?:риск|проблем|недоразумени|спор)/)||tl.match(/рискованн|осторожн|внимательн/);

            if(isCritical) type='critical';
            else if(isWarning) type='warning';
            const icons={critical:'🔴',warning:'🟡',info:'🔵'};
            return{text,type,icon:icons[type]};
        });
    }

    function buildFindingsHTML(findings){
        if(!findings.length) return '<div class="text-center py-4 text-slate-500">Проблемы не обнаружены</div>';
        return findings.map(f=>`<div class="issue-row ${f.type}"><div class="flex items-start gap-3"><span class="flex-shrink-0 text-lg">${f.icon}</span><p class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed">${escapeHtml(f.text)}</p></div></div>`).join('');
    }

    // ===== SHARED TABLE ROW BUILDER =====
    function buildTableRow(item){
        const date=new Date(item.createdAt);
        const ext=getFileExtension(item.filename);
        const icon=getFileIcon(item.filename);
        const status=item.status||'completed';
        const isCompleted=status==='completed';
        const statusBadge=isCompleted
            ?`<span class="status-badge status-completed"><span class="status-dot"></span>Completed</span>`
            :`<span class="status-badge status-processing"><span class="status-dot"></span>Processing</span>`;
        const riskClass=item.riskLevel==='critical'?'risk-pill-critical':item.riskLevel==='medium'?'risk-pill-medium':'risk-pill-low';
        const riskLabel=item.riskLevel==='critical'?'High':item.riskLevel==='medium'?'Medium':'Low';
        const score=getItemScore(item);
        const riskPill=`<span class="risk-pill ${riskClass}"><span class="risk-pill-dot"></span>${riskLabel} · ${score}</span>`;
        return `<tr onclick="openReportModal('${item.id}')">
            <td><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-lg flex-shrink-0">${icon}</div><div class="min-w-0"><p class="font-medium text-slate-900 dark:text-white truncate max-w-[280px]">${escapeHtml(item.filename)}</p><p class="text-xs text-slate-500 mt-0.5">${item.fileSize||''} · .${ext}</p></div></div></td>
            <td>${statusBadge}</td>
            <td>${riskPill}</td>
            <td><div><p class="text-sm text-slate-700 dark:text-slate-300">${date.toLocaleDateString('ru-RU')}</p><p class="text-xs text-slate-500 mt-0.5">${getTimeAgo(date)}</p></div></td>
            <td><div class="flex items-center justify-end gap-1">
                <button onclick="event.stopPropagation();downloadItemPDF('${item.id}')" class="action-btn action-download" title="PDF"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg></button>
                <button onclick="event.stopPropagation();downloadItemDOCX('${item.id}')" class="action-btn action-download" title="DOCX"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></button>
                <button onclick="event.stopPropagation();confirmDeleteItem('${item.id}')" class="action-btn action-delete" title="Удалить"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div></td>
        </tr>`;
    }

    // ===== HISTORY =====
    const HM={
        load(){try{historyItems=JSON.parse(localStorage.getItem(HISTORY_KEY)||'[]');}catch{historyItems=[];}},
        save(){try{localStorage.setItem(HISTORY_KEY,JSON.stringify(historyItems));}catch{}},
        add(i){historyItems.unshift({...i,id:Date.now().toString(36)+Math.random().toString(36).substr(2,5),createdAt:new Date().toISOString(),status:i.status||'completed'});if(historyItems.length>50)historyItems=historyItems.slice(0,50);this.save();renderHistory();updateDashboardStats();},
        remove(id){historyItems=historyItems.filter(i=>i.id!==id);this.save();renderHistory();updateDashboardStats();},
        clearAll(){historyItems=[];this.save();renderHistory();updateDashboardStats();},
        get(id){return historyItems.find(i=>i.id===id);},
        getFiltered(){let f=[...historyItems];const s=(document.getElementById('historySearch')?.value||'').toLowerCase().trim();if(s)f=f.filter(i=>i.filename.toLowerCase().includes(s));if(currentFilter!=='all')f=f.filter(i=>i.riskLevel===currentFilter);return f;}
    };

    // ===== NAVIGATION =====
    const sections=['dashboard','analysis','documents','analytics'];
    function showSection(sec){sections.forEach(s=>document.getElementById(s+'-section')?.classList.add('hidden'));document.getElementById(sec+'-section')?.classList.remove('hidden');document.querySelectorAll('.sidebar-item').forEach(i=>i.classList.remove('active'));document.getElementById('nav-'+sec)?.classList.add('active');if(sec==='dashboard')updateDashboardStats();if(sec==='documents')renderHistory();if(sec==='analytics')updateAnalytics();}

    // ===== DASHBOARD =====
    function updateDashboardStats(){
        const t=historyItems.length,cr=historyItems.filter(i=>i.riskLevel==='critical').length,m=historyItems.filter(i=>i.riskLevel==='medium').length,lw=historyItems.filter(i=>i.riskLevel==='low').length;
        document.getElementById('stat-total').textContent=t;document.getElementById('stat-low').textContent=lw;document.getElementById('stat-medium').textContent=m;document.getElementById('stat-critical').textContent=cr;
        renderDashboardTable();
    }
    function renderDashboardTable(){
        const tbody=document.getElementById('dashboardTableBody'),countEl=document.getElementById('dashTableCount'),emptyEl=document.getElementById('dashboardEmpty'),wrapEl=document.getElementById('dashTableWrap');
        const t=historyItems.length;
        countEl.textContent=`${t} документ${t===1?'':t<5?'а':'ов'}`;
        if(!t){emptyEl.classList.remove('hidden');wrapEl.classList.add('hidden');return;}
        emptyEl.classList.add('hidden');wrapEl.classList.remove('hidden');
        tbody.innerHTML=historyItems.slice(0,6).map(i=>buildTableRow(i)).join('');
    }

    // ===== DOCUMENTS =====
    function renderHistory(){
        const f=HM.getFiltered(),t=historyItems.length;
        document.getElementById('historyCount').textContent=`${t} документ${t===1?'':t<5?'а':'ов'}`;
        if(!t){document.getElementById('historyEmpty').classList.remove('hidden');document.getElementById('historyTableWrap').classList.add('hidden');return;}
        document.getElementById('historyEmpty').classList.add('hidden');document.getElementById('historyTableWrap').classList.remove('hidden');
        if(!f.length){document.getElementById('historyTableBody').innerHTML=`<tr><td colspan="5" class="py-12 text-center text-slate-500">Ничего не найдено</td></tr>`;return;}
        document.getElementById('historyTableBody').innerHTML=f.map(i=>buildTableRow(i)).join('');
    }

    // ===== ANALYTICS =====
    function updateAnalytics(){
        const content=document.getElementById('analyticsContent'),empty=document.getElementById('analyticsEmpty');
        if(!historyItems.length){content.classList.add('hidden');empty.classList.remove('hidden');return;}
        content.classList.remove('hidden');empty.classList.add('hidden');
        const total=historyItems.length,cr=historyItems.filter(i=>i.riskLevel==='critical').length,md=historyItems.filter(i=>i.riskLevel==='medium').length,lw=historyItems.filter(i=>i.riskLevel==='low').length;
        const pct=v=>total>0?Math.round(v/total*100):0;
        document.getElementById('riskDistributionChart').innerHTML=`<div><div class="flex justify-between text-sm mb-1"><span class="font-medium">🟢 Низкий</span><span class="text-slate-500">${lw} (${pct(lw)}%)</span></div><div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width:${pct(lw)}%"></div></div></div><div><div class="flex justify-between text-sm mb-1"><span class="font-medium">🟡 Средний</span><span class="text-slate-500">${md} (${pct(md)}%)</span></div><div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"><div class="h-full bg-amber-500 rounded-full transition-all duration-500" style="width:${pct(md)}%"></div></div></div><div><div class="flex justify-between text-sm mb-1"><span class="font-medium">🔴 Высокий</span><span class="text-slate-500">${cr} (${pct(cr)}%)</span></div><div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"><div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width:${pct(cr)}%"></div></div></div>`;

        const days=[];const now=new Date();for(let i=6;i>=0;i--){const d=new Date(now);d.setDate(d.getDate()-i);days.push(d.toISOString().split('T')[0]);}
        const actData=days.map(d=>({date:d,count:historyItems.filter(i=>i.createdAt.startsWith(d)).length}));
        const maxAct=Math.max(...actData.map(d=>d.count),1);
        const avgCount=actData.reduce((a,b)=>a+b.count,0)/actData.length;
        const todayStr=now.toISOString().split('T')[0];
        document.getElementById('activityChart').innerHTML=`<div class="flex items-end gap-3" style="height:200px;position:relative;">${actData.map((d,idx)=>{
            const h=d.count>0?Math.max((d.count/maxAct)*100,8):5;
            const dayName=new Date(d.date+'T12:00:00').toLocaleDateString('ru-RU',{weekday:'short'});
            const isToday=d.date===todayStr;const isZero=d.count===0;
            const prevCount=idx>0?actData[idx-1].count:0;const diff=d.count-prevCount;
            let trendHtml='';
            if(idx>0){if(diff>0)trendHtml=`<span class="activity-trend trend-up">▲${diff}</span>`;else if(diff<0)trendHtml=`<span class="activity-trend trend-down">▼${Math.abs(diff)}</span>`;else trendHtml=`<span class="activity-trend trend-neutral">—</span>`;}
            else trendHtml=`<span class="activity-trend trend-neutral">—</span>`;
            return `<div class="activity-bar-wrap"><div class="flex items-center gap-1"><span class="activity-count ${isZero?'is-zero':''}">${d.count}</span>${trendHtml}</div><div class="activity-bar-track"><div class="activity-bar ${isZero?'is-zero':''}" style="height:${h}%"><div class="activity-bar-inner"></div></div></div><span class="activity-day ${isToday?'is-today':''}">${dayName}</span></div>`;
        }).join('')}</div><div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between"><div class="flex items-center gap-4 text-xs text-slate-500"><span class="flex items-center gap-1.5"><span class="activity-trend trend-up">▲</span> Рост</span><span class="flex items-center gap-1.5"><span class="activity-trend trend-down">▼</span> Падение</span><span class="flex items-center gap-1.5"><span class="activity-trend trend-neutral">—</span> Без изменений</span></div><span class="text-xs text-slate-500">Среднее: <strong class="text-slate-700 dark:text-slate-300">${avgCount.toFixed(1)}</strong>/день</span></div>`;

        const types={};historyItems.forEach(i=>{const ext=(i.filename||'').split('.').pop().toUpperCase();types[ext]=(types[ext]||0)+1;});
        document.getElementById('fileTypesChart').innerHTML=Object.entries(types).sort((a,b)=>b[1]-a[1]).map(([ext,cnt])=>{const p=Math.round(cnt/total*100);return`<div><div class="flex justify-between text-sm mb-1"><span class="font-medium">${getFileIcon(ext.toLowerCase())} .${ext}</span><span class="text-slate-500">${cnt} (${p}%)</span></div><div class="h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden"><div class="h-full bg-blue-500 rounded-full transition-all" style="width:${p}%"></div></div></div>`;}).join('');

        // Средний балл риска по всем документам
        const avgScore = historyItems.length > 0 ? Math.round(historyItems.reduce((a,b) => a + getItemScore(b), 0) / historyItems.length) : 0;
        const avgTime=historyItems.filter(i=>i.elapsed).reduce((a,b,i,arr)=>a+b.elapsed/arr.length,0);
        document.getElementById('summaryStats').innerHTML=`<div class="flex justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">Всего</span><span class="font-semibold">${total}</span></div><div class="flex justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">Средний балл риска</span><span class="font-semibold ${avgScore>66?'text-red-600 dark:text-red-400':avgScore>33?'text-amber-600 dark:text-amber-400':'text-emerald-600 dark:text-emerald-400'}">${avgScore}/100</span></div><div class="flex justify-between py-2 border-b border-slate-200 dark:border-slate-700"><span class="text-slate-600 dark:text-slate-400">Среднее время</span><span class="font-semibold">${avgTime?avgTime.toFixed(1)+'с':'—'}</span></div><div class="flex justify-between py-2"><span class="text-slate-600 dark:text-slate-400">С проблемами</span><span class="font-semibold text-amber-600 dark:text-amber-400">${cr+md}</span></div>`;
    }

    // ===== MODALS =====
    function openReportModal(id){
        const i=HM.get(id);if(!i)return;currentModalReport=i;
        const score=getItemScore(i);
        document.getElementById('modal-title').textContent=i.filename;
        document.getElementById('modal-meta').textContent=`${new Date(i.createdAt).toLocaleString('ru-RU')} · ${getRiskLabel(i.riskLevel)} · ${score}/100`;
        const findings=parseFindings(i.report||'');
        document.getElementById('modalReportContent').innerHTML=`<div class="flex flex-col md:flex-row gap-6 mb-6"><div class="flex-shrink-0 flex flex-col items-center p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl"><div class="relative w-28 h-28 mb-3">${buildGaugeSVG(score,112,9)}<div class="absolute inset-0 flex items-center justify-center flex-col"><span class="text-3xl font-bold text-slate-900 dark:text-white">${score}</span><span class="text-[10px] text-slate-500">баллов риска</span></div></div><span class="px-3 py-1 rounded-full text-xs font-bold ${getRiskBadgeFull(i.riskLevel)}">${getRiskLabel(i.riskLevel)}</span></div><div class="flex-1"><h4 class="font-semibold text-slate-900 dark:text-white mb-3">Ключевые проблемы</h4><div class="space-y-2">${buildFindingsHTML(findings)}</div></div></div><div class="border-t border-slate-200 dark:border-slate-700 pt-4"><h4 class="font-semibold text-slate-900 dark:text-white mb-3">Полный отчёт</h4><div class="markdown-body text-slate-700 dark:text-slate-300">${typeof marked!=='undefined'?marked.parse(i.report||''):`<pre>${escapeHtml(i.report||'')}</pre>`}</div></div>`;
        const m=document.getElementById('reportModal');m.classList.add('active');m.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';
    }
    function closeReportModal(){document.getElementById('reportModal').classList.remove('active');document.getElementById('reportModal').setAttribute('aria-hidden','true');document.body.style.overflow='';currentModalReport=null;}
    function confirmDeleteItem(id){const i=HM.get(id);if(!i)return;document.getElementById('confirmTitle').textContent='Удалить?';document.getElementById('confirmMessage').textContent=`"${i.filename}" будет удалено.`;document.getElementById('confirmOk').onclick=()=>{HM.remove(id);closeConfirmModal();showToast('Удалено','info');};document.getElementById('confirmModal').classList.add('active');}
    function confirmClearAll(){if(!historyItems.length)return;document.getElementById('confirmTitle').textContent='Очистить?';document.getElementById('confirmMessage').textContent=`${historyItems.length} записей будет удалено.`;document.getElementById('confirmOk').onclick=()=>{HM.clearAll();closeConfirmModal();showToast('Очищено','info');};document.getElementById('confirmModal').classList.add('active');}
    function closeConfirmModal(){document.getElementById('confirmModal').classList.remove('active');}

    // ===== DOWNLOADS =====
    function generateReportHTML(item){
        const score=getItemScore(item),findings=parseFindings(item.report||''),date=new Date(item.createdAt);
        return `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Отчёт LegalAI — ${escapeHtml(item.filename)}</title><style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',system-ui,sans-serif;color:#1e293b;line-height:1.6;padding:40px;max-width:800px;margin:0 auto}.header{background:linear-gradient(135deg,${item.riskLevel==='critical'?'#ef4444,#dc2626':item.riskLevel==='medium'?'#f59e0b,#d97706':'#22c55e,#16a34a'});color:white;padding:32px;border-radius:``javascript
        16px;margin-bottom:24px;display:flex;align-items:center;gap:20px}.header h1{font-size:24px;font-weight:700}.header p{opacity:0.85;font-size:14px}.score-box{background:rgba(255,255,255,0.2);border-radius:12px;padding:16px;text-align:center;min-width:100px}.score-box .num{font-size:36px;font-weight:800}.score-box .label{font-size:11px;opacity:0.9}.section{margin-bottom:24px}.section h2{font-size:18px;font-weight:700;color:#1e40af;margin-bottom:12px;padding-bottom:8px;border-bottom:2px solid #dbeafe}.finding{padding:12px 16px;margin-bottom:8px;border-radius:8px;border-left:4px solid #3b82f6;background:#f8fafc}.finding.critical{border-color:#ef4444;background:#fef2f2}.finding.warning{border-color:#f59e0b;background:#fffbeb}.finding p{font-size:14px}.report-body{font-size:14px;line-height:1.8}.report-body h2{color:#1e40af;font-size:16px;margin-top:20px;margin-bottom:8px}.report-body ul{padding-left:20px;margin:8px 0}.report-body li{margin:4px 0}.footer{margin-top:32px;padding-top:16px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center}</style></head><body><div class="header"><div class="score-box"><div class="num">${score}</div><div class="label">БАЛЛОВ РИСКА</div></div><div><h1>Анализ завершён</h1><p>${escapeHtml(item.filename)} · ${date.toLocaleDateString('ru-RU')} · ${getRiskLabel(item.riskLevel)}</p></div></div><div class="section"><h2>Ключевые выявленные проблемы</h2>${findings.map(f=>`<div class="finding ${f.type}"><p>${f.icon} ${escapeHtml(f.text)}</p></div>`).join('')}</div><div class="section"><h2>Полный отчёт</h2><div class="report-body">${typeof marked!=='undefined'?marked.parse(item.report||''):`<p>${escapeHtml(item.report||'')}</p>`}</div></div><div class="footer">Сгенерировано LegalAI Pro · ${new Date().toLocaleString('ru-RU')} · Не заменяет юридическую консультацию</div></body></html>`;
    }

    async function downloadPDF(itemOrId){
        const item=itemOrId&&typeof itemOrId==='object'?itemOrId:(itemOrId?HM.get(itemOrId):null)||currentModalReport;if(!item){showToast('Нет данных','error');return;}
        showToast('Генерация PDF...','info',2000);const html=generateReportHTML(item);const container=document.createElement('div');container.innerHTML=html;document.body.appendChild(container);
        try{await html2pdf().set({margin:[10,10,10,10],filename:`LegalAI-${item.filename.replace(/\.[^.]+$/,'')}-${Date.now()}.pdf`,image:{type:'jpeg',quality:0.98},html2canvas:{scale:2,useCORS:true},jsPDF:{unit:'mm',format:'a4',orientation:'portrait'}}).from(container).save();showToast('PDF скачан','success');}catch(e){showToast('Ошибка генерации PDF','error');console.error(e);}
        document.body.removeChild(container);
    }
    function downloadItemPDF(id){downloadPDF(id);}

    async function downloadDOCX(itemOrId){
        const item=itemOrId&&typeof itemOrId==='object'?itemOrId:(itemOrId?HM.get(itemOrId):null)||currentModalReport;if(!item){showToast('Нет данных','error');return;}
        if(typeof docx==='undefined'){showToast('Библиотека DOCX не загружена','error');return;}
        showToast('Генерация DOCX...','info',2000);
        try{
            const{Document,Paragraph,TextRun,HeadingLevel,AlignmentType}=docx;
            const score=getItemScore(item);
            const findings=parseFindings(item.report||'');
            const date=new Date(item.createdAt);
            const children=[];

            children.push(new Paragraph({children:[new TextRun({text:'LEGALAI PRO — ОТЧЁТ ОБ АНАЛИЗЕ',bold:true,size:32,color:'1E40AF'})],alignment:AlignmentType.CENTER,spacing:{after:200}}));
            children.push(new Paragraph({children:[new TextRun({text:escapeHtml(item.filename),size:24,bold:true})],alignment:AlignmentType.CENTER,spacing:{after:100}}));
            children.push(new Paragraph({children:[new TextRun({text:`Дата: ${date.toLocaleString('ru-RU')} | Риск: ${getRiskLabel(item.riskLevel)} | Балл: ${score}/100`,size:20,color:'64748B'})],alignment:AlignmentType.CENTER,spacing:{after:400}}));

    children.push(new Paragraph({children:[new TextRun({text:'КЛЮЧЕВЫЕ ВЫЯВЛЕННЫЕ ПРОБЛЕМЫ',bold:true,size:26,color:'1E40AF'})],heading:HeadingLevel.HEADING_2,spacing:{before:300,after:200}}));
    findings.forEach((f,i)=>{const color=f.type==='critical'?'DC2626':f.type==='warning'?'D97706':'2563EB';children.push(new Paragraph({children:[new TextRun({text:`${i+1}. ${f.icon} ${f.text}`,size:20,color})],spacing:{after:120},indent:{left:200}}));});

    children.push(new Paragraph({children:[new TextRun({text:'ПОЛНЫЙ ОТЧЁТ',bold:true,size:26,color:'1E40AF'})],heading:HeadingLevel.HEADING_2,spacing:{before:400,after:200}}));
    const lines=(item.report||'').split('\n');lines.forEach(line=>{const trimmed=line.trim();if(!trimmed)return;if(trimmed.startsWith('# ')){children.push(new Paragraph({children:[new TextRun({text:trimmed.replace(/^#+\s*/,''),bold:true,size:24,color:'1E40AF'})],heading:HeadingLevel.HEADING_2,spacing:{before:300,after:100}}));}else if(trimmed.startsWith('## ')){children.push(new Paragraph({children:[new TextRun({text:trimmed.replace(/^#+\s*/,''),bold:true,size:22,color:'1E40AF'})],heading:HeadingLevel.HEADING_3,spacing:{before:200,after:100}}));}else if(trimmed.startsWith('- ')||trimmed.startsWith('* ')){children.push(new Paragraph({children:[new TextRun({text:trimmed.replace(/^[-*]\s*/,''),size:20})],spacing:{after:60},indent:{left:400}}));}else{children.push(new Paragraph({children:[new TextRun({text:trimmed,size:20})],spacing:{after:80}}));}});

    children.push(new Paragraph({children:[new TextRun({text:'— — —',size:16,color:'94A3B8'})],alignment:AlignmentType.CENTER,spacing:{before:400}}));
    children.push(new Paragraph({children:[new TextRun({text:`Сгенерировано LegalAI Pro · ${new Date().toLocaleString('ru-RU')} · Не заменяет юридическую консультацию`,size:16,color:'94A3B8',italics:true})],alignment:AlignmentType.CENTER,spacing:{after:200}}));

    const doc=new Document({sections:[{properties:{},children}]});const blob=await docx.Packer.toBlob(doc);const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download=`LegalAI-${item.filename.replace(/\.[^.]+$/,'')}-${Date.now()}.docx`;a.click();URL.revokeObjectURL(url);showToast('DOCX скачан','success');
    }catch(e){showToast('Ошибка генерации DOCX','error');console.error(e);}
    }
    function downloadItemDOCX(id){downloadDOCX(id);}

    function downloadHTML(itemOrId){
        const item=itemOrId&&typeof itemOrId==='object'?itemOrId:(itemOrId?HM.get(itemOrId):null)||currentModalReport;if(!item){showToast('Нет данных','error');return;}
        const html=generateReportHTML(item);const blob=new Blob([html],{type:'text/html'});const url=URL.createObjectURL(blob);const a=document.createElement('a');a.href=url;a.download=`LegalAI-${item.filename.replace(/\.[^.]+$/,'')}-${Date.now()}.html`;a.click();URL.revokeObjectURL(url);showToast('HTML скачан','success');
    }
    async function copyReportText(id){const i=id?HM.get(id):currentModalReport;if(!i)return;try{await navigator.clipboard.writeText(i.report||'');showToast('Скопировано','success',2500);}catch{showToast('Ошибка','error');}}

    // ===== FILE & UPLOAD =====
    let cName='',cSize='';

    function handleFile(file){if(!file)return;if(!ALLOWED_TYPES.includes(file.type)&&!file.name.match(/\.(pdf|doc|docx|txt)$/i)){showToast('Неподдерживаемый формат','error');return;}if(file.size>MAX_FILE_SIZE){showToast('Слишком большой файл','error');return;}document.getElementById('selectedFileName').textContent=file.name;document.getElementById('selectedFileSize').textContent=formatSize(file.size);document.getElementById('dropContent').classList.add('hidden');document.getElementById('fileSelected').classList.remove('hidden');log(`📎 Выбран: ${file.name}`,'info');}

    async function uploadFile(file){const fd=new FormData();fd.append('file',file);const c=new AbortController(),t=setTimeout(()=>c.abort(),120000);try{const r=await fetch(`${PYTHON_API}/api/analyze`,{method:'POST',body:fd,signal:c.signal});clearTimeout(t);if(!r.ok)throw new Error(`HTTP ${r.status}`);return(await r.json()).request_id;}catch(e){clearTimeout(t);throw e;}}

    function connectSSE(rid,fname,fsize){cName=fname;cSize=fsize;eventSource=new EventSource(`${PYTHON_API}/api/stream/${rid}`);eventSource.onmessage=(e)=>{try{const v=JSON.parse(e.data);switch(v.type){case'status':log(v.step||'Обработка...','status');break;case'progress':updateProgress(v.progress_percent);break;case'chunk_result':log(v.preview,'chunk');break;case'summary_complete':processSummary(v.content);break;case'complete':log('🎉 Завершено!','success');finalizeReport();break;case'error':log(`❌ ${v.message}`,'error');showToast(v.message||'Ошибка','error');eventSource.close();resetSubmitBtn();break;}}catch(err){console.error(err);}};eventSource.onerror=()=>{log('🔴 Ошибка соединения','error');showToast('Потеряно соединение','error');eventSource.close();resetSubmitBtn();};}

    function processSummary(content){
        lastReportContent=content;
        document.getElementById('reportContent').innerHTML=typeof marked!=='undefined'?marked.parse(content):`<pre>${escapeHtml(content)}</pre>`;

        // ===== ИСПОЛЬЗУЕМ НАСТОЯЩИЙ АНАЛИЗ РИСКОВ =====
        const analysisResult = analyzeReportRisk(content);
        const riskLevel = analysisResult.level;
        const riskScore = analysisResult.score;
        const findingsCount = analysisResult.findings;

        log(`🎯 Итог: ${riskScore}/100 (${getRiskLabel(riskLevel)}) | Критич: ${findingsCount.critical}, Предост: ${findingsCount.warning}, Инфо: ${findingsCount.info}`, 'success');

        const gaugeCircle=document.getElementById('riskGaugeCircle');
        const circ=2*Math.PI*52;
        const pct=riskScore/100;
        const offset=circ-(pct*circ);
        gaugeCircle.setAttribute('stroke-dashoffset',offset);
        gaugeCircle.setAttribute('stroke',getRiskColor(riskLevel));

        document.getElementById('riskGaugeScore').textContent=riskScore;

        const badge=document.getElementById('riskLevelBadge');
        badge.textContent=`${getRiskLabel(riskLevel)} — ${riskScore}/100`;
        badge.className=`px-4 py-1.5 rounded-full text-sm font-bold ${getRiskBadgeFull(riskLevel)}`;

        document.getElementById('reportHeaderBg').className=`bg-gradient-to-r ${getRiskGradient(riskLevel)} px-8 py-6 flex items-center gap-4`;

        const findings=parseFindings(content);
        document.getElementById('findingsTable').innerHTML=buildFindingsHTML(findings);
        document.getElementById('reportSubtitle').textContent=`${cName} — ${getRiskLabel(riskLevel)} — ${riskScore}/100`;

        // Сохраняем реальный балл и данные в историю
        HM.add({
            filename:cName,
            fileSize:cSize,
            riskLevel: riskLevel,
            riskScore: riskScore, // ВАЖНО: сохраняем реальный балл
            criticalCount: findingsCount.critical,
            warningCount: findingsCount.warning,
            infoCount: findingsCount.info,
            report:content,
            elapsed:null,
            status:'completed'
        });
        recordCabinetActivity({
            type:'analysis',
            title:'Анализ документа',
            details:`${cName || 'Документ'} — риск ${getRiskLabel(riskLevel)} (${riskScore}/100)`,
            file_name:cName || '',
            summary:`Риск ${getRiskLabel(riskLevel)} (${riskScore}/100). Критические: ${findingsCount.critical}, предупреждения: ${findingsCount.warning}, информационные: ${findingsCount.info}.`,
            result:content,
            status:'completed',
            metadata:{riskLevel,riskScore,criticalCount:findingsCount.critical,warningCount:findingsCount.warning,infoCount:findingsCount.info}
        });
        showToast('Сохранено в историю','success',2500);
    }

    function finalizeReport() {
        // 1. Показываем блок с готовым отчетом
        document.getElementById('finalReport').classList.remove('hidden');

        // 2. Переводим статус-badge в красивое состояние Pro-SaaS (стиль Stripe/Linear)
        const b = document.getElementById('analysisStatusBadge');
        b.innerHTML = '<span class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Анализ завершён';
        b.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-900/40 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-800 rounded-xl text-xs font-medium transition-all duration-200 shadow-sm';

        // 3. Доводим прогресс-бар до финала и закрываем поток данных
        updateProgress(100);
        if (typeof eventSource !== 'undefined' && eventSource) {
            eventSource.close();
        }

        // ===== ВАЖНОЕ ИСПРАВЛЕНИЕ РИСКОВ =====
        // Вызываем финальный перерасчет рисков ПОЛНОГО текста отчета,
        // который накопился в глобальной переменной (у вас это, скорее всего, `fullResponseText` или аналогичная)
        if (typeof fullResponseText !== 'undefined' && fullResponseText) {
            processSummary(fullResponseText);
        } else if (typeof lastReportContent !== 'undefined' && lastReportContent) {
            processSummary(lastReportContent);
        } else {
            // Если текст хранился прямо в DOM-элементе:
            const rawContent = document.getElementById('reportContent').innerText;
            processSummary(rawContent);
        }

        // 4. Показываем финальное уведомление
        showToast('Анализ документа успешно завершён', 'success', 3000);
    }
    function updateProgress(p){const b=document.getElementById('progressBar'),t=document.getElementById('progressText'),st={u:document.getElementById('stage-upload'),p:document.getElementById('stage-process'),a:document.getElementById('stage-analyze'),d:document.getElementById('stage-done')};b.style.width=`${p}%`;t.textContent=`${p}%`;if(p>=100){Object.values(st).forEach(e=>{e.className='w-2 h-2 rounded-full bg-green-500'});b.classList.remove('progress-animated');b.style.background='#22c55e';}else if(p>=75){st.a.className='w-2 h-2 rounded-full bg-blue-500';}else if(p>=40){st.p.className='w-2 h-2 rounded-full bg-blue-500';}}

    function resetSubmitBtn(){const b=document.getElementById('submitBtn');b.disabled=false;b.setAttribute('aria-busy','false');b.innerHTML='<span class="text-xl group-hover:scale-110 transition-transform">🚀</span><span>Запустить анализ</span><svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>';isAnalyzing=false;}

    function resetAnalysisUI(){document.getElementById('analysisProgress').classList.add('hidden');document.getElementById('finalReport').classList.add('hidden');document.getElementById('fileInput').value='';document.getElementById('dropContent').classList.remove('hidden');document.getElementById('fileSelected').classList.add('hidden');document.getElementById('progressBar').style.width='0%';document.getElementById('progressBar').style.background='';document.getElementById('progressBar').classList.add('progress-animated');document.getElementById('progressText').textContent='0%';['stage-upload','stage-process','stage-analyze','stage-done'].forEach((id,i)=>{document.getElementById(id).className=`w-2 h-2 rounded-full ${i===0?'bg-blue-500':'bg-slate-300 dark:bg-slate-600'}`;});isAnalyzing=false;resetSubmitBtn();}

    // ===== INIT =====
    document.addEventListener('DOMContentLoaded',()=>{
        const dz=document.getElementById('dropZone'),fi=document.getElementById('fileInput'),cf=document.getElementById('changeFile'),uf=document.getElementById('uploadForm'),cl=document.getElementById('clearLogs'),na=document.getElementById('newAnalysisBtn'),cah=document.getElementById('clearAllHistory'),hs=document.getElementById('historySearch'),mc=document.getElementById('modalClose'),cc=document.getElementById('confirmCancel'),mcb=document.getElementById('modalCopyBtn'),mpdf=document.getElementById('modalPdfBtn'),mdocx=document.getElementById('modalDocxBtn'),tt=document.getElementById('themeToggle');

        dz.addEventListener('click',()=>fi.click());dz.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();fi.click();}});
        ['dragenter','dragover'].forEach(e=>dz.addEventListener(e,ev=>{ev.preventDefault();ev.stopPropagation();dz.classList.add('dragover');}));
        ['dragleave','drop'].forEach(e=>dz.addEventListener(e,ev=>{ev.preventDefault();ev.stopPropagation();dz.classList.remove('dragover');}));
        dz.addEventListener('drop',e=>{const f=e.dataTransfer.files[0];if(f){fi.files=e.dataTransfer.files;handleFile(f);}});
        fi.addEventListener('change',e=>handleFile(e.target.files[0]));cf.addEventListener('click',e=>{e.stopPropagation();fi.click();});

        uf.addEventListener('submit',async e=>{
            e.preventDefault();if(isAnalyzing)return;const file=fi.files[0];if(!file){showToast('Выберите файл','warning');return;}
            document.getElementById('analysisProgress').classList.remove('hidden');document.getElementById('analysisFileName').textContent=file.name;document.getElementById('logContainer').innerHTML='';updateProgress(0);log('🔗 Подключение...','status');
            isAnalyzing=true;const btn=document.getElementById('submitBtn');btn.disabled=true;btn.innerHTML='<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Загрузка...</span>';
            try{log(`⬆️ ${file.name}`,'info');const id=await uploadFile(file);log('✅ Файл принят','success');connectSSE(id,file.name,formatSize(file.size));}catch(err){log(`❌ ${err.message}`,'error');showToast('Ошибка загрузки','error');resetSubmitBtn();}
        });

        cl.addEventListener('click',()=>{document.getElementById('logContainer').innerHTML='';log('🧹 Лог очищен','info');});
        na.addEventListener('click',()=>{resetAnalysisUI();showToast('Готов к анализу','info');});
        cah.addEventListener('click',confirmClearAll);
        let st;hs.addEventListener('input',()=>{clearTimeout(st);st=setTimeout(()=>renderHistory(),300);});
        document.querySelectorAll('.filter-btn').forEach(b=>b.addEventListener('click',()=>{document.querySelectorAll('.filter-btn').forEach(x=>{x.classList.remove('active');x.classList.add('bg-slate-100','dark:bg-slate-800','text-slate-600','dark:text-slate-400');});b.classList.add('active');b.classList.remove('bg-slate-100','dark:bg-slate-800','text-slate-600','dark:text-slate-400');currentFilter=b.dataset.filter;renderHistory();}));
        mc.addEventListener('click',closeReportModal);document.getElementById('reportModal').addEventListener('click',e=>{if(e.target.id==='reportModal')closeReportModal();});
        cc.addEventListener('click',closeConfirmModal);document.getElementById('confirmModal').addEventListener('click',e=>{if(e.target.id==='confirmModal')closeConfirmModal();});
        mcb.addEventListener('click',()=>copyReportText());mpdf.addEventListener('click',()=>downloadPDF());mdocx.addEventListener('click',()=>downloadDOCX());
        tt.addEventListener('click',toggleTheme);
        document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(document.getElementById('reportModal').classList.contains('active'))closeReportModal();if(document.getElementById('confirmModal').classList.contains('active'))closeConfirmModal();}});

        HM.load();updateDashboardStats();renderHistory();showSection('dashboard');
    });
</script>
</body>
</html>
