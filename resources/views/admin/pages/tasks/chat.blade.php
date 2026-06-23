<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LegalAI Assistant — Умный юридический бот</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://cdn.jsdelivr.net/fontsource/fonts/inter:latin@400,500,600,700&display.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ===== CSS VARIABLES FOR THEMES ===== */
        :root {
            /* Dark theme (default) */
            --bg-gradient-start: #0f172a;
            --bg-gradient-mid: #1e293b;
            --bg-gradient-end: #0f172a;

            --card-bg: rgba(30, 41, 59, 0.8);
            --card-bg-end: rgba(15, 23, 42, 0.9);
            --card-border: rgba(71, 85, 105, 0.5);

            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --text-muted: #94a3b8;
            --text-faint: #64748b;

            --accent: #3b82f6;
            --accent-dark: #1d4ed8;
            --accent-hover: rgba(59, 130, 246, 0.15);
            --accent-active: rgba(59, 130, 246, 0.2);

            --surface: #1e293b;
            --surface-2: #334155;
            --surface-3: #475569;

            --message-user-start: #3b82f6;
            --message-user-end: #1d4ed8;
            --message-ai-bg: rgba(59, 130, 246, 0.1);
            --message-ai-border: rgba(59, 130, 246, 0.3);

            --scrollbar-track: #1e293b;
            --scrollbar-thumb: #475569;
            --scrollbar-thumb-hover: #64748b;

            --code-bg: rgba(0, 0, 0, 0.3);
            --pre-bg: rgba(0, 0, 0, 0.3);
            --blockquote-color: #94a3b8;
            --table-border: #475569;
            --th-bg: rgba(59, 130, 246, 0.2);

            --input-bg: #1e293b;
            --input-border: #475569;

            --overlay-bg: rgba(0, 0, 0, 0.6);
            --modal-bg: #1e293b;
            --modal-border: #475569;

            --glow-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            --btn-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        }

        /* ===== LIGHT THEME ===== */
        [data-theme="light"] {
            --bg-gradient-start: #f8fafc;
            --bg-gradient-mid: #e2e8f0;
            --bg-gradient-end: #f1f5f9;

            --card-bg: rgba(255, 255, 255, 0.85);
            --card-bg-end: rgba(248, 250, 252, 0.95);
            --card-border: rgba(203, 213, 225, 0.6);

            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #475569;
            --text-faint: #64748b;

            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --accent-hover: rgba(37, 99, 235, 0.08);
            --accent-active: rgba(37, 99, 235, 0.12);

            --surface: #ffffff;
            --surface-2: #f1f5f9;
            --surface-3: #e2e8f0;

            --message-user-start: #2563eb;
            --message-user-end: #1d4ed8;
            --message-ai-bg: #f1f5f9;
            --message-ai-border: #e2e8f0;

            --scrollbar-track: #f1f5f9;
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-thumb-hover: #94a3b8;

            --code-bg: #f1f5f9;
            --pre-bg: #f8fafc;
            --blockquote-color: #475569;
            --table-border: #e2e8f0;
            --th-bg: #eff6ff;

            --input-bg: #ffffff;
            --input-border: #cbd5e1;

            --overlay-bg: rgba(15, 23, 42, 0.4);
            --modal-bg: #ffffff;
            --modal-border: #e2e8f0;

            --glow-shadow: 0 0 20px rgba(37, 99, 235, 0.25);
            --btn-shadow: 0 4px 16px rgba(37, 99, 235, 0.25);
        }

        /* ===== THEME TRANSITIONS ===== */
        body, nav, aside, main, header, .card-gradient, .message-ai, .message-user,
        .settings-panel, .delete-confirmation, .notification-dropdown, textarea,
        input, button, .sidebar-item, .model-selector, .chat-group-title {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-mid) 50%, var(--bg-gradient-end) 100%);
        }

        .card-gradient {
            background: linear-gradient(145deg, var(--card-bg) 0%, var(--card-bg-end) 100%);
            backdrop-filter: blur(10px);
        }

        .message-user {
            background: linear-gradient(135deg, var(--message-user-start) 0%, var(--message-user-end) 100%);
            color: #ffffff;
        }

        .message-ai {
            background: var(--message-ai-bg);
            border: 1px solid var(--message-ai-border);
        }

        [data-theme="light"] .message-ai {
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }

        .typing-indicator span {
            animation: typing 1.4s infinite;
            animation-fill-mode: both;
            background: var(--accent);
        }
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }

        .sidebar-item {
            transition: all 0.2s ease;
            position: relative;
            color: var(--text-secondary);
        }
        .sidebar-item:hover, .sidebar-item.active {
            background: var(--accent-hover);
        }
        .sidebar-item.active {
            border-left: 3px solid var(--accent);
            background: var(--accent-active);
        }

        .sidebar-item .delete-btn {
            opacity: 0;
            transition: opacity 0.2s;
        }
        .sidebar-item:hover .delete-btn {
            opacity: 1;
        }

        .chat-group-title {
            font-size: 0.75rem;
            color: var(--text-faint);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px 8px;
            margin-top: 8px;
        }

        .chat-group-title:first-child {
            margin-top: 0;
        }

        .pulse-animation { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .glow-text { text-shadow: var(--glow-shadow); }
        [data-theme="light"] .glow-text { text-shadow: none; color: var(--accent); }

        .btn-primary {
            background: linear-gradient(135deg, var(--message-user-start) 0%, var(--message-user-end) 100%);
            color: #ffffff;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            box-shadow: var(--btn-shadow);
            transform: translateY(-1px);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-stop {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: #ffffff;
            transition: all 0.2s;
        }
        .btn-stop:hover {
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.4);
            transform: translateY(-1px);
        }

        .btn-stop.hidden-btn,
        .btn-send.hidden-btn {
            display: none;
        }

        .markdown-content h1, .markdown-content h2, .markdown-content h3 {
            margin-top: 1em; margin-bottom: 0.5em; font-weight: 600;
            color: var(--text-primary);
        }
        .markdown-content p { margin-bottom: 1em; line-height: 1.7; color: var(--text-secondary); }
        .markdown-content ul, .markdown-content ol { margin-left: 1.5em; margin-bottom: 1em; color: var(--text-secondary); }
        .markdown-content code {
            background: var(--code-bg); padding: 0.2em 0.4em;
            border-radius: 4px; font-family: 'Courier New', monospace; font-size: 0.9em;
            color: var(--text-primary);
        }
        [data-theme="light"] .markdown-content code {
            border: 1px solid var(--input-border);
            color: #be185d;
        }
        .markdown-content pre {
            background: var(--pre-bg); padding: 1em;
            border-radius: 8px; overflow-x: auto; margin-bottom: 1em;
            border: 1px solid var(--input-border);
        }
        .markdown-content pre code {
            background: transparent;
            border: none;
            padding: 0;
        }
        .markdown-content blockquote {
            border-left: 3px solid var(--accent); padding-left: 1em;
            margin: 1em 0; color: var(--blockquote-color);
        }
        .markdown-content table {
            width: 100%; border-collapse: collapse; margin: 1em 0;
        }
        .markdown-content th, .markdown-content td {
            border: 1px solid var(--table-border); padding: 0.5em 1em; text-align: left;
            color: var(--text-secondary);
        }
        .markdown-content th { background: var(--th-bg); color: var(--text-primary); }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--scrollbar-track); }
        ::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-thumb-hover); }

        .settings-panel {
            transform: translateX(100%);
            transition: transform 0.3s ease;
            background: linear-gradient(145deg, var(--card-bg) 0%, var(--card-bg-end) 100%);
            border-left: 1px solid var(--card-border);
        }
        .settings-panel.open { transform: translateX(0); }

        .model-selector { transition: all 0.3s ease; cursor: pointer; }
        .model-selector.active { border-color: var(--accent) !important; background: var(--accent-active); }

        .chat-message { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        textarea {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
        }

        #messageInput {
            min-height: 44px;
            max-height: 200px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text-primary);
        }
        #messageInput::placeholder { color: var(--text-faint); }
        #messageInput:focus { border-color: var(--accent); }

        .notification-dropdown {
            transform: translateY(-8px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
        }
        .notification-dropdown.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .file-attachment-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--accent-hover);
            border: 1px solid var(--accent);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.8rem;
            color: var(--accent);
            margin-top: 8px;
        }

        .chat-timestamp {
            font-size: 0.7rem;
            color: var(--text-faint);
            margin-top: 2px;
        }

        .chat-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .delete-confirmation {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--modal-bg);
            border: 1px solid var(--modal-border);
            border-radius: 12px;
            padding: 20px;
            z-index: 100;
            min-width: 300px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            color: var(--text-primary);
        }
        [data-theme="dark"] .delete-confirmation {
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        .delete-confirmation.hidden { display: none; }
        .delete-confirmation-overlay {
            position: fixed;
            inset: 0;
            background: var(--overlay-bg);
            z-index: 99;
        }
        .delete-confirmation-overlay.hidden { display: none; }

        .chat-item-wrapper {
            padding: 4px 12px;
            margin: 2px 0;
        }

        /* ===== THEME TOGGLE BUTTON ===== */
        .theme-toggle {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.2s ease;
        }
        .theme-toggle:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-hover);
        }
        .theme-toggle svg {
            transition: transform 0.3s ease;
        }
        .theme-toggle:hover svg {
            transform: rotate(15deg);
        }
        .theme-icon-sun,
        .theme-icon-moon {
            position: absolute;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        [data-theme="dark"] .theme-icon-sun { opacity: 1; transform: rotate(0deg); }
        [data-theme="dark"] .theme-icon-moon { opacity: 0; transform: rotate(-90deg); }
        [data-theme="light"] .theme-icon-sun { opacity: 0; transform: rotate(90deg); }
        [data-theme="light"] .theme-icon-moon { opacity: 1; transform: rotate(0deg); }

        /* ===== TEXT COLOR HELPERS ===== */
        body { color: var(--text-primary); }
        .text-white { color: var(--text-primary) !important; }
        .text-slate-300 { color: var(--text-secondary) !important; }
        .text-slate-400 { color: var(--text-muted) !important; }
        .text-slate-500 { color: var(--text-faint) !important; }

        /* Light theme adjustments for elements using hardcoded Tailwind colors */
        [data-theme="light"] .bg-slate-800 { background: var(--surface-2); }
        [data-theme="light"] .bg-slate-800\/50 { background: rgba(241, 245, 249, 0.5); }
        [data-theme="light"] .hover\:bg-slate-800:hover { background: var(--surface-2); }
        [data-theme="light"] .hover\:bg-slate-800\/50:hover { background: rgba(241, 245, 249, 0.5); }
        [data-theme="light"] .border-slate-700 { border-color: var(--surface-3); }
        [data-theme="light"] .border-slate-700\/50 { border-color: rgba(203, 213, 225, 0.5); }
        [data-theme="light"] .border-slate-600 { border-color: var(--surface-3); }
        [data-theme="light"] .text-slate-300 { color: var(--text-secondary); }
        [data-theme="light"] .text-slate-400 { color: var(--text-muted); }
        [data-theme="light"] .text-slate-500 { color: var(--text-faint); }
        [data-theme="light"] .text-red-400 { color: #dc2626; }
        [data-theme="light"] .text-red-300 { color: #ef4444; }
        [data-theme="light"] .bg-blue-500\/20 { background: rgba(37, 99, 235, 0.12); }

        [data-theme="light"] nav.card-gradient {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(203, 213, 225, 0.6);
        }
        [data-theme="light"] aside.card-gradient {
            background: #ffffff;
            border-right: 1px solid rgba(203, 213, 225, 0.6);
        }
        [data-theme="light"] header.card-gradient {
            background: #ffffff;
            border-bottom: 1px solid rgba(203, 213, 225, 0.6);
        }
        [data-theme="light"] .notification-dropdown.card-gradient {
            background: #ffffff;
            border: 1px solid var(--surface-3);
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.1);
        }
        [data-theme="light"] .notification-dropdown .border-b {
            border-color: var(--surface-3);
        }

        /* Welcome screen cards in light theme */
        [data-theme="light"] .card-gradient.rounded-xl {
            background: #ffffff;
            border: 1px solid var(--surface-3);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
        }
        [data-theme="light"] .card-gradient.rounded-xl:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }

        /* User message text stays white even in light theme */
        .message-user * { color: #ffffff; }

        /* AI message text color */
        [data-theme="light"] .message-ai .markdown-content p,
        [data-theme="light"] .message-ai .markdown-content li,
        [data-theme="light"] .message-ai .markdown-content td {
            color: var(--text-secondary);
        }
        [data-theme="light"] .message-ai .markdown-content h1,
        [data-theme="light"] .message-ai .markdown-content h2,
        [data-theme="light"] .message-ai .markdown-content h3,
        [data-theme="light"] .message-ai .markdown-content th,
        [data-theme="light"] .message-ai .markdown-content strong {
            color: var(--text-primary);
        }

        /* Model selector in light theme */
        [data-theme="light"] .model-selector {
            background: var(--surface-2);
            border: 1px solid var(--surface-3);
        }
        [data-theme="light"] .model-selector:hover {
            border-color: var(--accent);
        }

        /* Radio and checkbox in light theme */
        [data-theme="light"] input[type="radio"],
        [data-theme="light"] input[type="checkbox"] {
            accent-color: var(--accent);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen" data-theme="dark">

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 card-gradient border-b" style="border-color: var(--card-border);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="showSection('home')">
                <svg class="w-8 h-8" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold glow-text">LegalAI Bot</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="text-slate-300 hover:text-white transition">Главная</a>
                <a href="{{route('tasks.create')}}" class="text-slate-300 hover:text-white transition">Возможности</a>
                <a href="{{route('tasks.calc')}}" class="text-slate-300 hover:text-white transition">Калькулятор</a>
                <a href="{{route('tasks.chat')}}" class="text-slate-300 hover:text-white transition">Чат ИИ</a>

                <!-- THEME TOGGLE -->
                <button onclick="toggleTheme()" class="theme-toggle" title="Сменить тему" aria-label="Переключить тему">
                    <svg class="theme-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg class="theme-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                <div class="relative">
                    <button onclick="toggleNotifications()" class="text-slate-300 hover:text-white transition relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notifBadge" class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs flex items-center justify-center text-white">3</span>
                    </button>
                    <div id="notificationPanel" class="notification-dropdown absolute right-0 mt-2 w-80 card-gradient rounded-xl border shadow-xl z-50">
                        <div class="p-4 border-b" style="border-color: var(--card-border);">
                            <h4 class="font-semibold" style="color: var(--text-primary);">Уведомления</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b hover:bg-slate-800/50 cursor-pointer" style="border-color: var(--card-border);">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--text-primary);">Анализ договора №1234 завершён</p>
                                        <p class="text-xs text-slate-400 mt-1">2 минуты назад</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b hover:bg-slate-800/50 cursor-pointer" style="border-color: var(--card-border);">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--text-primary);">Обнаружен высокий риск в договоре поставки</p>
                                        <p class="text-xs text-slate-400 mt-1">15 минут назад</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-slate-800/50 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm" style="color: var(--text-primary);">Срок действия договора истекает через 7 дней</p>
                                        <p class="text-xs text-slate-400 mt-1">1 час назад</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{route('login')}}"
                   class="btn-primary px-6 py-2 rounded-lg font-medium inline-block text-center">
                    Войти
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- App Container -->
<div class="flex h-screen pt-16 overflow-hidden">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-72 card-gradient border-r flex flex-col flex-shrink-0 hidden lg:flex" style="border-color: var(--card-border);">

        <div class="p-4">
            <button onclick="newChat()" class="w-full btn-primary py-3 rounded-xl font-medium flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Новый чат</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-2 pb-4">
            <div class="flex items-center justify-between px-2 mb-2">
                <h3 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-faint);">Все чаты</h3>
                <button onclick="clearAllHistory()" class="text-xs text-red-400 hover:text-red-300 transition" title="Очистить всю историю">
                    Очистить
                </button>
            </div>
            <div id="chatHistory" class="space-y-1"></div>
            <p id="emptyHistory" class="text-sm text-center mt-8 hidden" style="color: var(--text-faint);">Нет сохранённых чатов</p>
        </div>

        <div class="p-4 border-t" style="border-color: var(--card-border);">
            <button onclick="toggleSettings()" class="w-full py-3 rounded-xl font-medium border hover:border-blue-500 transition flex items-center justify-center space-x-2" style="border-color: var(--surface-3); color: var(--text-secondary);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Настройки</span>
            </button>
        </div>
    </aside>

    <!-- Main Chat Area -->
    <main class="flex-1 flex flex-col min-w-0">
        <!-- Header -->
        <header class="card-gradient border-b p-4" style="border-color: var(--card-border);">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-lg font-semibold" id="chatTitle" style="color: var(--text-primary);">Новый диалог</h2>
                        <div class="flex items-center space-x-2 text-sm text-slate-400">
                            <span class="w-2 h-2 bg-green-500 rounded-full pulse-animation"></span>
                            <span id="modelStatus">llama3.1:8b Ready</span>
                        </div>
                        <div id="chatSessionInfo" class="text-xs text-slate-500 mt-1 hidden"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportChat()" class="p-2 rounded-lg border hover:border-blue-500 transition" style="border-color: var(--surface-3); color: var(--text-muted);" title="Экспорт чата">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </button>
                    <button onclick="clearChat()" class="p-2 rounded-lg border hover:border-red-500 transition" style="border-color: var(--surface-3); color: var(--text-muted);" title="Очистить чат">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Chat Messages -->
        <div id="chatContainer" class="flex-1 overflow-y-auto p-6">
            <!-- Welcome Screen -->
            <div id="welcomeMessage" class="max-w-3xl mx-auto text-center py-12">
                <div class="w-20 h-20 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold mb-4" style="color: var(--text-primary);">Добро пожаловать в LegalAI Bot</h3>
                <p class="text-slate-400 mb-8 max-w-lg mx-auto">
                    Я ваш интеллектуальный юридический помощник на базе локальной ИИ-модели.
                    Могу помочь с анализом договоров, проверкой рисков и консультациями по законодательству.
                </p>
                <div class="grid md:grid-cols-2 gap-4 text-left">
                    <div class="p-4 card-gradient rounded-xl border cursor-pointer hover:border-blue-500 transition" onclick="sendQuickQuestion('Проанализируй договор поставки на предмет рисков')">
                        <div class="flex items-center space-x-3 mb-2">
                            <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="font-medium" style="color: var(--text-primary);">Анализ рисков</span>
                        </div>
                        <p class="text-sm text-slate-400">Проверка договоров на юридические риски</p>
                    </div>
                    <div class="p-4 card-gradient rounded-xl border cursor-pointer hover:border-blue-500 transition" onclick="sendQuickQuestion('Какие пункты должны быть в договоре аренды?')">
                        <div class="flex items-center space-x-3 mb-2">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="font-medium" style="color: var(--text-primary);">Шаблон договора</span>
                        </div>
                        <p class="text-sm text-slate-400">Создание и проверка шаблонов</p>
                    </div>
                    <div class="p-4 card-gradient rounded-xl border cursor-pointer hover:border-blue-500 transition" onclick="sendQuickQuestion('Какая ответственность за нарушение договора по ГК Республики Таджикистан?')">
                        <div class="flex items-center space-x-3 mb-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                            <span class="font-medium" style="color: var(--text-primary);">Консультация по ГК РТ</span>
                        </div>
                        <p class="text-sm text-slate-400">Ответы на юридические вопросы</p>
                    </div>
                    <div class="p-4 card-gradient rounded-xl border cursor-pointer hover:border-blue-500 transition" onclick="sendQuickQuestion('Как защитить интеллектуальную собственность в договоре?')">
                        <div class="flex items-center space-x-3 mb-2">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span class="font-medium" style="color: var(--text-primary);">Защита прав</span>
                        </div>
                        <p class="text-sm text-slate-400">Интеллектуальная собственность</p>
                    </div>
                </div>
            </div>

            <div id="messagesContainer" class="max-w-3xl mx-auto space-y-6 hidden"></div>
        </div>

        <!-- Input Area -->
        <div class="card-gradient border-t p-4" style="border-color: var(--card-border);">
            <div class="max-w-3xl mx-auto">
                <div id="fileBadgeContainer" class="hidden mb-3">
                    <span id="fileBadge" class="file-attachment-badge">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span id="fileName"></span>
                        <button onclick="removeFile()" class="ml-2 hover:text-white">✕</button>
                    </span>
                </div>
                <div class="flex items-end space-x-3">
                    <button onclick="attachFile()" class="p-3 rounded-xl border hover:border-blue-500 transition flex-shrink-0" style="border-color: var(--surface-3); color: var(--text-muted);" title="Прикрепить файл">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </button>
                    <input type="file" id="fileAttachment" class="hidden" accept=".pdf,.docx,.txt,.jpg,.png,.doc">
                    <div class="flex-1 relative">
                        <textarea
                            id="messageInput"
                            rows="1"
                            placeholder="Задайте вопрос или загрузите договор для анализа..."
                            class="w-full rounded-xl px-4 py-3 pr-12 focus:outline-none resize-none"
                            onkeydown="handleKeyDown(event)"
                        ></textarea>
                    </div>
                    <button onclick="sendMessage()" id="sendBtn" class="p-3 btn-primary rounded-xl flex-shrink-0 btn-send" title="Отправить">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                    <button onclick="stopGeneration()" id="stopBtn" class="p-3 btn-stop rounded-xl flex-shrink-0 btn-stop hidden-btn" title="Остановить генерацию">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <rect x="6" y="6" width="12" height="12" rx="2"/>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between mt-3 text-xs" style="color: var(--text-faint);">
                    <div class="flex items-center space-x-4">
                        <span>Enter — отправить</span>
                        <span>Shift+Enter — новая строка</span>
                    </div>
                    <span id="charCount">0 / 4000</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Settings Panel -->
    <div id="settingsPanel" class="settings-panel fixed right-0 top-16 h-full w-96 z-50 overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold" style="color: var(--text-primary);">Настройки</h3>
                <button onclick="toggleSettings()" class="hover:text-white transition" style="color: var(--text-muted);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- AI Model Selection -->
            <div class="mb-8">
                <h4 class="font-semibold mb-4 flex items-center space-x-2" style="color: var(--text-primary);">
                    <svg class="w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Модель ИИ</span>
                </h4>
                <div class="space-y-3">
                    <div class="model-selector active p-4 rounded-xl border" style="border-color: var(--accent);" onclick="selectModel('llama3.1:8b', this)">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium" style="color: var(--text-primary);">llama3.1:8b</div>
                                <div class="text-sm" style="color: var(--text-muted);">Локальная • Быстрая</div>
                            </div>
                            <svg class="check-icon w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="model-selector p-4 rounded-xl border" style="border-color: var(--surface-3);" onclick="selectModel('mistral', this)">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium" style="color: var(--text-primary);">mistral</div>
                                <div class="text-sm" style="color: var(--text-muted);">Локальная • Точная</div>
                            </div>
                        </div>
                    </div>
                    <div class="model-selector p-4 rounded-xl border" style="border-color: var(--surface-3);" onclick="selectModel('phi3', this)">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium" style="color: var(--text-primary);">phi3:mini</div>
                                <div class="text-sm" style="color: var(--text-muted);">Локальная • Супер быстрая</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ollama Connection -->
            <div class="mb-8">
                <h4 class="font-semibold mb-4 flex items-center space-x-2" style="color: var(--text-primary);">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                    <span>Подключение Ollama</span>
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-2" style="color: var(--text-muted);">URL Ollama</label>
                        <input type="text" id="ollamaUrl" value="http://localhost:11434" class="w-full rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500" style="background: var(--input-bg); border: 1px solid var(--input-border); color: var(--text-primary);">
                    </div>
                    <button onclick="testConnection()" class="w-full btn-primary py-3 rounded-lg font-medium">Проверить подключение</button>
                </div>
            </div>

            <!-- Bot Personality -->
            <div class="mb-8">
                <h4 class="font-semibold mb-4 flex items-center space-x-2" style="color: var(--text-primary);">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Персона бота</span>
                </h4>
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="radio" name="personality" value="professional" checked class="w-4 h-4">
                        <span style="color: var(--text-primary);">Профессиональный юрист</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="radio" name="personality" value="friendly" class="w-4 h-4">
                        <span style="color: var(--text-primary);">Дружелюбный помощник</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="radio" name="personality" value="concise" class="w-4 h-4">
                        <span style="color: var(--text-primary);">Краткий и точный</span>
                    </label>
                </div>
            </div>

            <!-- Advanced Settings -->
            <div class="mb-8">
                <h4 class="font-semibold mb-4 flex items-center space-x-2" style="color: var(--text-primary);">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span>Дополнительно</span>
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-2" style="color: var(--text-muted);">Температура (креативность)</label>
                        <input type="range" id="temperature" min="0" max="1" step="0.1" value="0.3" class="w-full" style="accent-color: var(--accent);">
                        <div class="flex justify-between text-xs" style="color: var(--text-faint);">
                            <span>Точный</span>
                            <span id="tempValue">0.3</span>
                            <span>Креативный</span>
                        </div>
                    </div>
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="checkbox" checked id="saveHistory" class="w-4 h-4 rounded">
                        <span style="color: var(--text-primary);">Сохранять историю чатов</span>
                    </label>
                </div>
            </div>

            <!-- About -->
            <div class="pt-6 border-t text-center" style="border-color: var(--card-border);">
                <div class="text-sm mb-2" style="color: var(--text-muted);">LegalAI Bot v1.0</div>
                <div class="text-xs" style="color: var(--text-faint);">
                    ⚠️ Бот не заменяет профессиональную юридическую консультацию
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Overlay -->
    <div id="settingsOverlay" onclick="toggleSettings()" class="hidden fixed inset-0 z-40" style="background: var(--overlay-bg);"></div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteOverlay" class="delete-confirmation-overlay hidden"></div>
    <div id="deleteConfirmation" class="delete-confirmation hidden">
        <h4 class="font-semibold mb-3" style="color: var(--text-primary);">Подтвердите удаление</h4>
        <p id="deleteConfirmText" class="text-sm mb-4" style="color: var(--text-muted);"></p>
        <div class="flex justify-end space-x-3">
            <button onclick="hideDeleteConfirmation()" class="px-4 py-2 rounded-lg border transition" style="border-color: var(--surface-3); color: var(--text-secondary);">
                Отмена
            </button>
            <button id="confirmDeleteBtn" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 transition text-white font-medium">
                Удалить
            </button>
        </div>
    </div>
</div>

<script>
    // ===== THEME SYSTEM =====
    const THEME_KEY = 'legalai_theme';

    function initTheme() {
        const saved = localStorage.getItem(THEME_KEY) || 'dark';
        document.body.setAttribute('data-theme', saved);
    }

    function toggleTheme() {
        const current = document.body.getAttribute('data-theme') || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        document.body.setAttribute('data-theme', next);
        localStorage.setItem(THEME_KEY, next);

        // Show toast
        const toastMsg = next === 'light' ? '☀️ Светлая тема активирована' : '🌙 Тёмная тема активирована';
        showToast(toastMsg);
    }

    function showToast(msg) {
        const existing = document.getElementById('themeToast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'themeToast';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            right: 24px;
            padding: 12px 20px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--accent);
            color: var(--text-primary);
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 3000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        `;
        toast.textContent = msg;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    // Initialize theme on load
    initTheme();

    // Application State
    const state = {
        currentModel: 'llama3.1:8b',
        chatHistory: [],
        conversations: [],
        currentConversationId: null,
        isProcessing: false,
        attachedFile: null,
        abortController: null,
        deleteCallback: null
    };

    // Initialization
    document.addEventListener('DOMContentLoaded', () => {
        loadConversations();
        setupTextarea();
        setupTemperatureSlider();
    });

    function setupTextarea() {
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', () => {
            autoResize(textarea);
            updateCharCount();
        });
    }

    function setupTemperatureSlider() {
        const slider = document.getElementById('temperature');
        const display = document.getElementById('tempValue');
        slider.addEventListener('input', () => {
            display.textContent = slider.value;
        });
    }

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 200) + 'px';
    }

    function updateCharCount() {
        const input = document.getElementById('messageInput');
        document.getElementById('charCount').textContent = `${input.value.length} / 4000`;
    }

    function handleKeyDown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    }

    function toggleButtons(isProcessing) {
        const sendBtn = document.getElementById('sendBtn');
        const stopBtn = document.getElementById('stopBtn');
        if (isProcessing) {
            sendBtn.classList.add('hidden-btn');
            stopBtn.classList.remove('hidden-btn');
        } else {
            sendBtn.classList.remove('hidden-btn');
            stopBtn.classList.add('hidden-btn');
        }
    }

    function stopGeneration() {
        if (state.abortController) {
            state.abortController.abort();
            state.abortController = null;
        }
        state.isProcessing = false;
        toggleButtons(false);
        hideTypingIndicator();

        const container = document.getElementById('messagesContainer');
        if (container.lastElementChild) {
            const lastMsg = container.lastElementChild;
            const timeDiv = lastMsg.querySelector('.text-xs');
            if (timeDiv) {
                timeDiv.innerHTML += ' <span style="color: #ef4444; margin-left: 8px;">⏹ Остановлено</span>';
            }
        }

        document.getElementById('messageInput').focus();
        saveConversation();
    }

    async function sendMessage() {
        if (state.isProcessing) return;

        const input = document.getElementById('messageInput');
        const messageText = input.value.trim();
        if (!messageText && !state.attachedFile) return;

        state.abortController = new AbortController();
        state.isProcessing = true;
        toggleButtons(true);

        document.getElementById('welcomeMessage').classList.add('hidden');
        document.getElementById('messagesContainer').classList.remove('hidden');

        let fullMessage = messageText;
        if (state.attachedFile) {
            fullMessage += `\n\n[Прикреплённый файл: ${state.attachedFile.name}]`;
        }

        addMessage('user', fullMessage);
        input.value = '';
        input.style.height = '44px';
        updateCharCount();
        removeFile();

        if (state.chatHistory.length === 1) {
            const title = messageText || state.attachedFile?.name || 'Новый диалог';
            document.getElementById('chatTitle').textContent = title.length > 40 ? title.substring(0, 40) + '...' : title;
        }

        showTypingIndicator();

        try {
            await streamAIResponse(messageText);
        } catch (error) {
            if (error.name === 'AbortError') {
                hideTypingIndicator();
            } else {
                hideTypingIndicator();
                addMessage('ai', `⚠️ **Ошибка**: ${error.message}\n\nПроверьте что Ollama запущен: \`ollama serve\``);
            }
        } finally {
            state.isProcessing = false;
            state.abortController = null;
            toggleButtons(false);
            input.focus();
        }

        saveConversation();
    }

    async function streamAIResponse(message) {
        const personality = document.querySelector('input[name="personality"]:checked')?.value || 'professional';
        const temperature = parseFloat(document.getElementById('temperature').value);
        const ollamaUrl = document.getElementById('ollamaUrl').value || 'http://localhost:11434';

        const systemPrompts = {
            professional: 'Вы профессиональный юрист с 15-летним опытом работы. Отвечайте точно, с ссылками на законодательство Республики Таджикистан. Будьте формальны и подробны. Используйте markdown для форматирования.',
            friendly: 'Вы дружелюбный юридический помощник. Объясняйте сложные понятия простым языком, будьте приветливы и полезны.',
            concise: 'Вы краткий юридический эксперт. Давайте точные ответы без лишних деталей, только суть.'
        };

        const response = await fetch(`${ollamaUrl}/api/generate`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                model: state.currentModel,
                prompt: `${systemPrompts[personality]}\n\nВопрос пользователя: ${message}`,
                stream: true,
                options: { temperature: temperature, num_predict: 1024 }
            }),
            signal: state.abortController.signal
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        if (!response.body) throw new Error('Stream не поддерживается');

        hideTypingIndicator();

        const container = document.getElementById('messagesContainer');
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chat-message flex justify-start';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'max-w-[80%] message-ai rounded-2xl p-4';
        contentDiv.innerHTML = `
            <div class="markdown-content" id="streamingContent"></div>
            <div class="text-xs mt-2" style="color: var(--text-faint);">${new Date().toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})}</div>`;
        msgDiv.appendChild(contentDiv);
        container.appendChild(msgDiv);

        const streamTarget = contentDiv.querySelector('#streamingContent');
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';
        let fullResponse = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop() || '';

            for (const line of lines) {
                if (!line.trim()) continue;
                try {
                    const json = JSON.parse(line);
                    if (json.response) {
                        fullResponse += json.response;
                        streamTarget.innerHTML = marked.parse(fullResponse);
                        scrollToBottom();
                    }
                    if (json.done) break;
                } catch (e) { continue; }
            }
        }

        state.chatHistory.push({ role: 'ai', content: fullResponse, timestamp: new Date().toISOString() });
    }

    function sendQuickQuestion(question) {
        document.getElementById('messageInput').value = question;
        autoResize(document.getElementById('messageInput'));
        updateCharCount();
        sendMessage();
    }

    function addMessage(role, content) {
        const container = document.getElementById('messagesContainer');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;

        const messageContent = document.createElement('div');
        messageContent.className = `max-w-[80%] rounded-2xl p-4 ${role === 'user' ? 'message-user' : 'message-ai'}`;

        if (role === 'ai') {
            messageContent.innerHTML = `<div class="markdown-content">${marked.parse(content)}</div>`;
        } else {
            if (content.includes('[Прикреплённый файл:')) {
                const parts = content.split('\n\n[Прикреплённый файл: ');
                const text = parts[0];
                const filePart = parts[1]?.replace(']', '') || '';
                messageContent.innerHTML = `
                    <div style="white-space: pre-wrap;">${text}</div>
                    ${filePart ? `<div class="file-attachment-badge mt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        ${filePart}
                    </div>` : ''}`;
            } else {
                messageContent.textContent = content;
            }
        }

        const time = document.createElement('div');
        time.className = 'text-xs mt-2';
        time.style.color = role === 'user' ? 'rgba(255,255,255,0.7)' : 'var(--text-faint)';
        time.textContent = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
        messageContent.appendChild(time);

        messageDiv.appendChild(messageContent);
        container.appendChild(messageDiv);

        scrollToBottom();
        state.chatHistory.push({ role, content, timestamp: new Date().toISOString() });
    }

    function scrollToBottom() {
        const chatContainer = document.getElementById('chatContainer');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function showTypingIndicator() {
        const container = document.getElementById('messagesContainer');
        const indicator = document.createElement('div');
        indicator.id = 'typingIndicator';
        indicator.className = 'chat-message flex justify-start';
        indicator.innerHTML = `
            <div class="message-ai rounded-2xl p-4">
                <div class="typing-indicator flex space-x-1">
                    <span class="w-2 h-2 rounded-full"></span>
                    <span class="w-2 h-2 rounded-full"></span>
                    <span class="w-2 h-2 rounded-full"></span>
                </div>
            </div>`;
        container.appendChild(indicator);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    function selectModel(model, element) {
        state.currentModel = model;

        document.querySelectorAll('.model-selector').forEach(el => {
            el.classList.remove('active');
            el.style.borderColor = 'var(--surface-3)';
            const existingCheck = el.querySelector('.check-icon');
            if (existingCheck) existingCheck.remove();
        });

        element.classList.add('active');
        element.style.borderColor = 'var(--accent)';
        element.querySelector('.flex').insertAdjacentHTML('beforeend',
            `<svg class="check-icon w-5 h-5" style="color: var(--accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>`
        );

        document.getElementById('modelStatus').textContent = `${model} Ready`;
    }

    async function testConnection() {
        const url = document.getElementById('ollamaUrl').value.trim();
        try {
            const res = await fetch(`${url}/api/health`);
            if (res.ok) {
                showToast('✅ Ollama подключен!');
            } else {
                throw new Error('Ошибка подключения');
            }
        } catch (e) {
            showToast(`❌ Ошибка: ${e.message}`);
        }
    }

    function toggleSettings() {
        document.getElementById('settingsPanel').classList.toggle('open');
        document.getElementById('settingsOverlay').classList.toggle('hidden');
    }

    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        panel.classList.toggle('open');
    }

    document.addEventListener('click', (e) => {
        const panel = document.getElementById('notificationPanel');
        const notifBtn = e.target.closest('button');
        if (!panel.contains(e.target) && (!notifBtn || !notifBtn.onclick || !notifBtn.onclick.toString().includes('toggleNotifications'))) {
            panel.classList.remove('open');
        }
    });

    function showSection(section) {
        if (section === 'home' || section === 'login') {
            newChat();
        }
    }

    function getDateGroupLabel(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const dateOnly = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        const nowOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const diffDaysExact = Math.floor((nowOnly - dateOnly) / (1000 * 60 * 60 * 24));

        if (diffDaysExact === 0) return 'Сегодня';
        else if (diffDaysExact === 1) return 'Вчера';
        else if (diffDaysExact <= 7) return 'Предыдущие 7 дней';
        else {
            const monthYear = date.toLocaleDateString('ru-RU', { month: 'long', year: 'numeric' });
            return monthYear.charAt(0).toUpperCase() + monthYear.slice(1);
        }
    }

    function getDateGroupOrder(label) {
        if (label === 'Сегодня') return 0;
        if (label === 'Вчера') return 1;
        if (label === 'Предыдущие 7 дней') return 2;
        return 3;
    }

    function formatTimestamp(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return `Сегодня, ${date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})}`;
        else if (diffDays === 1) return `Вчера, ${date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})}`;
        else if (diffDays < 7) return `${date.toLocaleDateString('ru-RU', {weekday:'short'})}, ${date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})}`;
        else return `${date.toLocaleDateString('ru-RU', {day:'2-digit', month:'short'})}, ${date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})}`;
    }

    function formatFullDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleDateString('ru-RU', {
            year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function newChat() {
        if (state.isProcessing) stopGeneration();
        if (state.chatHistory.length > 0) saveConversation();
        state.chatHistory = [];
        state.currentConversationId = null;
        document.getElementById('welcomeMessage').classList.remove('hidden');
        document.getElementById('messagesContainer').classList.add('hidden');
        document.getElementById('messagesContainer').innerHTML = '';
        document.getElementById('chatTitle').textContent = 'Новый диалог';
        document.getElementById('chatSessionInfo').classList.add('hidden');
        updateChatHistoryUI();
    }

    function saveConversation() {
        if (state.chatHistory.length === 0) return;

        if (!state.currentConversationId) {
            state.currentConversationId = Date.now().toString();
        }

        const existingIndex = state.conversations.findIndex(c => c.id === state.currentConversationId);
        const conversation = {
            id: state.currentConversationId,
            title: state.chatHistory[0]?.content?.substring(0, 50) + (state.chatHistory[0]?.content?.length > 50 ? '...' : '') || 'Новый диалог',
            messages: [...state.chatHistory],
            createdAt: existingIndex >= 0 ? state.conversations[existingIndex].createdAt : new Date().toISOString(),
            lastAccessed: new Date().toISOString(),
            messageCount: state.chatHistory.length
        };

        if (existingIndex >= 0) state.conversations[existingIndex] = conversation;
        else state.conversations.unshift(conversation);

        state.conversations.sort((a, b) => new Date(b.lastAccessed) - new Date(a.lastAccessed));

        const shouldSave = document.getElementById('saveHistory')?.checked ?? true;
        if (shouldSave) localStorage.setItem('legalai_conversations', JSON.stringify(state.conversations));
        updateChatHistoryUI();
    }

    function loadConversations() {
        try {
            const saved = localStorage.getItem('legalai_conversations');
            if (saved) {
                state.conversations = JSON.parse(saved);
                state.conversations.sort((a, b) => new Date(b.lastAccessed) - new Date(a.lastAccessed));
            }
        } catch (e) {
            state.conversations = [];
        }
        updateChatHistoryUI();
    }

    function updateChatHistoryUI() {
        const container = document.getElementById('chatHistory');
        const emptyMsg = document.getElementById('emptyHistory');
        container.innerHTML = '';

        if (state.conversations.length === 0) {
            emptyMsg.classList.remove('hidden');
            return;
        }
        emptyMsg.classList.add('hidden');

        const grouped = {};
        state.conversations.forEach(conv => {
            const groupLabel = getDateGroupLabel(conv.lastAccessed);
            if (!grouped[groupLabel]) grouped[groupLabel] = [];
            grouped[groupLabel].push(conv);
        });

        const sortedGroups = Object.keys(grouped).sort((a, b) => {
            const orderA = getDateGroupOrder(a);
            const orderB = getDateGroupOrder(b);
            if (orderA !== orderB) return orderA - orderB;
            if (orderA === 3) {
                const dateA = new Date(grouped[a][0].lastAccessed);
                const dateB = new Date(grouped[b][0].lastAccessed);
                return dateB - dateA;
            }
            return 0;
        });

        sortedGroups.forEach(groupLabel => {
            const titleDiv = document.createElement('div');
            titleDiv.className = 'chat-group-title';
            titleDiv.textContent = groupLabel;
            container.appendChild(titleDiv);

            grouped[groupLabel].forEach(conv => {
                const wrapper = document.createElement('div');
                wrapper.className = 'chat-item-wrapper';

                const item = document.createElement('div');
                item.className = `sidebar-item p-3 rounded-lg cursor-pointer flex items-center space-x-3 ${conv.id === state.currentConversationId ? 'active' : ''}`;
                item.onclick = (e) => {
                    if (!e.target.closest('.delete-btn')) loadConversation(conv.id);
                };

                const createdDate = formatTimestamp(conv.lastAccessed);
                const messageCount = conv.messageCount || conv.messages?.length || 0;

                item.innerHTML = `
                    <svg class="w-5 h-5 flex-shrink-0" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <div class="chat-meta">
                            <span class="text-sm truncate font-medium" style="color: var(--text-primary);">${conv.title}</span>
                            <span class="text-xs flex-shrink-0" style="color: var(--text-faint);">${messageCount} 💬</span>
                        </div>
                        <div class="chat-timestamp">${createdDate}</div>
                    </div>
                    <button class="delete-btn p-1.5 rounded hover:bg-red-500/20 transition" style="color: var(--text-faint);"
                            onclick="event.stopPropagation(); showDeleteConfirmation('${conv.id}', 'chat')"
                            title="Удалить чат">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>`;

                wrapper.appendChild(item);
                container.appendChild(wrapper);
            });
        });
    }

    function loadConversation(id) {
        if (state.isProcessing) stopGeneration();
        const conv = state.conversations.find(c => c.id === id);
        if (!conv) return;

        state.currentConversationId = conv.id;
        state.chatHistory = [];

        document.getElementById('welcomeMessage').classList.add('hidden');
        document.getElementById('messagesContainer').classList.remove('hidden');
        document.getElementById('chatTitle').textContent = conv.title;

        const sessionInfo = document.getElementById('chatSessionInfo');
        sessionInfo.textContent = `Создан: ${formatFullDate(conv.createdAt)} | Последнее посещение: ${formatFullDate(conv.lastAccessed)}`;
        sessionInfo.classList.remove('hidden');

        document.getElementById('messagesContainer').innerHTML = '';

        if (conv.messages && Array.isArray(conv.messages)) {
            conv.messages.forEach(msg => {
                state.chatHistory.push({ role: msg.role, content: msg.content, timestamp: msg.timestamp });
                addMessage(msg.role, msg.content);
            });
        }

        const convIndex = state.conversations.findIndex(c => c.id === id);
        if (convIndex >= 0) {
            state.conversations[convIndex].lastAccessed = new Date().toISOString();
            state.conversations.sort((a, b) => new Date(b.lastAccessed) - new Date(a.lastAccessed));
            const shouldSave = document.getElementById('saveHistory')?.checked ?? true;
            if (shouldSave) localStorage.setItem('legalai_conversations', JSON.stringify(state.conversations));
        }

        updateChatHistoryUI();
    }

    function showDeleteConfirmation(id, type) {
        const overlay = document.getElementById('deleteOverlay');
        const modal = document.getElementById('deleteConfirmation');
        const confirmText = document.getElementById('deleteConfirmText');
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        if (type === 'chat') {
            const conv = state.conversations.find(c => c.id === id);
            confirmText.textContent = `Вы уверены, что хотите удалить чат "${conv?.title || 'Без названия'}"? Это действие нельзя отменить.`;
            state.deleteCallback = () => deleteConversation(id);
        } else if (type === 'all') {
            confirmText.textContent = `Вы уверены, что хотите удалить ВСЮ историю чатов? Это действие нельзя отменить.`;
            state.deleteCallback = deleteAllConversations;
        }

        confirmBtn.onclick = () => {
            if (state.deleteCallback) state.deleteCallback();
            hideDeleteConfirmation();
        };

        overlay.classList.remove('hidden');
        modal.classList.remove('hidden');
    }

    function hideDeleteConfirmation() {
        document.getElementById('deleteOverlay').classList.add('hidden');
        document.getElementById('deleteConfirmation').classList.add('hidden');
        state.deleteCallback = null;
    }

    function deleteConversation(id) {
        const index = state.conversations.findIndex(c => c.id === id);
        if (index >= 0) {
            state.conversations.splice(index, 1);
            if (id === state.currentConversationId) newChat();
            const shouldSave = document.getElementById('saveHistory')?.checked ?? true;
            if (shouldSave) localStorage.setItem('legalai_conversations', JSON.stringify(state.conversations));
            updateChatHistoryUI();
        }
    }

    function clearChat() {
        if (state.isProcessing) stopGeneration();
        if (state.chatHistory.length === 0) return;

        const overlay = document.getElementById('deleteOverlay');
        const modal = document.getElementById('deleteConfirmation');
        const confirmText = document.getElementById('deleteConfirmText');
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        confirmText.innerHTML = `
            <div class="space-y-3">
                <p>Что вы хотите сделать с текущим чатом?</p>
                <div class="space-y-2">
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="radio" name="clearOption" value="clear" checked class="w-4 h-4">
                        <span style="color: var(--text-primary);">Очистить сообщения (сохранить в истории)</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer" style="background: var(--accent-hover);">
                        <input type="radio" name="clearOption" value="delete" class="w-4 h-4">
                        <span style="color: var(--text-primary);">Полностью удалить из истории</span>
                    </label>
                </div>
            </div>`;

        confirmBtn.textContent = 'Подтвердить';
        confirmBtn.className = 'px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 transition text-white font-medium';
        confirmBtn.onclick = () => {
            const option = document.querySelector('input[name="clearOption"]:checked')?.value;
            if (option === 'delete' && state.currentConversationId) {
                deleteConversation(state.currentConversationId);
            } else {
                state.chatHistory = [];
                document.getElementById('welcomeMessage').classList.remove('hidden');
                document.getElementById('messagesContainer').classList.add('hidden');
                document.getElementById('messagesContainer').innerHTML = '';
                document.getElementById('chatTitle').textContent = 'Новый диалог';
                document.getElementById('chatSessionInfo').classList.add('hidden');
                if (state.currentConversationId) {
                    const conv = state.conversations.find(c => c.id === state.currentConversationId);
                    if (conv) {
                        conv.messages = [];
                        conv.messageCount = 0;
                        const shouldSave = document.getElementById('saveHistory')?.checked ?? true;
                        if (shouldSave) localStorage.setItem('legalai_conversations', JSON.stringify(state.conversations));
                    }
                }
            }
            hideDeleteConfirmation();
            confirmBtn.textContent = 'Удалить';
            confirmBtn.className = 'px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 transition text-white font-medium';
        };

        overlay.classList.remove('hidden');
        modal.classList.remove('hidden');
    }

    function deleteAllConversations() {
        state.conversations = [];
        state.chatHistory = [];
        state.currentConversationId = null;
        localStorage.removeItem('legalai_conversations');
        document.getElementById('welcomeMessage').classList.remove('hidden');
        document.getElementById('messagesContainer').classList.add('hidden');
        document.getElementById('messagesContainer').innerHTML = '';
        document.getElementById('chatTitle').textContent = 'Новый диалог';
        document.getElementById('chatSessionInfo').classList.add('hidden');
        updateChatHistoryUI();
    }

    function clearAllHistory() {
        if (state.conversations.length === 0) return;
        showDeleteConfirmation(null, 'all');
    }

    function exportChat() {
        if (state.chatHistory.length === 0) {
            showToast('Чат пуст');
            return;
        }
        let content = `# LegalAI Bot — Экспорт чата\n\nДата: ${new Date().toLocaleDateString('ru-RU')}\n\n---\n\n`;
        state.chatHistory.forEach(msg => {
            const role = msg.role === 'user' ? '👤 Вы' : '🤖 LegalAI';
            const timestamp = msg.timestamp ? new Date(msg.timestamp).toLocaleString('ru-RU') : '';
            content += `### ${role} ${timestamp ? `• ${timestamp}` : ''}\n\n${msg.content}\n\n`;
        });
        const blob = new Blob([content], { type: 'text/markdown' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `legalai_chat_${Date.now()}.md`;
        a.click();
        URL.revokeObjectURL(url);
        showToast('💾 Чат экспортирован');
    }

    function attachFile() {
        document.getElementById('fileAttachment').click();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileAttachment');
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                state.attachedFile = file;
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileBadgeContainer').classList.remove('hidden');
            }
        });
    });

    function removeFile() {
        state.attachedFile = null;
        document.getElementById('fileAttachment').value = '';
        document.getElementById('fileBadgeContainer').classList.add('hidden');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('absolute');
        sidebar.classList.toggle('z-50');
        sidebar.classList.toggle('h-full');
        sidebar.classList.toggle('top-16');
    }
</script>
</body>
</html>
