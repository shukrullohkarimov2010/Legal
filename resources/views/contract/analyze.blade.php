<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LegalAI — интеллектуальный анализ юридических документов с помощью искусственного интеллекта">
    <title>LegalAI Assistant — Умный юридический анализ</title>

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe',
                            500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af'
                        },
                        legal: { dark: '#0f172a', card: '#1e293b', accent: '#6366f1' },
                        status: { success: '#22c55e', warning: '#f59e0b', error: '#ef4444', info: '#3b82f6' }
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'shimmer': 'shimmer 2s linear infinite',
                        'fadeIn': 'fadeIn 0.3s ease-out',
                        'slideIn': 'slideIn 0.2s ease-out'
                    },
                    keyframes: {
                        float: { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-6px)' } },
                        shimmer: { '0%': { backgroundPosition: '-200% 0' }, '100%': { backgroundPosition: '200% 0' } },
                        fadeIn: { '0%': { opacity: '0', transform: 'translateY(8px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        slideIn: { '0%': { opacity: '0', transform: 'translateX(-8px)' }, '100%': { opacity: '1', transform: 'translateX(0)' } }
                    },
                    backdropBlur: { xs: '2px' }
                }
            }
        }
    </script>

    <style>
        * { box-sizing: border-box; }
        body { font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }
        .gradient-bg {
            background: linear-gradient(-45deg, #0f172a, #1e3a8a, #3b82f6, #1e40af);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(30, 41, 59, 0.75);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .card-gradient {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.98) 100%);
            backdrop-filter: blur(16px);
        }
        .glow-text {
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5), 0 0 20px rgba(59, 130, 246, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        .notification-badge {
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .terminal {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            font-family: 'JetBrains Mono', monospace;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        }
        .terminal-line { animation: slideIn 0.2s ease-out; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-8px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .progress-animated {
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 50%, #3b82f6 100%);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }
        .drop-zone {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px dashed #cbd5e1;
        }
        .drop-zone:hover, .drop-zone.dragover {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, 0.06);
            transform: scale(1.01);
        }
        .dark .drop-zone { border-color: #475569; }
        .dark .drop-zone:hover { background: rgba(59, 130, 246, 0.12); }
        .card-hover { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 0.5rem;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, #334155 25%, #475569 50%, #334155 75%);
        }
        .toast {
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toast.show { transform: translateX(0); }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; transition: background 0.2s; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb { background: #475569; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #64748b; }
        .markdown-body { line-height: 1.7; color: inherit; }
        .markdown-body h1, .markdown-body h2, .markdown-body h3 { margin-top: 1.5em; margin-bottom: 0.75em; font-weight: 600; }
        .markdown-body h1 { font-size: 1.5rem; }
        .markdown-body h2 { font-size: 1.25rem; color: #1e40af; }
        .dark .markdown-body h2 { color: #60a5fa; }
        .markdown-body ul { list-style-type: disc; padding-left: 1.5em; margin: 0.5em 0; }
        .markdown-body li { margin: 0.25em 0; }
        .markdown-body code {
            background: rgba(148, 163, 184, 0.15);
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9em;
        }
        .markdown-body pre {
            background: #1e293b; color: #e2e8f0;
            padding: 1em; border-radius: 0.5rem;
            overflow-x: auto; margin: 1em 0;
        }
        .markdown-body blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1em; margin: 1em 0;
            color: #64748b; font-style: italic;
        }
        :focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        .history-card {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }
        .history-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .modal-overlay {
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .modal-overlay .modal-content {
            transform: scale(0.95) translateY(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-overlay.active .modal-content { transform: scale(1) translateY(0); }
        .filter-btn.active { background: #2563eb; color: white; }
        .empty-state-icon { animation: float 3s ease-in-out infinite; }
        .risk-critical { background: #fef2f2; color: #dc2626; }
        .dark .risk-critical { background: rgba(220, 38, 38, 0.2); color: #fca5a5; }
        .risk-medium { background: #fffbeb; color: #d97706; }
        .dark .risk-medium { background: rgba(217, 119, 6, 0.2); color: #fcd34d; }
        .risk-low { background: #f0fdf4; color: #16a34a; }
        .dark .risk-low { background: rgba(22, 163, 74, 0.2); color: #86efac; }
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mobile-menu.open { transform: translateX(0); }

        /* Профиль пользователя */
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            width: 280px;
            border-radius: 12px;
            border: 1px solid rgba(71, 85, 105, 0.4);
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.97), rgba(30, 41, 59, 0.97));
            backdrop-filter: blur(18px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            z-index: 100;
        }
        .profile-dropdown.show {
            display: block;
            animation: dropdownFade 0.2s ease;
        }
        @keyframes dropdownFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .profile-dropdown a, .profile-dropdown button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 1rem;
            color: #cbd5e1;
            text-decoration: none;
            transition: background 0.15s ease;
            border: none;
            background: none;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .profile-dropdown a:hover, .profile-dropdown button:hover {
            background: rgba(59, 130, 246, 0.15);
            color: #fff;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/50 to-indigo-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 min-h-screen font-sans text-slate-900 dark:text-slate-100 transition-colors duration-300 pt-16">

@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : 'П';
@endphp

<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-20 focus:left-4 z-50 px-4 py-2 bg-primary-600 text-white rounded-lg">
    Перейти к основному контенту
</a>

<!-- Navigation -->
<nav class="fixed top-0 left-0 right-0 z-50 card-gradient border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                <div class="relative">
                    <svg class="w-8 h-8 text-blue-500 animate-float" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-slate-900 animate-pulse" aria-label="Сервис активен"></span>
                </div>
                <span class="text-xl font-bold glow-text text-white">LegalAI Pro</span>
            </div>

            <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/50 transition-colors" aria-label="Открыть меню" aria-expanded="false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="text-slate-300 hover:text-white transition">Главная</a>
                <a href="{{route('tasks.create')}}" class="text-slate-300 hover:text-white transition">Возможности</a>
                <a href="{{route('tasks.calc')}}" class="text-slate-300 hover:text-white transition">Калькулятор</a>
                <a href="{{route('tasks.chat')}}" class="text-slate-300 hover:text-white transition">Чат ИИ</a>
                <div class="relative">
                    <button id="notifBtn" class="text-slate-300 hover:text-white transition relative p-1" aria-label="Уведомления">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span id="notifBadge" class="notification-badge absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center font-bold">3</span>
                    </button>
                    <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 card-gradient rounded-xl border border-slate-700 shadow-xl z-50 animate-fadeIn">
                        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                            <h4 class="font-semibold text-white">Уведомления</h4>
                            <button id="clearNotifs" class="text-xs text-slate-400 hover:text-white transition-colors">Очистить</button>
                        </div>
                        <div id="notifList" class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-slate-200">Анализ договора №1234 завершён</p>
                                        <p class="text-xs text-slate-400 mt-1">2 минуты назад</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-slate-200">Обнаружен высокий риск в договоре поставки</p>
                                        <p class="text-xs text-slate-400 mt-1">15 минут назад</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-slate-800/50 cursor-pointer transition-colors">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2 flex-shrink-0"></div>
                                    <div>
                                        <p class="text-sm text-slate-200">Срок действия договора истекает через 7 дней</p>
                                        <p class="text-xs text-slate-400 mt-1">1 час назад</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t border-slate-700 text-center">
                            <a href="#historySection" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Посмотреть все →</a>
                        </div>
                    </div>
                </div>
                <button id="themeToggle" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/50 transition-colors" aria-label="Переключить тему" title="Тёмная/светлая тема">
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button onclick="toggleProfile()" class="flex items-center space-x-2 hover:bg-slate-800/50 rounded-lg px-2 py-1.5 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-lg">
                            {{ $userInitials }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <div class="text-sm font-medium text-white leading-tight">{{ $userName }}</div>
                            <div class="text-[11px] text-slate-400">{{ $userEmail }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="profileDropdown" class="profile-dropdown">
                        <div class="p-4 border-b border-slate-700">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">{{ $userInitials }}</div>
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ $userName }}</div>
                                    <div class="text-xs text-slate-400">{{ $userEmail }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Мой профиль
                            </a>
                            <a href="#">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Настройки
                            </a>
                            <a href="#">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Мои документы
                            </a>
                        </div>
                        <div class="border-t border-slate-700 py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-red-400 hover:text-red-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Выйти
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div id="mobileMenu" class="mobile-menu fixed inset-0 z-40 md:hidden">
    <div id="mobileMenuOverlay" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="absolute right-0 top-0 bottom-0 w-72 card-gradient border-l border-slate-700 p-6 space-y-6">
        <div class="flex items-center justify-between">
            <span class="text-lg font-bold text-white glow-text">Меню</span>
            <button id="mobileMenuClose" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/50 transition-colors" aria-label="Закрыть меню">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="space-y-4">
            <a href="#main-content" class="block text-slate-300 hover:text-white transition-colors py-2 border-b border-slate-700/50">🏠 Главная</a>
            <a href="#features-section" class="block text-slate-300 hover:text-white transition-colors py-2 border-b border-slate-700/50">✨ Возможности</a>
            <a href="#historySection" class="block text-slate-300 hover:text-white transition-colors py-2 border-b border-slate-700/50">📋 История</a>

            <!-- Mobile Profile Section -->
            <div class="pt-4 border-t border-slate-700/50">
                <div class="flex items-center space-x-3 mb-4 px-2">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ $userInitials }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ $userName }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ $userEmail }}</div>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="block text-slate-300 hover:text-white transition-colors py-2 px-2 rounded-lg hover:bg-slate-800/50 text-sm">👤 Мой профиль</a>
                <a href="#" class="block text-slate-300 hover:text-white transition-colors py-2 px-2 rounded-lg hover:bg-slate-800/50 text-sm">⚙️ Настройки</a>
                <a href="#" class="block text-slate-300 hover:text-white transition-colors py-2 px-2 rounded-lg hover:bg-slate-800/50 text-sm">📁 Мои документы</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-400 hover:text-red-300 transition-colors py-2 px-2 rounded-lg hover:bg-slate-800/50 text-sm">🚪 Выйти</button>
                </form>
            </div>
        </nav>
    </div>
</div>

<!-- Main Content -->
<main id="main-content" class="max-w-5xl mx-auto px-4 py-8 md:py-12" role="main">

    <!-- Hero Section -->
    <section class="text-center mb-12 animate-fadeIn" aria-labelledby="hero-title">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 rounded-full text-sm font-medium mb-5">
            <span class="relative flex w-2 h-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full w-2 h-2 bg-primary-500"></span>
            </span>
            Готов к анализу
        </div>
        <h2 id="hero-title" class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-4 tracking-tight">
            Анализ договора за <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">минуты</span>
        </h2>
        <p class="text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Загрузите документ — наш ИИ выявит юридические риски, проверит соответствие законодательству РФ и подготовит экспертное заключение с рекомендациями
        </p>
    </section>

    <!-- Upload Card -->
    <section class="glass rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden card-hover mb-10" aria-labelledby="upload-title">
        <div class="p-6 md:p-8">
            <h3 id="upload-title" class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Загрузите документ для анализа
            </h3>

            <form id="uploadForm" class="space-y-6" novalidate>
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3" for="fileInput">Файл договора</label>
                    <div id="dropZone" class="drop-zone rounded-xl p-8 text-center cursor-pointer bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-100/50 dark:hover:bg-slate-800/50" role="button" tabindex="0" aria-describedby="fileHelp">
                        <input type="file" id="fileInput" class="sr-only" accept=".pdf,.doc,.docx,.txt" required aria-label="Выберите файл договора">
                        <div id="dropContent" class="space-y-4">
                            <div class="w-16 h-16 mx-auto bg-primary-100 dark:bg-primary-900/40 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="text-slate-800 dark:text-slate-200 font-medium mb-1">Перетащите файл сюда</p>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">или нажмите для выбора</p>
                            </div>
                            <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-all shadow-lg shadow-primary-500/25">Выбрать файл</span>
                            <p id="fileHelp" class="text-xs text-slate-400 dark:text-slate-500 mt-3">Поддерживаются: PDF, DOC, DOCX, TXT • Макс. 50 МБ</p>
                        </div>
                        <div id="fileSelected" class="hidden space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-left flex-1 min-w-0">
                                    <p id="selectedFileName" class="font-medium text-slate-800 dark:text-slate-100 truncate"></p>
                                    <p id="selectedFileSize" class="text-sm text-slate-500 dark:text-slate-400"></p>
                                    <p id="selectedFileType" class="text-xs text-slate-400 dark:text-slate-500 mt-0.5"></p>
                                </div>
                                <button type="button" id="changeFile" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium transition-colors">Изменить</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full py-4 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-3 group disabled:opacity-60 disabled:cursor-not-allowed" aria-busy="false">
                    <span class="text-xl group-hover:scale-110 transition-transform">🚀</span>
                    <span>Запустить анализ</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>
        </div>
    </section>

    <!-- Analysis Section -->
    <section id="analysisSection" class="space-y-6 hidden" aria-live="polite" aria-busy="false">
        <div class="glass rounded-xl shadow-md border border-slate-200/60 dark:border-slate-700/60 p-4 flex items-center gap-4" role="status">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/40 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide font-medium">Анализируется</p>
                <p id="fileName" class="font-semibold text-slate-800 dark:text-slate-100 truncate"></p>
            </div>
            <span class="px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-xs font-medium flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                В процессе
            </span>
        </div>

        <div class="glass rounded-xl shadow-md border border-slate-200/60 dark:border-slate-700/60 p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Прогресс анализа</span>
                <span id="progressText" class="text-sm font-bold text-primary-600 dark:text-primary-400 tabular-nums">10%</span>
            </div>
            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                <div id="progressBar" class="progress-animated h-full rounded-full transition-all duration-500 ease-out" style="width: 10%"></div>
            </div>
            <div class="flex justify-between mt-4 text-xs text-slate-400 dark:text-slate-500">
                <span class="flex items-center gap-1"><span id="stage-upload" class="w-2 h-2 rounded-full bg-primary-500"></span> Загрузка</span>
                <span class="flex items-center gap-1"><span id="stage-process" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span> Обработка</span>
                <span class="flex items-center gap-1"><span id="stage-analyze" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span> Анализ</span>
                <span class="flex items-center gap-1"><span id="stage-done" class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span> Готово</span>
            </div>
        </div>

        <div class="terminal rounded-xl shadow-lg overflow-hidden" aria-label="Лог анализа">
            <div class="flex items-center justify-between px-4 py-3 bg-slate-800/80 border-b border-slate-700/50">
                <div class="flex items-center gap-2" aria-hidden="true">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                </div>
                <span class="text-xs text-slate-400 font-mono">terminal • live logs</span>
                <button id="clearLogs" class="text-xs text-slate-400 hover:text-slate-200 transition-colors" aria-label="Очистить лог">✕ Очистить</button>
            </div>
            <div id="logContainer" class="p-4 h-64 overflow-y-auto font-mono text-sm text-slate-300" style="scroll-behavior: smooth;" aria-live="polite"></div>
        </div>

        <article id="finalReport" class="glass rounded-xl shadow-xl border border-green-200/60 dark:border-green-800/40 overflow-hidden hidden" aria-labelledby="report-title">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 id="report-title" class="text-white font-bold text-lg">Анализ завершён</h3>
                <span class="ml-auto px-3 py-1 bg-white/20 rounded-full text-white text-xs font-medium backdrop-blur-sm">✅ Готово</span>
            </div>
            <div class="p-6">
                <div id="reportContent" class="markdown-body text-slate-700 dark:text-slate-300 leading-relaxed"></div>
                <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700 flex flex-wrap gap-3">
                    <button id="downloadBtn" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-2 shadow-lg shadow-primary-500/25">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Скачать PDF
                    </button>
                    <button id="copyBtn" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Копировать
                    </button>
                    <button id="newAnalysis" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium transition-colors ml-auto">🔁 Новый анализ</button>
                </div>
            </div>
        </article>
    </section>

    <!-- History Section -->
    <section id="historySection" class="mt-12 animate-fadeIn" aria-labelledby="history-title">
        <div class="glass rounded-2xl shadow-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden">
            <div class="p-5 md:p-6 border-b border-slate-200/60 dark:border-slate-700/60">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 id="history-title" class="text-lg font-semibold text-slate-800 dark:text-slate-100">История анализов</h3>
                            <p id="historyCount" class="text-xs text-slate-500 dark:text-slate-400">0 документов</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" id="historySearch" placeholder="Поиск..." class="pl-9 pr-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 w-40 md:w-48 transition-all">
                        </div>
                        <button id="clearAllHistory" class="p-2 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors" title="Очистить всю историю">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-4 overflow-x-auto pb-1">
                    <button class="filter-btn active px-3 py-1.5 rounded-lg text-xs font-medium transition-all whitespace-nowrap" data-filter="all">Все</button>
                    <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap" data-filter="critical">🔴 Критические</button>
                    <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap" data-filter="medium">🟡 Средний риск</button>
                    <button class="filter-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all whitespace-nowrap" data-filter="low">🟢 Низкий риск</button>
                </div>
            </div>
            <div id="historyList" class="divide-y divide-slate-200/60 dark:divide-slate-700/60 max-h-[600px] overflow-y-auto"></div>
            <div id="historyEmpty" class="hidden py-16 text-center">
                <div class="empty-state-icon text-5xl mb-4">📂</div>
                <h4 class="text-lg font-medium text-slate-700 dark:text-slate-300 mb-2">История пуста</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Загрузите первый договор для анализа, и он появится здесь с полным отчётом</p>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features-section" class="mt-20 grid md:grid-cols-3 gap-6" aria-labelledby="features-title">
        <h3 id="features-title" class="sr-only">Преимущества сервиса</h3>
        <article class="glass rounded-xl p-6 border border-slate-200/60 dark:border-slate-700/60 card-hover" aria-labelledby="feature-security">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h4 id="feature-security" class="font-semibold text-slate-800 dark:text-slate-100 mb-2">🔒 Конфиденциально</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Документы шифруются при передаче и автоматически удаляются через 24 часа.</p>
        </article>
        <article class="glass rounded-xl p-6 border border-slate-200/60 dark:border-slate-700/60 card-hover" aria-labelledby="feature-speed">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h4 id="feature-speed" class="font-semibold text-slate-800 dark:text-slate-100 mb-2">⚡ Мгновенно</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Анализ до 100 страниц за 2-5 минут благодаря параллельной обработке.</p>
        </article>
        <article class="glass rounded-xl p-6 border border-slate-200/60 dark:border-slate-700/60 card-hover" aria-labelledby="feature-ai">
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <h4 id="feature-ai" class="font-semibold text-slate-800 dark:text-slate-100 mb-2">🧠 Умный ИИ</h4>
            <p class="text-sm text-slate-600 dark:text-slate-400">Модель обучена на 50 000+ юридических документах и актуальном законодательстве РФ.</p>
        </article>
    </section>
</main>

<!-- Footer -->
<footer class="mt-16 py-8 border-t border-slate-200 dark:border-slate-800 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <p class="text-sm text-slate-500 dark:text-slate-400">© 2024 LegalAI Pro. Все права защищены.</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 max-w-2xl mx-auto">
            ⚠️ Сервис предоставляет информационную поддержку на основе ИИ и не заменяет профессиональную юридическую консультацию.
        </p>
    </div>
</footer>

<!-- Toast Container -->
<div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2" aria-live="assertive" aria-atomic="true"></div>

<!-- Report View Modal -->
<div id="reportModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-content bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-5 border-b border-slate-200 dark:border-slate-700">
            <div>
                <h3 id="modal-title" class="text-lg font-bold text-slate-800 dark:text-slate-100"></h3>
                <p id="modal-meta" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"></p>
            </div>
            <button id="modalClose" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" aria-label="Закрыть">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="modalBody" class="p-6 overflow-y-auto flex-1">
            <div id="modalReport" class="markdown-body text-slate-700 dark:text-slate-300"></div>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-700 flex gap-2">
            <button id="modalCopyBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Копировать
            </button>
            <button id="modalDownloadBtn" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Скачать
            </button>
        </div>
    </div>
</div>

<!-- Confirm Dialog -->
<div id="confirmModal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" aria-hidden="true">
    <div class="modal-content bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
        <div class="w-14 h-14 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h4 id="confirmTitle" class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2"></h4>
        <p id="confirmMessage" class="text-sm text-slate-500 dark:text-slate-400 mb-6"></p>
        <div class="flex gap-3">
            <button id="confirmCancel" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl text-sm font-medium transition-colors">Отмена</button>
            <button id="confirmOk" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-medium transition-colors">Удалить</button>
        </div>
    </div>
</div>

<script>
    // ===== CONFIG =====
    const PYTHON_API = "{{ $pythonApiUrl ?? 'http://127.0.0.1:5000' }}";
    const MAX_FILE_SIZE = 50 * 1024 * 1024;
    const ALLOWED_TYPES = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    const HISTORY_KEY = 'legalai_history';
    const MAX_HISTORY_ITEMS = 50;

    // ===== STATE =====
    let eventSource = null;
    let isAnalyzing = false;
    let currentFilter = 'all';
    let historyItems = [];
    let currentModalReport = null;

    // ===== DOM ELEMENTS =====
    const elements = {
        form: document.getElementById('uploadForm'),
        fileInput: document.getElementById('fileInput'),
        dropZone: document.getElementById('dropZone'),
        dropContent: document.getElementById('dropContent'),
        fileSelected: document.getElementById('fileSelected'),
        selectedFileName: document.getElementById('selectedFileName'),
        selectedFileSize: document.getElementById('selectedFileSize'),
        selectedFileType: document.getElementById('selectedFileType'),
        changeFile: document.getElementById('changeFile'),
        logContainer: document.getElementById('logContainer'),
        progressBar: document.getElementById('progressBar'),
        progressText: document.getElementById('progressText'),
        reportContent: document.getElementById('reportContent'),
        analysisSection: document.getElementById('analysisSection'),
        finalReport: document.getElementById('finalReport'),
        submitBtn: document.getElementById('submitBtn'),
        newAnalysis: document.getElementById('newAnalysis'),
        downloadBtn: document.getElementById('downloadBtn'),
        copyBtn: document.getElementById('copyBtn'),
        clearLogs: document.getElementById('clearLogs'),
        themeToggle: document.getElementById('themeToggle'),
        toastContainer: document.getElementById('toastContainer'),
        historyList: document.getElementById('historyList'),
        historyEmpty: document.getElementById('historyEmpty'),
        historyCount: document.getElementById('historyCount'),
        historySearch: document.getElementById('historySearch'),
        clearAllHistory: document.getElementById('clearAllHistory'),
        reportModal: document.getElementById('reportModal'),
        modalClose: document.getElementById('modalClose'),
        modalTitle: document.getElementById('modal-title'),
        modalMeta: document.getElementById('modal-meta'),
        modalReport: document.getElementById('modalReport'),
        modalCopyBtn: document.getElementById('modalCopyBtn'),
        modalDownloadBtn: document.getElementById('modalDownloadBtn'),
        confirmModal: document.getElementById('confirmModal'),
        confirmTitle: document.getElementById('confirmTitle'),
        confirmMessage: document.getElementById('confirmMessage'),
        confirmCancel: document.getElementById('confirmCancel'),
        confirmOk: document.getElementById('confirmOk'),
        stages: {
            upload: document.getElementById('stage-upload'),
            process: document.getElementById('stage-process'),
            analyze: document.getElementById('stage-analyze'),
            done: document.getElementById('stage-done')
        }
    };

    // ===== HISTORY MODULE =====
    const HistoryManager = {
        load() {
            try {
                const data = localStorage.getItem(HISTORY_KEY);
                historyItems = data ? JSON.parse(data) : [];
            } catch (e) { historyItems = []; }
        },
        save() {
            try { localStorage.setItem(HISTORY_KEY, JSON.stringify(historyItems)); }
            catch (e) { utils.showToast('Не удалось сохранить историю', 'warning'); }
        },
        add(item) {
            historyItems.unshift({
                ...item,
                id: item.id || Date.now().toString(36) + Math.random().toString(36).substr(2, 5),
                createdAt: new Date().toISOString()
            });
            if (historyItems.length > MAX_HISTORY_ITEMS) historyItems = historyItems.slice(0, MAX_HISTORY_ITEMS);
            this.save();
            renderHistory();
        },
        remove(id) {
            historyItems = historyItems.filter(item => item.id !== id);
            this.save();
            renderHistory();
        },
        clearAll() {
            historyItems = [];
            this.save();
            renderHistory();
        },
        get(id) { return historyItems.find(item => item.id === id); },
        getFiltered() {
            let filtered = [...historyItems];
            const search = elements.historySearch.value.toLowerCase().trim();
            if (search) filtered = filtered.filter(item => item.filename.toLowerCase().includes(search));
            if (currentFilter !== 'all') filtered = filtered.filter(item => item.riskLevel === currentFilter);
            return filtered;
        }
    };

    function renderHistory() {
        const filtered = HistoryManager.getFiltered();
        const total = historyItems.length;
        elements.historyCount.textContent = `${total} документ${total === 1 ? '' : total < 5 ? 'а' : 'ов'}`;
        if (historyItems.length === 0) {
            elements.historyEmpty.classList.remove('hidden');
            elements.historyList.classList.add('hidden');
            return;
        }
        elements.historyEmpty.classList.add('hidden');
        elements.historyList.classList.remove('hidden');
        if (filtered.length === 0) {
            elements.historyList.innerHTML = `<div class="py-12 text-center"><div class="text-3xl mb-3">🔍</div><p class="text-sm text-slate-500 dark:text-slate-400">Ничего не найдено</p><p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Попробуйте изменить фильтры</p></div>`;
            return;
        }
        elements.historyList.innerHTML = filtered.map(item => {
            const date = new Date(item.createdAt);
            const timeAgo = getTimeAgo(date);
            const riskClass = item.riskLevel === 'critical' ? 'risk-critical' : item.riskLevel === 'medium' ? 'risk-medium' : 'risk-low';
            const riskLabel = item.riskLevel === 'critical' ? 'Критический' : item.riskLevel === 'medium' ? 'Средний' : 'Низкий';
            const fileIcon = getFileIconByExt(item.filename);
            return `<div class="history-card p-4 md:p-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/50 cursor-pointer group" onclick="HistoryUI.openReport('${item.id}')"><div class="flex items-start gap-4"><div class="w-10 h-10 md:w-12 md:h-12 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center flex-shrink-0 text-xl">${fileIcon}</div><div class="flex-1 min-w-0"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><h4 class="font-medium text-slate-800 dark:text-slate-100 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">${escapeHtml(item.filename)}</h4><div class="flex items-center gap-3 mt-1 flex-wrap"><span class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${timeAgo}</span><span class="text-xs text-slate-400 dark:text-slate-500">•</span><span class="text-xs text-slate-500 dark:text-slate-400">${item.fileSize || ''}</span>${item.elapsed ? `<span class="text-xs text-slate-400 dark:text-slate-500">• ${item.elapsed}с</span>` : ''}</div></div><div class="flex items-center gap-2 flex-shrink-0"><span class="px-2.5 py-1 rounded-full text-xs font-medium ${riskClass}">${riskLabel}</span><button onclick="event.stopPropagation(); HistoryUI.confirmDelete('${item.id}')" class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all opacity-0 group-hover:opacity-100" title="Удалить" aria-label="Удалить запись"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div></div>${item.riskSummary ? `<p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">${escapeHtml(item.riskSummary)}</p>` : ''}</div></div></div>`;
        }).join('');
    }

    const HistoryUI = {
        openReport(id) {
            const item = HistoryManager.get(id);
            if (!item) { utils.showToast('Запись не найдена', 'error'); return; }
            currentModalReport = item;
            elements.modalTitle.textContent = item.filename;
            const date = new Date(item.createdAt);
            elements.modalMeta.textContent = `Анализ от ${date.toLocaleDateString('ru-RU')} в ${date.toLocaleTimeString('ru-RU', {hour:'2-digit', minute:'2-digit'})} • Риск: ${item.riskLevel === 'critical' ? 'критический' : item.riskLevel === 'medium' ? 'средний' : 'низкий'}`;
            elements.modalReport.innerHTML = typeof marked !== 'undefined' ? marked.parse(item.report || '') : `<pre class="whitespace-pre-wrap font-mono text-sm">${escapeHtml(item.report || '')}</pre>`;
            elements.reportModal.classList.add('active');
            elements.reportModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        },
        closeReport() {
            elements.reportModal.classList.remove('active');
            elements.reportModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            currentModalReport = null;
        },
        confirmDelete(id) {
            const item = HistoryManager.get(id);
            if (!item) return;
            elements.confirmTitle.textContent = 'Удалить запись?';
            elements.confirmMessage.textContent = `Запись "${item.filename}" будет удалена без возможности восстановления.`;
            elements.confirmOk.onclick = () => { HistoryManager.remove(id); HistoryUI.closeConfirm(); utils.showToast('Запись удалена', 'info'); };
            elements.confirmModal.classList.add('active');
            elements.confirmModal.setAttribute('aria-hidden', 'false');
        },
        closeConfirm() {
            elements.confirmModal.classList.remove('active');
            elements.confirmModal.setAttribute('aria-hidden', 'true');
        },
        confirmClearAll() {
            if (historyItems.length === 0) return;
            elements.confirmTitle.textContent = 'Очистить историю?';
            elements.confirmMessage.textContent = `Все ${historyItems.length} записей будут удалены безвозвратно.`;
            elements.confirmOk.onclick = () => { HistoryManager.clearAll(); HistoryUI.closeConfirm(); utils.showToast('История очищена', 'info'); };
            elements.confirmModal.classList.add('active');
            elements.confirmModal.setAttribute('aria-hidden', 'false');
        }
    };

    function getTimeAgo(date) {
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        if (seconds < 60) return 'только что';
        if (seconds < 3600) return `${Math.floor(seconds / 60)} мин. назад`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)} ч. назад`;
        if (seconds < 604800) return `${Math.floor(seconds / 86400)} дн. назад`;
        return date.toLocaleDateString('ru-RU');
    }

    function getFileIconByExt(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const icons = { 'pdf': '📕', 'doc': '📘', 'docx': '📘', 'txt': '📄' };
        return icons[ext] || '📁';
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // ===== UTILITIES =====
    const utils = {
        formatSize: (bytes) => {
            if (bytes === 0) return '0 Б';
            const k = 1024, sizes = ['Б', 'КБ', 'МБ', 'ГБ'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },
        getFileIcon: (type) => {
            const icons = { 'pdf': '📕', 'doc': '📘', 'docx': '📘', 'txt': '📄' };
            const ext = type.split('.').pop().toLowerCase();
            return icons[ext] || '📁';
        },
        showToast: (message, type = 'info', duration = 4000) => {
            const toast = document.createElement('div');
            const styles = { info: 'bg-slate-800 dark:bg-slate-700 text-white', success: 'bg-green-600 text-white', error: 'bg-red-600 text-white', warning: 'bg-amber-500 text-white' };
            const icons = { info: 'ℹ️', success: '✅', error: '❌', warning: '⚠️' };
            toast.className = `toast flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl ${styles[type]} min-w-[280px] max-w-sm`;
            toast.innerHTML = `<span class="text-lg">${icons[type]}</span><span class="text-sm font-medium flex-1">${message}</span><button class="ml-2 opacity-70 hover:opacity-100 transition-opacity" aria-label="Закрыть">&times;</button>`;
            elements.toastContainer.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('show'));
            const timeout = setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, duration);
            toast.querySelector('button').onclick = () => { clearTimeout(timeout); toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); };
        },
        log: (msg, type = 'info') => {
            const config = { info: { color: '#94a3b8', icon: '•' }, status: { color: '#7dd3fc', icon: '📌' }, success: { color: '#86efac', icon: '✅' }, warning: { color: '#fcd34d', icon: '⚠️' }, error: { color: '#f87171', icon: '❌' }, chunk: { color: '#d8b4fe', icon: '📋' } };
            const { color, icon } = config[type] || config.info;
            const div = document.createElement('div');
            div.className = 'terminal-line py-1.5 px-2 rounded hover:bg-white/5 transition-colors';
            div.innerHTML = `<span class="text-slate-500">[${new Date().toLocaleTimeString('ru-RU')}]</span><span style="color:${color}" class="ml-2 select-none">${icon}</span><span style="color:${color}" class="ml-1.5">${msg}</span>`;
            elements.logContainer.appendChild(div);
            elements.logContainer.scrollTop = elements.logContainer.scrollHeight;
        },
        updateProgress: (percent) => {
            elements.progressBar.style.width = `${percent}%`;
            elements.progressText.textContent = `${percent}%`;
            elements.progressBar.parentElement.setAttribute('aria-valuenow', percent);
            if (percent >= 100) {
                Object.values(elements.stages).forEach(el => { el.classList.remove('bg-primary-500'); el.classList.add('bg-green-500'); });
                elements.progressBar.classList.remove('progress-animated');
                elements.progressBar.style.background = '#22c55e';
            } else if (percent >= 75) elements.stages.analyze.classList.add('bg-primary-500');
            else if (percent >= 40) elements.stages.process.classList.add('bg-primary-500');
        },
        resetUI: () => {
            elements.analysisSection.classList.add('hidden');
            elements.finalReport.classList.add('hidden');
            elements.fileInput.value = '';
            elements.dropContent.classList.remove('hidden');
            elements.fileSelected.classList.add('hidden');
            elements.submitBtn.disabled = false;
            elements.submitBtn.innerHTML = '<span class="text-xl">🚀</span><span>Запустить анализ</span><svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>';
            elements.submitBtn.setAttribute('aria-busy', 'false');
            Object.values(elements.stages).forEach(el => { el.classList.remove('bg-primary-500', 'bg-green-500'); el.classList.add('bg-slate-300', 'dark:bg-slate-600'); });
            elements.stages.upload.classList.add('bg-primary-500');
            elements.stages.upload.classList.remove('bg-slate-300', 'dark:bg-slate-600');
            isAnalyzing = false;
        }
    };

    // ===== FILE HANDLING =====
    function handleFile(file) {
        if (!file) return;
        if (!ALLOWED_TYPES.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|txt)$/i)) {
            utils.showToast('Неподдерживаемый формат файла', 'error');
            utils.log(`❌ Формат не поддерживается: ${file.type}`, 'error');
            return;
        }
        if (file.size > MAX_FILE_SIZE) { utils.showToast(`Файл слишком большой (макс. ${utils.formatSize(MAX_FILE_SIZE)})`, 'error'); return; }
        elements.selectedFileName.textContent = `${utils.getFileIcon(file.name)} ${file.name}`;
        elements.selectedFileSize.textContent = utils.formatSize(file.size);
        elements.selectedFileType.textContent = file.type || 'Неизвестный тип';
        elements.dropContent.classList.add('hidden');
        elements.fileSelected.classList.remove('hidden');
        utils.log(`📎 Файл выбран: ${file.name} (${utils.formatSize(file.size)})`, 'info');
    }

    // ===== PROFILE TOGGLE =====
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('show');
        // Close notification panel if open
        const notifPanel = document.getElementById('notificationPanel');
        if (notifPanel) notifPanel.classList.add('hidden');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const profileDropdown = document.getElementById('profileDropdown');
        const notificationPanel = document.getElementById('notificationPanel');

        // Close profile dropdown
        if (profileDropdown && !e.target.closest('#profileDropdown') && !e.target.closest('[onclick*="toggleProfile"]')) {
            profileDropdown.classList.remove('show');
        }
        // Close notification panel
        if (notificationPanel && !e.target.closest('#notificationPanel') && !e.target.closest('[onclick*="toggleNotifications"]')) {
            notificationPanel.classList.add('hidden');
        }
    });

    // ===== EVENT LISTENERS =====
    elements.dropZone.addEventListener('click', () => elements.fileInput.click());
    elements.dropZone.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); elements.fileInput.click(); } });
    ['dragenter', 'dragover'].forEach(evt => elements.dropZone.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); elements.dropZone.classList.add('dragover'); }));
    ['dragleave', 'drop'].forEach(evt => elements.dropZone.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); elements.dropZone.classList.remove('dragover'); }));
    elements.dropZone.addEventListener('drop', (e) => { const file = e.dataTransfer.files[0]; if (file) { elements.fileInput.files = e.dataTransfer.files; handleFile(file); } });
    elements.fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));
    elements.changeFile.addEventListener('click', (e) => { e.stopPropagation(); elements.fileInput.click(); });

    // Form submission
    elements.form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (isAnalyzing) return;
        const file = elements.fileInput.files[0];
        if (!file) { utils.showToast('Выберите файл для анализа', 'warning'); return; }
        elements.analysisSection.classList.remove('hidden');
        elements.analysisSection.setAttribute('aria-busy', 'true');
        document.getElementById('fileName').textContent = file.name;
        elements.logContainer.innerHTML = '';
        utils.updateProgress(10);
        utils.log('🔗 Подключение к серверу...', 'status');
        isAnalyzing = true;
        elements.submitBtn.disabled = true;
        elements.submitBtn.setAttribute('aria-busy', 'true');
        elements.submitBtn.innerHTML = `<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Загрузка...</span>`;
        try {
            utils.log(`⬆️ Загрузка: ${file.name}`, 'info');
            const id = await uploadFile(file);
            utils.log(`✅ Файл принят • ID: ${id.slice(0, 8)}...`, 'success');
            connectSSE(id, file.name, utils.formatSize(file.size));
        } catch (error) {
            console.error('Upload error:', error);
            utils.log(`❌ Ошибка: ${error.message || 'Неизвестная ошибка'}`, 'error');
            utils.showToast('Ошибка при загрузке файла', 'error');
            resetFormState();
        }
    });

    // SSE Connection
    let currentFileName = '', currentFileSize = '', lastRiskLevel = 'low', criticalCount = 0;
    function connectSSE(requestId, filename, filesize) {
        currentFileName = filename;
        currentFileSize = filesize;
        lastRiskLevel = 'low';
        criticalCount = 0;
        eventSource = new EventSource(`${PYTHON_API}/api/stream/${requestId}`);
        eventSource.onmessage = (e) => {
            try {
                const evt = JSON.parse(e.data);
                switch(evt.type) {
                    case 'status': utils.log(evt.step || 'Обработка...', 'status'); break;
                    case 'progress': utils.updateProgress(evt.progress_percent); break;
                    case 'chunk_result': utils.log(evt.preview, 'chunk'); break;
                    case 'summary_complete':
                        elements.reportContent.innerHTML = typeof marked !== 'undefined' ? marked.parse(evt.content) : `<pre class="whitespace-pre-wrap font-mono text-sm">${evt.content}</pre>`;
                        const reportText = evt.content.toLowerCase();
                        if (reportText.includes('критическ') || reportText.includes('высокий') || reportText.includes('🔴')) lastRiskLevel = 'critical';
                        else if (reportText.includes('средний') || reportText.includes('🟡')) lastRiskLevel = 'medium';
                        else lastRiskLevel = 'low';
                        criticalCount = (reportText.match(/⚠️/g) || []).length;
                        let riskSummary = '';
                        const lines = evt.content.split('\n').filter(l => l.trim());
                        for (let i = 0; i < Math.min(15, lines.length); i++) {
                            if (lines[i].startsWith('#') || lines[i].startsWith('###')) continue;
                            if (lines[i].includes('- ') || lines[i].includes('1.') || lines[i].includes('2.')) {
                                riskSummary += lines[i].replace(/[#*_-]/g, '').trim() + ' ';
                                if (riskSummary.length > 200) break;
                            }
                        }
                        riskSummary = riskSummary.trim().substring(0, 250);
                        if (!riskSummary) riskSummary = 'Анализ завершён. См. полный отчёт.';
                        HistoryManager.add({ filename: currentFileName, fileSize: currentFileSize, riskLevel: lastRiskLevel, riskSummary: riskSummary, criticalCount: criticalCount, report: evt.content, elapsed: evt.meta?.processing_time_sec || null });
                        utils.showToast('Анализ сохранён в историю', 'success', 2500);
                        break;
                    case 'complete':
                        utils.log('🎉 Анализ успешно завершён!', 'success');
                        elements.finalReport.classList.remove('hidden');
                        elements.finalReport.setAttribute('aria-hidden', 'false');
                        utils.updateProgress(100);
                        elements.analysisSection.setAttribute('aria-busy', 'false');
                        eventSource.close();
                        utils.showToast('Анализ завершён! Отчёт готов.', 'success');
                        break;
                    case 'error':
                        utils.log(`❌ ${evt.message}`, 'error');
                        utils.showToast(evt.message || 'Ошибка анализа', 'error');
                        eventSource.close();
                        resetFormState();
                        break;
                }
            } catch (err) { console.error('SSE parse error:', err); }
        };
        eventSource.onerror = () => {
            utils.log('🔴 Потеряно соединение с сервером', 'error');
            utils.showToast('Потеряно соединение. Попробуйте снова.', 'error');
            eventSource.close();
            resetFormState();
        };
    }

    async function uploadFile(file) {
        const fd = new FormData();
        fd.append('file', file);
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 120000);
        try {
            const res = await fetch(`${PYTHON_API}/api/analyze`, { method: 'POST', body: fd, signal: controller.signal, headers: { 'Accept': 'application/json' } });
            clearTimeout(timeout);
            if (!res.ok) { const error = await res.json().catch(() => ({})); throw new Error(error.message || `HTTP ${res.status}`); }
            const data = await res.json();
            return data.request_id;
        } catch (err) {
            clearTimeout(timeout);
            if (err.name === 'AbortError') throw new Error('Превышено время ожидания сервера');
            throw err;
        }
    }

    function resetFormState() {
        isAnalyzing = false;
        elements.submitBtn.disabled = false;
        elements.submitBtn.setAttribute('aria-busy', 'false');
        elements.submitBtn.innerHTML = '<span class="text-xl">🚀</span><span>Запустить анализ</span><svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>';
    }

    // Action buttons
    elements.newAnalysis?.addEventListener('click', () => { utils.resetUI(); window.scrollTo({ top: 0, behavior: 'smooth' }); utils.showToast('Готов к новому анализу', 'info'); });
    elements.copyBtn?.addEventListener('click', async () => { try { await navigator.clipboard.writeText(elements.reportContent.innerText); utils.showToast('Отчёт скопирован в буфер обмена', 'success', 2500); } catch { utils.showToast('Не удалось скопировать', 'error'); } });
    elements.downloadBtn?.addEventListener('click', () => {
        const content = elements.reportContent.innerHTML;
        const blob = new Blob([`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Отчёт LegalAI</title><style>body{font-family:system-ui,sans-serif;line-height:1.6;max-width:800px;margin:40px auto;padding:20px;color:#334155}h1,h2,h3{color:#1e40af}code{background:#f1f5f9;padding:2px 6px;border-radius:4px}pre{background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;overflow-x:auto}</style></head><body><h1>📋 Отчёт LegalAI</h1><p><small>Сгенерирован: ${new Date().toLocaleString('ru-RU')}</small></p>${content}</body></html>`], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `legalai-report-${Date.now()}.html`;
        a.click();
        URL.revokeObjectURL(url);
        utils.showToast('Отчёт скачивается...', 'success', 2000);
    });
    elements.clearLogs?.addEventListener('click', () => { elements.logContainer.innerHTML = ''; utils.log('🧹 Лог очищен', 'info'); });

    // Theme Toggle
    const themeToggle = () => { const isDark = document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light'); };
    if (localStorage.theme === 'dark' || (!localStorage.theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) document.documentElement.classList.add('dark');
    elements.themeToggle?.addEventListener('click', themeToggle);

    // Notification Panel
    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        panel.classList.toggle('hidden');
        // Close profile dropdown if open
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown) profileDropdown.classList.remove('show');
    }
    document.getElementById('notifBtn')?.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleNotifications();
    });
    document.getElementById('clearNotifs')?.addEventListener('click', () => {
        document.getElementById('notifList').innerHTML = '<div class="py-8 text-center text-slate-400 text-sm">Нет уведомлений</div>';
        document.getElementById('notifBadge').style.display = 'none';
    });

    // Mobile Menu
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    mobileMenuBtn?.addEventListener('click', () => {
        mobileMenu.classList.add('open');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    });
    mobileMenuClose?.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    });
    mobileMenuOverlay?.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    });
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey)) { if (e.key === 'Enter' && !isAnalyzing) { e.preventDefault(); elements.form.requestSubmit(); } if (e.key === 'k') { e.preventDefault(); elements.fileInput.click(); } }
        if (e.key === 'Escape') {
            if (elements.reportModal.classList.contains('active')) HistoryUI.closeReport();
            if (elements.confirmModal.classList.contains('active')) HistoryUI.closeConfirm();
            if (mobileMenu && mobileMenu.classList.contains('open')) {
                mobileMenu.classList.remove('open');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
            const notifPanel = document.getElementById('notificationPanel');
            if (notifPanel) notifPanel.classList.add('hidden');
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) profileDropdown.classList.remove('show');
        }
    });

    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => { b.classList.remove('active'); b.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-400'); });
            btn.classList.add('active');
            btn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-400');
            currentFilter = btn.dataset.filter;
            renderHistory();
        });
    });

    // Search
    let searchTimeout;
    elements.historySearch?.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => renderHistory(), 300); });
    elements.clearAllHistory?.addEventListener('click', () => HistoryUI.confirmClearAll());
    elements.modalClose?.addEventListener('click', () => HistoryUI.closeReport());
    elements.reportModal?.addEventListener('click', (e) => { if (e.target === elements.reportModal) HistoryUI.closeReport(); });
    elements.confirmCancel?.addEventListener('click', () => HistoryUI.closeConfirm());
    elements.confirmModal?.addEventListener('click', (e) => { if (e.target === elements.confirmModal) HistoryUI.closeConfirm(); });
    elements.modalCopyBtn?.addEventListener('click', async () => { if (!currentModalReport) return; try { await navigator.clipboard.writeText(currentModalReport.report || ''); utils.showToast('Скопировано в буфер обмена', 'success', 2500); } catch { utils.showToast('Не удалось скопировать', 'error'); } });
    elements.modalDownloadBtn?.addEventListener('click', () => {
        if (!currentModalReport) return;
        const content = currentModalReport.report || '';
        const blob = new Blob([`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Отчёт LegalAI — ${escapeHtml(currentModalReport.filename)}</title><style>body{font-family:system-ui,sans-serif;line-height:1.6;max-width:800px;margin:40px auto;padding:20px;color:#334155}h1,h2,h3{color:#1e40af}code{background:#f1f5f9;padding:2px 6px;border-radius:4px}pre{background:#1e293b;color:#e2e8f0;padding:16px;border-radius:8px;overflow-x:auto}.meta{color:#64748b;font-size:0.9em;margin-bottom:1em;padding-bottom:1em;border-bottom:1px solid #e2e8f0}</style></head><body><h1>📋 ${escapeHtml(currentModalReport.filename)}</h1><p class="meta">Дата: ${new Date(currentModalReport.createdAt).toLocaleString('ru-RU')} | Риск: ${currentModalReport.riskLevel === 'critical' ? 'Критический' : currentModalReport.riskLevel === 'medium' ? 'Средний' : 'Низкий'}</p>${content}</body></html>`], { type: 'text/html' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `legalai-${Date.now()}.html`;
        a.click();
        URL.revokeObjectURL(url);
        utils.showToast('Отчёт скачивается...', 'success', 2000);
    });

    // ===== INIT =====
    HistoryManager.load();
    renderHistory();
    utils.log('🟢 LegalAI готов к работе', 'success');
</script>
</body>
</html>
