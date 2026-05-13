<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.app_name') }} — {{ __('ui.dashboard_title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fontsource/inter@4.5.15/index.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: var(--primary-gradient);
        }

        .gradient-bg-secondary {
            background: var(--secondary-gradient);
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .card {
            background: #1a202c;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transform: translateY(-4px);
        }

        .upload-zone {
            border: 3px dashed #cbd5e0;
            transition: all 0.4s ease;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
        }

        .upload-zone:hover, .upload-zone.dragover {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: scale(1.02);
        }

        .risk-gauge {
            width: 140px;
            height: 140px;
            position: relative;
        }

        .gauge-circle {
            fill: none;
            stroke: #e2e8f0;
            stroke-width: 10;
        }

        .dark .gauge-circle {
            stroke: #2d3748;
        }

        .gauge-fill {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gauge-low {
            stroke: url(#gradientLow);
        }
        .gauge-medium {
            stroke: url(#gradientMedium);
        }
        .gauge-high {
            stroke: url(#gradientHigh);
        }

        .progress-bar {
            background: var(--primary-gradient);
            border-radius: 8px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .risk-item {
            border-left: 5px solid;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .risk-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .risk-item:hover::before {
            opacity: 1;
        }

        .risk-item:hover {
            transform: translateX(8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .risk-low { border-left-color: #48bb78; }
        .risk-medium { border-left-color: #ed8936; }
        .risk-high { border-left-color: #f56565; }

        .badge {
            padding: 6px 16px;
            border-radius: 24px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        .badge-low {
            background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
            color: #276749;
            box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
        }
        .badge-medium {
            background: linear-gradient(135deg, #feebc8 0%, #fbd38d 100%);
            color: #c05621;
            box-shadow: 0 2px 8px rgba(237, 137, 54, 0.3);
        }
        .badge-high {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
            box-shadow: 0 2px 8px rgba(245, 101, 101, 0.3);
        }

        .tab-active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .analysis-step {
            opacity: 0.6;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .analysis-step.active {
            opacity: 1;
            transform: scale(1.03);
        }

        .analysis-step.completed {
            opacity: 1;
        }

        .analysis-step.completed .progress-bar {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 12px;
        }

        .dark .skeleton {
            background: linear-gradient(90deg, #2d3748 25%, #4a5568 50%, #2d3748 75%);
            background-size: 200% 100%;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .chat-message {
            animation: messageSlide 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes messageSlide {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .heatmap-cell {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .heatmap-cell:hover {
            transform: scale(1.15);
            z-index: 10;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .heatmap-cell::after {
            content: attr(data-risk);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-5px);
            background: #1a202c;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .heatmap-cell:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-10px);
        }

        .highlight {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
            padding: 4px 8px;
            border-radius: 6px;
            border: 2px solid #667eea;
        }

        .toggle-switch {
            position: relative;
            width: 56px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #cbd5e0 0%, #a0aec0 100%);
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 30px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background: white;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        input:checked + .toggle-slider {
            background: var(--primary-gradient);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        .tooltip {
            position: relative;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-8px);
            background: #1a202c;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-12px);
        }

        .pulse-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .version-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        .comparison-diff {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.15) 0%, rgba(245, 101, 101, 0.25) 100%);
            text-decoration: line-through;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .comparison-add {
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.15) 0%, rgba(72, 187, 120, 0.25) 100%);
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .floating-action {
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 50;
        }

        .floating-action button {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .floating-action button:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .modal-overlay {
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
            border: 1px solid rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }

        .btn-primary {
            background: var(--primary-gradient);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .animate-pulse-slow {
            animation: pulse 3s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }

        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 100% { transform: translateY(0); opacity: 0.4; }
            50% { transform: translateY(-5px); opacity: 1; }
        }

        .clause-number {
            background: var(--primary-gradient);
            transition: all 0.3s ease;
        }

        .clause-number:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .search-highlight {
            background: linear-gradient(135deg, rgba(255, 219, 88, 0.5) 0%, rgba(255, 193, 7, 0.5) 100%);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 600;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-gradient);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        .loading-spinner {
            border: 3px solid rgba(102, 126, 234, 0.2);
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #667eea;
            animation: confetti-fall 3s linear forwards;
            z-index: 1000;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .sidebar {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .quick-action-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .quick-action-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .quick-action-btn:hover::after {
            width: 200px;
            height: 200px;
        }

        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .risk-gauge {
                width: 100px;
                height: 100px;
            }

            .gauge-circle, .gauge-fill {
                r: 42;
            }

            .floating-action {
                bottom: 20px;
                right: 20px;
            }
        }

        /* Print styles */
        @media print {
            .floating-action, .upload-zone, .btn-primary {
                display: none !important;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-purple-50 to-blue-50 min-h-screen dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 dark:text-white transition-colors duration-300">

<!-- SVG Gradients -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="gradientLow" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#48bb78;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#38a169;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="gradientMedium" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#ed8936;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#dd6b20;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="gradientHigh" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#f56565;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#e53e3e;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>

<!-- Header -->
<header class="gradient-bg text-white py-5 shadow-2xl sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm animate-float">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">AI Legal Analyzer Pro</h1>
                    <p class="text-xs opacity-80 font-medium">Анализ договоров • Оценка рисков • AI-помощник</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 flex-wrap">
                <button class="bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-xl transition tooltip flex items-center space-x-2 backdrop-blur-sm" data-tooltip="База шаблонов" onclick="openTemplates()">
                    <span>📚</span>
                    <span class="hidden sm:inline">{{ __('ui.dashboard_chip_templates') ?? 'Шаблоны' }}</span>
                </button>
                <button class="bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-xl transition tooltip flex items-center space-x-2 backdrop-blur-sm relative" data-tooltip="История версий" onclick="openVersionHistory()">
                    <span>📜</span>
                    <span class="hidden sm:inline">{{ __('ui.dashboard_builder_badge') ?? 'История' }}</span>
                    <span class="notification-badge">3</span>
                </button>
                <button class="bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-xl transition tooltip flex items-center space-x-2 backdrop-blur-sm" data-tooltip="Сравнение документов" onclick="openComparison()">
                    <span>🔄</span>
                    <span class="hidden sm:inline">{{ __('ui.feature_version_compare') ?? 'Сравнить' }}</span>
                </button>

                <!-- Language Switcher -->
                @php $currentLocale = app()->getLocale(); @endphp
                <div class="flex items-center space-x-2 ml-2">
                    <a href="{{ route('locale.switch', ['locale' => 'ru']) }}" class="px-3 py-1 rounded-md font-semibold text-sm transition {{ $currentLocale === 'ru' ? 'bg-white text-indigo-700 shadow' : 'bg-white/10 text-white' }}">RU</a>
                    <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="px-3 py-1 rounded-md font-semibold text-sm transition {{ $currentLocale === 'en' ? 'bg-white text-indigo-700 shadow' : 'bg-white/10 text-white' }}">EN</a>
                    <a href="{{ route('locale.switch', ['locale' => 'tg']) }}" class="px-3 py-1 rounded-md font-semibold text-sm transition {{ $currentLocale === 'tg' ? 'bg-white text-indigo-700 shadow' : 'bg-white/10 text-white' }}">TJ</a>
                </div>

                <button class="bg-white/20 hover:bg-white/30 px-4 py-2.5 rounded-xl transition tooltip flex items-center space-x-2 backdrop-blur-sm" data-tooltip="Настройки" onclick="toggleSettings()">
                    <span>⚙️</span>
                    <span class="hidden sm:inline">{{ __('ui.settings') ?? 'Настройки' }}</span>
                </button>

                <label class="toggle-switch tooltip ml-2" data-tooltip="Тёмная тема">
                    <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()">
                    <span class="toggle-slider"></span>
                </label>

                <div class="relative">
                    <button id="userMenuBtn" class="w-11 h-11 rounded-full bg-white/30 flex items-center justify-center cursor-pointer hover:bg-white/40 transition border-2 border-white/50 focus:outline-none" onclick="document.getElementById('userMenu').classList.toggle('hidden')">
                        <span class="text-lg">👤</span>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-44 bg-white text-gray-800 dark:bg-gray-800 dark:text-white rounded-lg shadow-lg overflow-hidden z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('ui.my_profile') ?? 'Профиль' }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('ui.nav_logout') ?? 'Выйти' }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container mx-auto px-4 py-8">
    <!-- Quick Actions Bar -->
    <div class="card p-5 mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-3 flex-wrap">
                <button class="btn-primary text-white px-6 py-3 rounded-xl flex items-center space-x-2 font-semibold" onclick="startNewAnalysis()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Новый анализ</span>
                </button>
                <button class="quick-action-btn bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-5 py-3 rounded-xl flex items-center space-x-2 font-medium" onclick="openAIChat()">
                    <span>🤖</span>
                    <span>AI-помощник</span>
                </button>
                <button class="quick-action-btn bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-5 py-3 rounded-xl flex items-center space-x-2 font-medium" onclick="openChecklist()">
                    <span>✅</span>
                    <span>Чек-лист</span>
                </button>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" placeholder="🔍 Поиск по документу..." class="px-5 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent w-64 transition-all" id="searchInput" onkeyup="searchDocument()">
                </div>
                <button class="bg-gray-100 dark:bg-gray-700 p-3 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition tooltip" data-tooltip="Режим выделения" onclick="toggleHighlightMode()" id="highlightBtn">
                    <span class="text-lg">🖍️</span>
                </button>
                <button class="bg-gray-100 dark:bg-gray-700 p-3 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition tooltip" data-tooltip="Добавить заметку" onclick="addAnnotation()" id="annotateBtn">
                    <span class="text-lg">📝</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Left Column - Upload & Analysis -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Upload Zone -->
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                    <span class="mr-2">📄</span>
                    Загрузка документа
                </h2>
                <div class="upload-zone rounded-2xl p-8 text-center cursor-pointer" id="uploadZone">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 font-medium">Перетащите файл или</p>
                    <button class="btn-primary text-white px-6 py-3 rounded-xl text-sm font-semibold" onclick="document.getElementById('fileInput').click()">
                        Выбрать файл
                    </button>
                    <input type="file" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.txt" multiple>
                    <p class="text-xs text-gray-400 mt-4">PDF, DOC, DOCX, TXT (макс. 50MB)</p>
                </div>
                <!-- Uploaded Files -->
                <div id="uploadedFiles" class="hidden mt-5 space-y-3"></div>
            </div>

            <!-- Analysis Progress -->
            <div class="card p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                    <span class="mr-2">🔍</span>
                    Процесс анализа
                </h2>
                <div class="space-y-4">
                    <div class="analysis-step flex items-center space-x-3" id="step1">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 transition-all">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">1</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">Извлечение текста</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="progress-bar h-2 rounded-full" style="width: 0%" id="progress1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="analysis-step flex items-center space-x-3" id="step2">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 transition-all">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">2</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">Анализ структуры</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="progress-bar h-2 rounded-full" style="width: 0%" id="progress2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="analysis-step flex items-center space-x-3" id="step3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 transition-all">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">3</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">Оценка рисков</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="progress-bar h-2 rounded-full" style="width: 0%" id="progress3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="analysis-step flex items-center space-x-3" id="step4">
                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 transition-all">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">4</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">Генерация отчёта</p>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="progress-bar h-2 rounded-full" style="width: 0%" id="progress4"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="w-full mt-6 btn-primary text-white py-3.5 rounded-xl font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed" id="analyzeBtn" onclick="startAnalysis()" disabled>
                    🚀 Начать анализ
                </button>
            </div>

            <!-- Quick Stats -->
            <div class="card p-5">
                <h2 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                    <span class="mr-2">📊</span>
                    Статистика
                </h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="stat-card text-center p-4 rounded-xl">
                        <p class="text-2xl font-bold gradient-text" id="totalClauses">0</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Пунктов</p>
                    </div>
                    <div class="stat-card text-center p-4 rounded-xl">
                        <p class="text-2xl font-bold text-red-500" id="totalRisks">0</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Рисков</p>
                    </div>
                    <div class="stat-card text-center p-4 rounded-xl">
                        <p class="text-2xl font-bold text-green-500" id="complianceScore">0%</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Соответствие</p>
                    </div>
                    <div class="stat-card text-center p-4 rounded-xl">
                        <p class="text-2xl font-bold text-orange-500" id="reviewTime">0 мин</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium mt-1">Время</p>
                    </div>
                </div>
            </div>

            <!-- Risk Heatmap -->
            <div class="card p-5">
                <h2 class="text-sm font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                    <span class="mr-2">🔥</span>
                    Тепловая карта рисков
                </h2>
                <div class="grid grid-cols-5 gap-2" id="riskHeatmap"></div>
                <div class="flex items-center justify-between mt-4 text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <span>Низкий</span>
                    <div class="flex space-x-1">
                        <div class="w-5 h-5 rounded bg-green-400 shadow"></div>
                        <div class="w-5 h-5 rounded bg-yellow-400 shadow"></div>
                        <div class="w-5 h-5 rounded bg-orange-400 shadow"></div>
                        <div class="w-5 h-5 rounded bg-red-400 shadow"></div>
                    </div>
                    <span>Высокий</span>
                </div>
            </div>
        </div>

        <!-- Right Column - Results -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Tabs -->
            <div class="card p-2">
                <div class="flex space-x-2 overflow-x-auto scrollbar-thin pb-2">
                    <button class="tab-active px-5 py-3 rounded-xl font-semibold text-sm transition flex-shrink-0" onclick="switchTab('overview')" id="tab-overview">
                        📋 Обзор
                    </button>
                    <button class="px-5 py-3 rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0" onclick="switchTab('risks')" id="tab-risks">
                        ⚠️ Риски
                    </button>
                    <button class="px-5 py-3 rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0" onclick="switchTab('clauses')" id="tab-clauses">
                        📝 Пункты
                    </button>
                    <button class="px-5 py-3 rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0" onclick="switchTab('recommendations')" id="tab-recommendations">
                        💡 Рекомендации
                    </button>
                    <button class="px-5 py-3 rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0" onclick="switchTab('compliance')" id="tab-compliance">
                        ✅ Комплаенс
                    </button>
                    <button class="px-5 py-3 rounded-xl font-medium text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex-shrink-0" onclick="switchTab('ai-chat')" id="tab-ai-chat">
                        🤖 AI-чат
                    </button>
                </div>
            </div>

            <!-- Overview Tab -->
            <div id="overview-content" class="space-y-6">
                <!-- Risk Gauges -->
                <div class="card p-8">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-8 flex items-center">
                        <span class="mr-2">🎯</span>
                        Оценка рисков
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="text-center group">
                            <div class="relative inline-block">
                                <svg class="risk-gauge mx-auto mb-4" viewBox="0 0 120 120">
                                    <circle class="gauge-circle" cx="60" cy="60" r="52"/>
                                    <circle class="gauge-fill gauge-low" cx="60" cy="60" r="52"
                                            stroke-dasharray="326.56" stroke-dashoffset="326.56" id="gauge1"/>
                                    <text x="60" y="70" text-anchor="middle" class="text-2xl font-bold fill-gray-700 dark:fill-white" id="gauge1-value">0%</text>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-green-400/20 to-transparent"></div>
                                </div>
                            </div>
                            <p class="font-bold text-gray-700 dark:text-gray-200 text-lg">Комплаенс</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" id="gauge1-label">Низкий риск</p>
                        </div>
                        <div class="text-center group">
                            <div class="relative inline-block">
                                <svg class="risk-gauge mx-auto mb-4" viewBox="0 0 120 120">
                                    <circle class="gauge-circle" cx="60" cy="60" r="52"/>
                                    <circle class="gauge-fill gauge-medium" cx="60" cy="60" r="52"
                                            stroke-dasharray="326.56" stroke-dashoffset="326.56" id="gauge2"/>
                                    <text x="60" y="70" text-anchor="middle" class="text-2xl font-bold fill-gray-700 dark:fill-white" id="gauge2-value">0%</text>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-orange-400/20 to-transparent"></div>
                                </div>
                            </div>
                            <p class="font-bold text-gray-700 dark:text-gray-200 text-lg">Исполнение</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" id="gauge2-label">Средний риск</p>
                        </div>
                        <div class="text-center group">
                            <div class="relative inline-block">
                                <svg class="risk-gauge mx-auto mb-4" viewBox="0 0 120 120">
                                    <circle class="gauge-circle" cx="60" cy="60" r="52"/>
                                    <circle class="gauge-fill gauge-high" cx="60" cy="60" r="52"
                                            stroke-dasharray="326.56" stroke-dashoffset="326.56" id="gauge3"/>
                                    <text x="60" y="70" text-anchor="middle" class="text-2xl font-bold fill-gray-700 dark:fill-white" id="gauge3-value">0%</text>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-red-400/20 to-transparent"></div>
                                </div>
                            </div>
                            <p class="font-bold text-gray-700 dark:text-gray-200 text-lg">Споры</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" id="gauge3-label">Высокий риск</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="card p-6">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                            <span class="mr-2">📈</span>
                            Распределение рисков
                        </h2>
                        <canvas id="riskChart" ></canvas>
                    </div>
                    <div class="card p-6">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                            <span class="mr-2">📊</span>
                            По категориям
                        </h2>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Key Findings -->
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                        <span class="mr-2">🔑</span>
                        Ключевые выводы
                    </h2>
                    <div class="space-y-3" id="keyFindings">
                        <div class="skeleton h-16 rounded-xl"></div>
                        <div class="skeleton h-16 rounded-xl"></div>
                        <div class="skeleton h-16 rounded-xl"></div>
                    </div>
                </div>
            </div>

            <!-- Risks Tab -->
            <div id="risks-content" class="hidden space-y-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <span class="mr-2">⚠️</span>
                            Выявленные риски
                        </h2>
                        <div class="flex items-center space-x-2 flex-wrap gap-2">
                            <input type="text" placeholder="Фильтр рисков..." class="px-4 py-2 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" id="riskFilter" onkeyup="filterRisksSearch()">
                            <button class="badge badge-low cursor-pointer" onclick="filterRisks('all')">Все</button>
                            <button class="badge badge-low cursor-pointer" onclick="filterRisks('low')">Низкие</button>
                            <button class="badge badge-medium cursor-pointer" onclick="filterRisks('medium')">Средние</button>
                            <button class="badge badge-high cursor-pointer" onclick="filterRisks('high')">Высокие</button>
                        </div>
                    </div>
                    <div class="space-y-3 scrollbar-thin max-h-96 overflow-y-auto" id="risksList"></div>
                </div>
            </div>

            <!-- Clauses Tab -->
            <div id="clauses-content" class="hidden space-y-6">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <span class="mr-2">📝</span>
                            Анализ пунктов договора
                        </h2>
                        <div class="flex items-center space-x-2">
                            <button class="text-sm bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="expandAllClauses()">
                                Развернуть все
                            </button>
                            <button class="text-sm bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="collapseAllClauses()">
                                Свернуть все
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3 scrollbar-thin max-h-96 overflow-y-auto" id="clausesList"></div>
                </div>
            </div>

            <!-- Recommendations Tab -->
            <div id="recommendations-content" class="hidden space-y-6">
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                        <span class="mr-2">💡</span>
                        Рекомендации по улучшению
                    </h2>
                    <div class="space-y-4 scrollbar-thin max-h-96 overflow-y-auto" id="recommendationsList"></div>
                </div>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                        <span class="mr-2">📥</span>
                        Экспорт отчёта
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button class="btn-primary bg-red-500 hover:bg-red-600 text-white py-4 rounded-xl font-bold transition flex items-center justify-center space-x-2" onclick="exportReport('pdf')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>PDF</span>
                        </button>
                        <button class="btn-primary bg-blue-500 hover:bg-blue-600 text-white py-4 rounded-xl font-bold transition flex items-center justify-center space-x-2" onclick="exportReport('docx')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>DOCX</span>
                        </button>
                        <button class="btn-primary bg-purple-500 hover:bg-purple-600 text-white py-4 rounded-xl font-bold transition flex items-center justify-center space-x-2" onclick="exportReport('json')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                            <span>JSON</span>
                        </button>
                        <button class="btn-primary bg-green-500 hover:bg-green-600 text-white py-4 rounded-xl font-bold transition flex items-center justify-center space-x-2" onclick="exportReport('email')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Email</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Compliance Tab -->
            <div id="compliance-content" class="hidden space-y-6">
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                        <span class="mr-2">✅</span>
                        Чек-лист соответствия
                    </h2>
                    <div class="space-y-4 scrollbar-thin max-h-96 overflow-y-auto" id="complianceList"></div>
                    <div class="mt-8 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Общий score соответствия</p>
                                <p class="text-4xl font-bold gradient-text mt-1" id="complianceTotal">0%</p>
                            </div>
                            <button class="btn-primary text-white px-8 py-4 rounded-xl font-bold hover:opacity-90 transition" onclick="generateComplianceReport()">
                                📄 Сформировать отчёт
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Chat Tab -->
            <div id="ai-chat-content" class="hidden space-y-6">
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-5 flex items-center">
                        <span class="mr-2">🤖</span>
                        AI-юридический помощник
                    </h2>
                    <div class="bg-gradient-to-br from-gray-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-5 h-96 overflow-y-auto scrollbar-thin mb-5 border-2 border-gray-200 dark:border-gray-600" id="chatContainer">
                        <div class="chat-message flex items-start space-x-3 mb-4">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg">
                                🤖
                            </div>
                            <div class="bg-white dark:bg-gray-700 rounded-2xl p-4 flex-1 shadow-md border border-gray-100 dark:border-gray-600">
                                <p class="text-gray-800 dark:text-gray-200 font-medium">Здравствуйте! Я ваш AI-юридический помощник. Задавайте мне вопросы по договору, рискам или рекомендациям. Чем могу помочь?</p>
                                <p class="text-xs text-gray-400 mt-2">Только что</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="text" placeholder="Задайте вопрос по договору..." class="flex-1 px-5 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent font-medium" id="chatInput" onkeypress="handleChatKeyPress(event)">
                        <button class="btn-primary text-white px-7 py-4 rounded-xl font-bold hover:opacity-90 transition" onclick="sendChatMessage()">
                            ➤
                        </button>
                    </div>
                    <div class="flex items-center space-x-2 mt-5 flex-wrap gap-2">
                        <button class="text-xs bg-gray-100 dark:bg-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="quickQuestion('Какие самые критические риски?')">
                            ❓ Критические риски
                        </button>
                        <button class="text-xs bg-gray-100 dark:bg-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="quickQuestion('Как улучшить договор?')">
                            💡 Улучшения
                        </button>
                        <button class="text-xs bg-gray-100 dark:bg-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="quickQuestion('Соответствует ли договор законодательству?')">
                            ⚖️ Законодательство
                        </button>
                        <button class="text-xs bg-gray-100 dark:bg-gray-700 px-4 py-2.5 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium" onclick="quickQuestion('Какие пункты требуют внимания?')">
                            📋 Важные пункты
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<div class="floating-action">
    <button class="w-16 h-16 rounded-full btn-primary text-white shadow-2xl flex items-center justify-center text-2xl" onclick="scrollToTop()" id="scrollTopBtn" style="display: none;">
        ↑
    </button>
</div>

<!-- Modals -->
<!-- Version History Modal -->
<div id="versionModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center modal-overlay" onclick="closeModal('versionModal')">
    <div class="card p-6 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto scrollbar-thin modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <span class="mr-2">📜</span>
                История версий
            </h2>
            <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center" onclick="closeModal('versionModal')">✕</button>
        </div>
        <div class="version-timeline relative pl-10 space-y-6" id="versionTimeline"></div>
    </div>
</div>

<!-- Comparison Modal -->
<div id="comparisonModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center modal-overlay" onclick="closeModal('comparisonModal')">
    <div class="card p-6 w-full max-w-4xl mx-4 max-h-[80vh] overflow-y-auto scrollbar-thin modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <span class="mr-2">🔄</span>
                Сравнение документов
            </h2>
            <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center" onclick="closeModal('comparisonModal')">✕</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Документ A</label>
                <select class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option>Договор_поставки_2024_v1.pdf</option>
                    <option>Договор_поставки_2024_v2.pdf</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Документ B</label>
                <select class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option>Договор_поставки_2024_v2.pdf</option>
                    <option>Договор_поставки_2024_v3.pdf</option>
                </select>
            </div>
        </div>
        <button class="w-full btn-primary text-white py-4 rounded-xl font-bold hover:opacity-90 transition mb-6" onclick="compareDocuments()">
            🔍 Сравнить
        </button>
        <div id="comparisonResult" class="hidden">
            <div class="bg-gradient-to-br from-gray-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-5 border-2 border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4 text-lg">Результаты сравнения</h3>
                <div class="space-y-3 text-sm">
                    <p><span class="comparison-diff">Удалено: 3 пункта</span></p>
                    <p><span class="comparison-add">Добавлено: 5 пунктов</span></p>
                    <p class="font-medium">Изменено: 8 пунктов</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Templates Modal -->
<div id="templatesModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center modal-overlay" onclick="closeModal('templatesModal')">
    <div class="card p-6 w-full max-w-4xl mx-4 max-h-[80vh] overflow-y-auto scrollbar-thin modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <span class="mr-2">📚</span>
                База шаблонов
            </h2>
            <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center" onclick="closeModal('templatesModal')">✕</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="templatesGrid"></div>
    </div>
</div>

<!-- Settings Modal -->
<div id="settingsModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center modal-overlay" onclick="closeModal('settingsModal')">
    <div class="card p-6 w-full max-w-lg mx-4 modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                <span class="mr-2">⚙️</span>
                Настройки
            </h2>
            <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center justify-center" onclick="closeModal('settingsModal')">✕</button>
        </div>
        <div class="space-y-5">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div>
                    <p class="font-bold text-gray-800 dark:text-white">Тёмная тема</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Включить тёмный режим интерфейса</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="settingsDarkMode" onchange="toggleDarkMode()">
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div>
                    <p class="font-bold text-gray-800 dark:text-white">Уведомления</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Получать уведомления о завершении анализа</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div>
                    <p class="font-bold text-gray-800 dark:text-white">Авто-сохранение</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Автоматически сохранять результаты</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div>
                    <p class="font-bold text-gray-800 dark:text-white">Язык анализа</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Основной язык для анализа документов</p>
                </div>
                <select class="px-4 py-2.5 rounded-xl border-2 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option>Русский</option>
                    <option>English</option>
                    <option>Deutsch</option>
                </select>
            </div>
        </div>
        <button class="w-full mt-6 btn-primary text-white py-4 rounded-xl font-bold hover:opacity-90 transition" onclick="saveSettings()">
            💾 Сохранить настройки
        </button>
    </div>
</div>

<script>
    // State
    let fileUploaded = false;
    let analysisComplete = false;
    let currentTab = 'overview';
    let riskData = [];
    let clausesData = [];
    let recommendationsData = [];
    let complianceData = [];
    let highlightMode = false;
    let annotations = [];
    let uploadedFiles = [];

    // Sample data
    const sampleRisks = [
        { id: 1, level: 'high', category: 'Финансовые', title: 'Неопределённые штрафные санкции', description: 'Пункт 7.2 не содержит чётких критериев расчёта неустойки, что может привести к спорам.', clause: '7.2' },
        { id: 2, level: 'high', category: 'Юридические', title: 'Отсутствие юрисдикции', description: 'Не указано применимое право и подсудность для разрешения споров.', clause: '12.1' },
        { id: 3, level: 'medium', category: 'Операционные', title: 'Размытые сроки поставки', description: 'Сроки поставки определены как «разумные», что создаёт риски задержек.', clause: '4.3' },
        { id: 4, level: 'medium', category: 'Комплаенс', title: 'Неполная конфиденциальность', description: 'Отсутствуют положения о защите персональных данных согласно 152-ФЗ.', clause: '9.1' },
        { id: 5, level: 'low', category: 'Документальные', title: 'Отсутствие приложения', description: 'В договоре указано Приложение А, но оно не приложено.', clause: 'Приложение А' },
        { id: 6, level: 'low', category: 'Технические', title: 'Устаревшие реквизиты', description: 'Реквизиты одной из сторон могут быть неактуальны.', clause: '15.2' },
        { id: 7, level: 'high', category: 'Финансовые', title: 'Неясные условия оплаты', description: 'Не определены сроки и порядок оплаты в полном объёме.', clause: '5.1' },
        { id: 8, level: 'medium', category: 'Юридические', title: 'Ограничение ответственности', description: 'Одностороннее ограничение ответственности исполнителя.', clause: '8.3' }
    ];

    const sampleClauses = [
        { number: '1.1', title: 'Предмет договора', status: 'ok', note: 'Корректно определён', content: 'Поставщик обязуется передать покупателю товар в соответствии с условиями настоящего договора...' },
        { number: '2.3', title: 'Цена и порядок расчётов', status: 'warning', note: 'Требуется уточнение валюты', content: 'Цена товара определяется в рублях по курсу на дату оплаты...' },
        { number: '3.5', title: 'Сроки исполнения', status: 'warning', note: 'Не указаны конкретные даты', content: 'Поставка осуществляется в разумные сроки после получения предоплаты...' },
        { number: '4.2', title: 'Гарантийные обязательства', status: 'ok', note: 'Соответствует стандартам', content: 'Гарантийный срок составляет 12 месяцев с момента поставки...' },
        { number: '5.1', title: 'Ответственность сторон', status: 'error', note: 'Дисбаланс ответственности', content: 'Ответственность поставщика ограничена суммой договора...' },
        { number: '6.4', title: 'Форс-мажор', status: 'ok', note: 'Полный перечень обстоятельств', content: 'Стороны освобождаются от ответственности при наступлении форс-мажорных обстоятельств...' },
        { number: '7.2', title: 'Штрафные санкции', status: 'error', note: 'Неопределённые критерии', content: 'Неустойка выплачивается в случае нарушения сроков поставки...' },
        { number: '8.1', title: 'Конфиденциальность', status: 'warning', note: 'Требуется дополнение по 152-ФЗ', content: 'Стороны обязуются хранить конфиденциальность информации...' }
    ];

    const sampleRecommendations = [
        { priority: 'high', title: 'Добавить пункт о применимом праве', description: 'Рекомендуется явно указать, что договор регулируется законодательством РФ, и определить подсудность.', impact: 'Высокая', effort: 'Низкая' },
        { priority: 'high', title: 'Уточнить расчёт неустойки', description: 'В пункте 7.2 необходимо добавить формулу расчёта штрафных санкций с конкретными процентами.', impact: 'Высокая', effort: 'Низкая' },
        { priority: 'medium', title: 'Дополнить раздел о конфиденциальности', description: 'Добавить положения о защите персональных данных в соответствии с 152-ФЗ.', impact: 'Средняя', effort: 'Средняя' },
        { priority: 'medium', title: 'Конкретизировать сроки', description: 'Заменить формулировку «разумные сроки» на конкретные даты или периоды.', impact: 'Средняя', effort: 'Низкая' },
        { priority: 'low', title: 'Актуализировать реквизиты', description: 'Проверить и обновить банковские реквизиты всех сторон договора.', impact: 'Низкая', effort: 'Низкая' },
        { priority: 'low', title: 'Добавить отсутствующие приложения', description: 'Приложить все указанные в договоре приложения и спецификации.', impact: 'Низкая', effort: 'Низкая' }
    ];

    const sampleCompliance = [
        { id: 1, category: 'Гражданский кодекс РФ', items: [
                { text: 'Предмет договора определён', checked: true },
                { text: 'Стороны идентифицированы', checked: true },
                { text: 'Цена и порядок оплаты', checked: false },
                { text: 'Сроки исполнения', checked: false }
            ]},
        { id: 2, category: '152-ФЗ (Персональные данные)', items: [
                { text: 'Согласие на обработку ПД', checked: false },
                { text: 'Политика конфиденциальности', checked: false },
                { text: 'Защита данных', checked: true }
            ]},
        { id: 3, category: 'Налоговый кодекс', items: [
                { text: 'НДС указан корректно', checked: true },
                { text: 'Реквизиты полные', checked: false },
                { text: 'Документообразование', checked: true }
            ]},
        { id: 4, category: 'Трудовое законодательство', items: [
                { text: 'Нет признаков трудовых отношений', checked: true },
                { text: 'ГПХ договор корректен', checked: true }
            ]}
    ];

    const templates = [
        { name: 'Договор поставки', icon: '📦', category: 'Коммерческие' },
        { name: 'Договор услуг', icon: '🛠️', category: 'Коммерческие' },
        { name: 'Договор аренды', icon: '🏢', category: 'Недвижимость' },
        { name: 'NDA (Соглашение о конфиденциальности)', icon: '🔒', category: 'Защита' },
        { name: 'Трудовой договор', icon: '👤', category: 'Кадры' },
        { name: 'Договор подряда', icon: '🏗️', category: 'Коммерческие' },
        { name: 'Лицензионный договор', icon: '📜', category: 'IP' },
        { name: 'Договор займа', icon: '💰', category: 'Финансы' },
        { name: 'Агентский договор', icon: '🤝', category: 'Коммерческие' }
    ];

    const versions = [
        { version: 'v3.0', date: '2024-01-15 14:30', changes: 'Добавлен раздел о конфиденциальности', author: 'Иванов А.' },
        { version: 'v2.0', date: '2024-01-10 09:15', changes: 'Изменены условия оплаты', author: 'Петров Б.' },
        { version: 'v1.0', date: '2024-01-05 16:45', changes: 'Первоначальная версия', author: 'Сидоров В.' }
    ];

    // File upload handlers
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        for (let file of files) {
            uploadedFiles.push(file);
        }
        fileUploaded = true;
        renderUploadedFiles();
        document.getElementById('analyzeBtn').disabled = false;
        resetProgress();
    }

    function renderUploadedFiles() {
        const container = document.getElementById('uploadedFiles');
        container.classList.remove('hidden');
        container.innerHTML = uploadedFiles.map((file, index) => `
        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-xl border-2 border-green-200 dark:border-green-800 transition hover:shadow-md">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 dark:text-white text-sm">${file.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            </div>
            <button class="text-red-500 hover:text-red-700 w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30 transition flex items-center justify-center" onclick="removeFile(${index})">✕</button>
        </div>
    `).join('');
    }

    function removeFile(index) {
        uploadedFiles.splice(index, 1);
        if (uploadedFiles.length === 0) {
            fileUploaded = false;
            document.getElementById('uploadedFiles').classList.add('hidden');
            document.getElementById('analyzeBtn').disabled = true;
        } else {
            renderUploadedFiles();
        }
        fileInput.value = '';
    }

    function resetProgress() {
        for (let i = 1; i <= 4; i++) {
            document.getElementById(`progress${i}`).style.width = '0%';
            document.getElementById(`step${i}`).classList.remove('active', 'completed');
            const indicator = document.getElementById(`step${i}`).querySelector('div:first-child');
            indicator.className = 'w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 transition-all';
            indicator.querySelector('span').className = 'text-xs font-bold text-gray-500 dark:text-gray-400';
        }
    }

    // Analysis simulation
    async function startAnalysis() {
        if (!fileUploaded) return;

        document.getElementById('analyzeBtn').disabled = true;
        document.getElementById('analyzeBtn').textContent = '⏳ Анализ...';

        for (let step = 1; step <= 4; step++) {
            const stepEl = document.getElementById(`step${step}`);
            const progressEl = document.getElementById(`progress${step}`);
            const indicator = stepEl.querySelector('div:first-child');
            const number = stepEl.querySelector('span');

            stepEl.classList.add('active');
            indicator.className = 'w-8 h-8 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center flex-shrink-0 animate-pulse-slow';
            number.className = 'text-xs font-bold text-white';

            for (let p = 0; p <= 100; p += 5) {
                progressEl.style.width = p + '%';
                await sleep(80);
            }

            stepEl.classList.remove('active');
            stepEl.classList.add('completed');
            indicator.className = 'w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0 shadow-lg';
            number.innerHTML = '✓';
            number.className = 'text-xs font-bold text-white';
        }

        analysisComplete = true;
        document.getElementById('analyzeBtn').textContent = '✓ Анализ завершён';
        document.getElementById('analyzeBtn').classList.add('bg-green-500');

        // Update stats
        document.getElementById('totalClauses').textContent = sampleClauses.length;
        document.getElementById('totalRisks').textContent = sampleRisks.length;
        document.getElementById('complianceScore').textContent = '73%';
        document.getElementById('reviewTime').textContent = '2.5 мин';

        // Update gauges
        updateGauge('gauge1', 25, 'Низкий риск', 'gauge1-value');
        updateGauge('gauge2', 55, 'Средний риск', 'gauge2-value');
        updateGauge('gauge3', 80, 'Высокий риск', 'gauge3-value');

        riskData = sampleRisks;
        clausesData = sampleClauses;
        recommendationsData = sampleRecommendations;
        complianceData = sampleCompliance;

        renderRisks();
        renderClauses();
        renderRecommendations();
        renderCompliance();
        renderKeyFindings();
        renderHeatmap();
        initCharts();

        // Confetti celebration
        createConfetti();
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function updateGauge(id, percentage, label, valueId) {
        const circumference = 326.56;
        const offset = circumference - (percentage / 100) * circumference;
        document.getElementById(id).style.strokeDashoffset = offset;
        document.getElementById(`${id}-label`).textContent = label;
        if (valueId) {
            document.getElementById(valueId).textContent = percentage + '%';
        }
    }

    // Tab switching
    function switchTab(tab) {
        currentTab = tab;
        ['overview', 'risks', 'clauses', 'recommendations', 'compliance', 'ai-chat'].forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            const content = document.getElementById(`${t}-content`);
            if (t === tab) {
                btn.classList.add('tab-active');
                btn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                content.classList.remove('hidden');
            } else {
                btn.classList.remove('tab-active');
                btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                content.classList.add('hidden');
            }
        });
    }

    // Render functions
    function renderRisks(filter = 'all') {
        const container = document.getElementById('risksList');
        const filtered = filter === 'all' ? riskData : riskData.filter(r => r.level === filter);

        container.innerHTML = filtered.map(risk => `
        <div class="risk-item risk-${risk.level} bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 p-5 rounded-xl hover:shadow-lg cursor-pointer" onclick="viewRiskDetails(${risk.id})">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-3 flex-wrap gap-2">
                        <span class="badge badge-${risk.level}">${getRiskLevelText(risk.level)}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">${risk.category}</span>
                        <span class="text-xs bg-gray-200 dark:bg-gray-600 px-3 py-1 rounded-full font-medium">Пункт ${risk.clause}</span>
                    </div>
                    <h3 class="font-bold text-gray-800 dark:text-white mb-2">${risk.title}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">${risk.description}</p>
                </div>
                <button class="text-purple-500 hover:text-purple-700 ml-4 w-10 h-10 rounded-full hover:bg-purple-100 dark:hover:bg-purple-900/30 transition flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
    }

    function filterRisks(level) {
        renderRisks(level);
    }

    function filterRisksSearch() {
        const query = document.getElementById('riskFilter').value.toLowerCase();
        const filtered = riskData.filter(r =>
            r.title.toLowerCase().includes(query) ||
            r.description.toLowerCase().includes(query) ||
            r.category.toLowerCase().includes(query)
        );
        const container = document.getElementById('risksList');
        container.innerHTML = filtered.map(risk => `
        <div class="risk-item risk-${risk.level} bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 p-5 rounded-xl hover:shadow-lg cursor-pointer" onclick="viewRiskDetails(${risk.id})">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-3 flex-wrap gap-2">
                        <span class="badge badge-${risk.level}">${getRiskLevelText(risk.level)}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">${risk.category}</span>
                        <span class="text-xs bg-gray-200 dark:bg-gray-600 px-3 py-1 rounded-full font-medium">Пункт ${risk.clause}</span>
                    </div>
                    <h3 class="font-bold text-gray-800 dark:text-white mb-2">${risk.title}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">${risk.description}</p>
                </div>
            </div>
        </div>
    `).join('');
    }

    function getRiskLevelText(level) {
        return { low: 'Низкий', medium: 'Средний', high: 'Высокий' }[level];
    }

    function renderClauses() {
        const container = document.getElementById('clausesList');
        container.innerHTML = clausesData.map((clause, index) => `
        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition hover:border-purple-300 dark:hover:border-purple-700" id="clause-${index}">
            <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 cursor-pointer" onclick="toggleClause(${index})">
                <div class="flex items-center space-x-4">
                    <div class="clause-number w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-lg">
                        ${clause.number}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white">${clause.title}</h3>
                        <p class="text-xs ${clause.status === 'ok' ? 'text-green-600' : clause.status === 'warning' ? 'text-orange-600' : 'text-red-600'} font-medium mt-1">
                            ${clause.note}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full ${clause.status === 'ok' ? 'bg-green-500' : clause.status === 'warning' ? 'bg-orange-500' : 'bg-red-500'} shadow"></span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform" id="arrow-${index}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
            <div class="p-5 hidden bg-white dark:bg-gray-750" id="clause-content-${index}">
                <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">${clause.content}</p>
                <div class="flex items-center space-x-2 mt-4">
                    <button class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-4 py-2 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/50 transition font-medium" onclick="addAnnotationToClause(${index})">
                        📝 Добавить заметку
                    </button>
                    <button class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-2 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition font-medium" onclick="highlightClause(${index})">
                        🖍️ Выделить
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    }

    function toggleClause(index) {
        const content = document.getElementById(`clause-content-${index}`);
        const arrow = document.getElementById(`arrow-${index}`);
        content.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    function expandAllClauses() {
        clausesData.forEach((_, index) => {
            document.getElementById(`clause-content-${index}`).classList.remove('hidden');
            document.getElementById(`arrow-${index}`).classList.add('rotate-180');
        });
    }

    function collapseAllClauses() {
        clausesData.forEach((_, index) => {
            document.getElementById(`clause-content-${index}`).classList.add('hidden');
            document.getElementById(`arrow-${index}`).classList.remove('rotate-180');
        });
    }

    function renderRecommendations() {
        const container = document.getElementById('recommendationsList');
        container.innerHTML = recommendationsData.map((rec, index) => `
        <div class="flex items-start space-x-4 p-5 bg-gradient-to-r from-${rec.priority === 'high' ? 'red' : rec.priority === 'medium' ? 'orange' : 'blue'}-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl border-l-4 border-${rec.priority === 'high' ? 'red' : rec.priority === 'medium' ? 'orange' : 'blue'}-500 shadow-sm hover:shadow-md transition">
            <div class="w-10 h-10 rounded-full bg-${rec.priority === 'high' ? 'red' : rec.priority === 'medium' ? 'orange' : 'blue'}-500 flex items-center justify-center text-white font-bold flex-shrink-0 shadow-lg">
                ${index + 1}
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2 flex-wrap gap-2">
                    <h3 class="font-bold text-gray-800 dark:text-white">${rec.title}</h3>
                    <span class="badge badge-${rec.priority === 'high' ? 'high' : rec.priority === 'medium' ? 'medium' : 'low'}">
                        ${rec.priority === 'high' ? 'Высокий' : rec.priority === 'medium' ? 'Средний' : 'Низкий'} приоритет
                    </span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 leading-relaxed">${rec.description}</p>
                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 font-medium">
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">Влияние: ${rec.impact}</span>
                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">Усилия: ${rec.effort}</span>
                </div>
            </div>
            <button class="text-purple-500 hover:text-purple-700 w-10 h-10 rounded-full hover:bg-purple-100 dark:hover:bg-purple-900/30 transition flex items-center justify-center" onclick="applyRecommendation(${index})">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>
    `).join('');
    }

    function renderCompliance() {
        const container = document.getElementById('complianceList');
        let totalChecked = 0;
        let totalItems = 0;

        container.innerHTML = complianceData.map(category => `
        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-purple-300 dark:hover:border-purple-700 transition">
            <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                ${category.category}
            </h3>
            <div class="space-y-3">
                ${category.items.map(item => {
            totalItems++;
            if (item.checked) totalChecked++;
            return `
                        <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <input type="checkbox" ${item.checked ? 'checked' : ''} class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500 border-2 border-gray-300" onchange="updateComplianceScore()">
                            <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">${item.text}</span>
                        </label>
                    `;
        }).join('')}
            </div>
        </div>
    `).join('');

        const score = Math.round((totalChecked / totalItems) * 100);
        document.getElementById('complianceTotal').textContent = score + '%';
    }

    function updateComplianceScore() {
        const checkboxes = document.querySelectorAll('#complianceList input[type="checkbox"]');
        const checked = Array.from(checkboxes).filter(cb => cb.checked).length;
        const score = Math.round((checked / checkboxes.length) * 100);
        document.getElementById('complianceTotal').textContent = score + '%';
    }

    function renderKeyFindings() {
        const container = document.getElementById('keyFindings');
        const findings = [
            { icon: '⚠️', text: 'Обнаружено 2 критических риска, требующих немедленного внимания', color: 'red' },
            { icon: '📋', text: '4 пункта договора требуют уточнения формулировок', color: 'orange' },
            { icon: '✅', text: '85% пунктов соответствуют стандартным практикам', color: 'green' }
        ];

        container.innerHTML = findings.map(finding => `
        <div class="flex items-center space-x-4 p-5 bg-${finding.color}-50 dark:bg-${finding.color}-900/20 rounded-xl border-2 border-${finding.color}-200 dark:border-${finding.color}-800 hover:shadow-md transition">
            <span class="text-3xl">${finding.icon}</span>
            <p class="text-gray-700 dark:text-gray-300 font-medium">${finding.text}</p>
        </div>
    `).join('');
    }

    function renderHeatmap() {
        const container = document.getElementById('riskHeatmap');
        const cells = [];
        for (let i = 0; i < 25; i++) {
            const riskLevel = Math.random();
            let colorClass = 'bg-green-400';
            if (riskLevel > 0.75) colorClass = 'bg-red-400';
            else if (riskLevel > 0.5) colorClass = 'bg-orange-400';
            else if (riskLevel > 0.25) colorClass = 'bg-yellow-400';

            cells.push(`
            <div class="heatmap-cell w-full aspect-square rounded-lg ${colorClass} hover:opacity-80 shadow-sm"
                data-risk="${Math.round(riskLevel * 100)}%"
                onclick="showHeatmapDetail(this)"></div>
        `);
        }
        container.innerHTML = cells.join('');
    }

    function showHeatmapDetail(cell) {
        const risk = cell.dataset.risk;
        alert(`Уровень риска: ${risk}`);
    }

    // Charts
    let riskChart = null;
    let categoryChart = null;

    function initCharts() {
        // Risk Distribution Chart
        const ctx1 = document.getElementById('riskChart').getContext('2d');
        if (riskChart) riskChart.destroy();

        const riskCounts = {
            'Финансовые': riskData.filter(r => r.category === 'Финансовые').length,
            'Юридические': riskData.filter(r => r.category === 'Юридические').length,
            'Операционные': riskData.filter(r => r.category === 'Операционные').length,
            'Комплаенс': riskData.filter(r => r.category === 'Комплаенс').length,
            'Документальные': riskData.filter(r => r.category === 'Документальные').length,
            'Технические': riskData.filter(r => r.category === 'Технические').length
        };

        riskChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: Object.keys(riskCounts),
                datasets: [{
                    label: 'Количество рисков',
                    data: Object.values(riskCounts),
                    backgroundColor: [
                        'rgba(245, 101, 101, 0.8)',
                        'rgba(237, 137, 54, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(72, 187, 120, 0.8)'
                    ],
                    borderColor: [
                        'rgba(245, 101, 101, 1)',
                        'rgba(237, 137, 54, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(72, 187, 120, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Category Chart (Pie)
        const ctx2 = document.getElementById('categoryChart').getContext('2d');
        if (categoryChart) categoryChart.destroy();

        const levelCounts = {
            'Высокий': riskData.filter(r => r.level === 'high').length,
            'Средний': riskData.filter(r => r.level === 'medium').length,
            'Низкий': riskData.filter(r => r.level === 'low').length
        };

        categoryChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: Object.keys(levelCounts),
                datasets: [{
                    data: Object.values(levelCounts),
                    backgroundColor: [
                        'rgba(245, 101, 101, 0.9)',
                        'rgba(245, 158, 11, 0.9)',
                        'rgba(72, 187, 120, 0.9)'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Chat functionality
    function handleChatKeyPress(event) {
        if (event.key === 'Enter') {
            sendChatMessage();
        }
    }

    function sendChatMessage() {
        const input = document.getElementById('chatInput');
        const message = input.value.trim();
        if (!message) return;

        const container = document.getElementById('chatContainer');

        // User message
        container.innerHTML += `
        <div class="chat-message flex items-start space-x-3 mb-4 flex-row-reverse">
            <div class="w-11 h-11 rounded-full bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                👤
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-2xl p-4 flex-1 shadow-md">
                <p class="text-white font-medium">${message}</p>
                <p class="text-xs text-white/70 mt-2 text-right">Только что</p>
            </div>
        </div>
    `;

        input.value = '';
        container.scrollTop = container.scrollHeight;

        // Show typing indicator
        const typingId = 'typing-' + Date.now();
        container.innerHTML += `
        <div class="chat-message flex items-start space-x-3 mb-4" id="${typingId}">
            <div class="w-11 h-11 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg">
                🤖
            </div>
            <div class="bg-white dark:bg-gray-700 rounded-2xl p-4 flex-1 shadow-md border border-gray-100 dark:border-gray-600">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    `;
        container.scrollTop = container.scrollHeight;

        // AI response simulation
        setTimeout(() => {
            document.getElementById(typingId).remove();

            const responses = [
                'На основе анализа договора, я рекомендую обратить особое внимание на пункты 7.2 и 12.1. Они содержат неопределённые формулировки, которые могут привести к спорам.',
                'Данный договор соответствует 73% требований законодательства. Основные пробелы касаются защиты персональных данных и определения подсудности.',
                'Для снижения рисков рекомендую: 1) Добавить пункт о применимом праве, 2) Уточнить расчёт неустойки, 3) Дополнить раздел о конфиденциальности.',
                'Критические риски обнаружены в финансовых и юридических разделах. Требуется немедленное внимание к пунктам о штрафных санкциях и юрисдикции.'
            ];

            const response = responses[Math.floor(Math.random() * responses.length)];

            container.innerHTML += `
            <div class="chat-message flex items-start space-x-3 mb-4">
                <div class="w-11 h-11 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg">
                    🤖
                </div>
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-4 flex-1 shadow-md border border-gray-100 dark:border-gray-600">
                    <p class="text-gray-800 dark:text-gray-200 font-medium">${response}</p>
                    <p class="text-xs text-gray-400 mt-2">Только что</p>
                </div>
            </div>
        `;
            container.scrollTop = container.scrollHeight;
        }, 1500);
    }

    function quickQuestion(question) {
        document.getElementById('chatInput').value = question;
        sendChatMessage();
    }

    // Modal functions
    function openVersionHistory() {
        const container = document.getElementById('versionTimeline');
        container.innerHTML = versions.map((v, index) => `
        <div class="relative pl-6">
            <div class="absolute left-0 w-5 h-5 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 border-4 border-white dark:border-gray-800 shadow-lg"></div>
            <div class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 rounded-xl p-5 border-2 border-gray-200 dark:border-gray-700 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-800 dark:text-white text-lg">${v.version}</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full">${v.date}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">${v.changes}</p>
                <p class="text-xs text-gray-400 font-medium">Автор: ${v.author}</p>
            </div>
        </div>
    `).join('');
        document.getElementById('versionModal').classList.remove('hidden');
    }

    function openComparison() {
        document.getElementById('comparisonModal').classList.remove('hidden');
    }

    function openTemplates() {
        const container = document.getElementById('templatesGrid');
        container.innerHTML = templates.map(t => `
        <div class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-5 hover:border-purple-500 hover:shadow-lg cursor-pointer transition group">
            <div class="text-4xl mb-3 group-hover:scale-110 transition-transform">${t.icon}</div>
            <h3 class="font-bold text-gray-800 dark:text-white">${t.name}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">${t.category}</p>
        </div>
    `).join('');
        document.getElementById('templatesModal').classList.remove('hidden');
    }

    function toggleSettings() {
        document.getElementById('settingsModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function compareDocuments() {
        document.getElementById('comparisonResult').classList.remove('hidden');
    }

    function saveSettings() {
        alert('Настройки сохранены!');
        closeModal('settingsModal');
    }

    // Utility functions
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
        const isDark = document.body.classList.contains('dark');
        document.getElementById('darkModeToggle').checked = isDark;
        document.getElementById('settingsDarkMode').checked = isDark;
        localStorage.setItem('darkMode', isDark);
    }

    function toggleHighlightMode() {
        highlightMode = !highlightMode;
        document.getElementById('highlightBtn').classList.toggle('bg-purple-500', highlightMode);
        document.getElementById('highlightBtn').classList.toggle('text-white', highlightMode);
        alert(highlightMode ? 'Режим выделения включён. Выберите текст для выделения.' : 'Режим выделения выключен.');
    }

    function addAnnotation() {
        alert('Режим заметок активирован. Кликните на пункт договора, чтобы добавить заметку.');
    }

    function addAnnotationToClause(index) {
        const note = prompt('Введите заметку:');
        if (note) {
            annotations.push({ clause: index, note, date: new Date().toLocaleString() });
            alert('Заметка добавлена!');
        }
    }

    function highlightClause(index) {
        const clause = document.getElementById(`clause-${index}`);
        clause.classList.add('highlight');
        alert('Пункт выделен!');
    }

    function searchDocument() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        clausesData.forEach((clause, index) => {
            const el = document.getElementById(`clause-${index}`);
            if (clause.title.toLowerCase().includes(query) || clause.content.toLowerCase().includes(query)) {
                el.classList.remove('hidden');
                el.classList.add('highlight');
            } else {
                el.classList.add('hidden');
            }
        });
    }

    function applyRecommendation(index) {
        alert(`Рекомендация #${index + 1} добавлена в список изменений!`);
    }

    function viewRiskDetails(id) {
        const risk = riskData.find(r => r.id === id);
        alert(`Риск: ${risk.title}\nОписание: ${risk.description}\nПункт: ${risk.clause}`);
    }

    function generateComplianceReport() {
        alert('Отчёт о соответствии формируется и будет отправлен на вашу почту.');
    }

    function exportReport(format) {
        alert(`Отчёт экспортируется в формате ${format.toUpperCase()}...`);
    }

    function startNewAnalysis() {
        uploadedFiles = [];
        fileUploaded = false;
        analysisComplete = false;
        document.getElementById('uploadedFiles').classList.add('hidden');
        document.getElementById('analyzeBtn').disabled = true;
        document.getElementById('analyzeBtn').textContent = '🚀 Начать анализ';
        document.getElementById('analyzeBtn').classList.remove('bg-green-500');
        resetProgress();
        document.getElementById('searchInput').value = '';
    }

    function openAIChat() {
        switchTab('ai-chat');
    }

    function openChecklist() {
        switchTab('compliance');
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Confetti effect
    function createConfetti() {
        const colors = ['#667eea', '#764ba2', '#48bb78', '#ed8936', '#f56565'];
        for (let i = 0; i < 50; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 4000);
            }, i * 50);
        }
    }

    // Scroll listener for back to top button
    window.addEventListener('scroll', () => {
        const btn = document.getElementById('scrollTopBtn');
        btn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });

    // Initialize dark mode from localStorage
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark');
        document.getElementById('darkModeToggle').checked = true;
        document.getElementById('settingsDarkMode').checked = true;
    }

    // Initialize heatmap
    renderHeatmap();
</script>
</body>
</html>

