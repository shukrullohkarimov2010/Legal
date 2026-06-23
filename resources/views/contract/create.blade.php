<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.dashboard_title') }} — LegalAI Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/docx@8.2.2/build/index.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Alpine.js v3 - ОБЯЗАТЕЛЬНО в <head> -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1)',
                        'slide-down': 'slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
                        'scale-in': 'scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
                        'toast-in': 'toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                        'toast-out': 'toastOut 0.3s ease-in',
                        'shimmer': 'shimmer 2s linear infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        slideDown: { '0%': { opacity: '0', transform: 'translateY(-10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        scaleIn: { '0%': { opacity: '0', transform: 'scale(0.95)' }, '100%': { opacity: '1', transform: 'scale(1)' } },
                        toastIn: { '0%': { opacity: '0', transform: 'translateX(100%)' }, '100%': { opacity: '1', transform: 'translateX(0)' } },
                        toastOut: { '0%': { opacity: '1', transform: 'translateX(0)' }, '100%': { opacity: '0', transform: 'translateX(100%)' } },
                        shimmer: { '0%': { backgroundPosition: '-1000px 0' }, '100%': { backgroundPosition: '1000px 0' } },
                    }
                }
            }
        }
    </script>
    <style>
        * { -webkit-tap-highlight-color: transparent; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            transition: background-color 0.4s ease;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        [data-theme="light"] body {
            background-color: #fafaf9;
            background-image:
                radial-gradient(at 12% 8%, rgba(99, 102, 241, 0.12) 0px, transparent 50%),
                radial-gradient(at 88% 4%, rgba(168, 85, 247, 0.10) 0px, transparent 50%),
                radial-gradient(at 78% 88%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
                radial-gradient(at 8% 92%, rgba(14, 165, 233, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(251, 191, 36, 0.04) 0px, transparent 60%);
        }

        [data-theme="dark"] body {
            background-color: #0a0a0b;
            background-image:
                radial-gradient(at 12% 8%, rgba(99, 102, 241, 0.20) 0px, transparent 50%),
                radial-gradient(at 88% 4%, rgba(168, 85, 247, 0.16) 0px, transparent 50%),
                radial-gradient(at 78% 88%, rgba(236, 72, 153, 0.12) 0px, transparent 50%),
                radial-gradient(at 8% 92%, rgba(14, 165, 233, 0.14) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(251, 191, 36, 0.06) 0px, transparent 60%);
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.03;
            z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }

        [data-theme="dark"] body::before { opacity: 0.05; }

        main, nav { position: relative; z-index: 1; }

        .glass {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 4px 16px rgba(0, 0, 0, 0.04),
                0 12px 40px rgba(0, 0, 0, 0.03);
        }

        .dark .glass {
            background: rgba(24, 24, 27, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.3),
                0 4px 16px rgba(0, 0, 0, 0.2),
                0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .glass-strong {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .dark .glass-strong {
            background: rgba(24, 24, 27, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-premium {
            position: relative;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-premium input,
        .input-premium textarea {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: rgba(255, 255, 255, 0.6);
            border: 1.5px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            font-size: 0.9375rem;
            color: inherit;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .input-premium input,
        .dark .input-premium textarea {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .input-premium input:hover,
        .input-premium textarea:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }

        .input-premium input:focus,
        .input-premium textarea:focus {
            outline: none;
            border-color: rgb(99, 102, 241);
            background: rgba(255, 255, 255, 0.95);
            box-shadow:
                0 0 0 4px rgba(99, 102, 241, 0.12),
                0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .dark .input-premium input:focus,
        .dark .input-premium textarea:focus {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
        }

        .input-premium .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0, 0, 0, 0.4);
            pointer-events: none;
            transition: color 0.2s;
        }

        .dark .input-premium .input-icon { color: rgba(255, 255, 255, 0.4); }

        .input-premium:focus-within .input-icon {
            color: rgb(99, 102, 241);
        }

        .input-premium textarea ~ .input-icon {
            top: 1.125rem;
            transform: none;
        }

        .hero-premium {
            background:
                linear-gradient(135deg, #2417e1 0%, #621ddd 35%, #cc257c 70%);
            position: relative;
            overflow: hidden;
        }

        .hero-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.35'/%3E%3C/svg%3E");
            mix-blend-mode: overlay;
            pointer-events: none;
        }

        .hero-premium::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%);
            border-radius: 50%;
            pointer-events: none;
        }

        .template-card-premium {
            position: relative;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .template-card-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(99, 102, 241, 0.05) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .template-card-premium:hover {
            transform: translateY(-4px);
        }

        .template-card-premium:hover::before { opacity: 1; }

        .template-card-premium.active {
            border-color: rgb(99, 102, 241);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.25);
        }

        .template-card-premium.active::after {
            content: '';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 24px;
            height: 24px;
            background: rgb(99, 102, 241);
            border-radius: 50%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white' stroke='white' stroke-width='3'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
            background-size: 14px;
            background-repeat: no-repeat;
            background-position: center;
            animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .section-card {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .section-card:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .section-card.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.05) 100%);
            border-color: rgb(99, 102, 241);
        }

        .dark .section-card.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(168, 85, 247, 0.1) 100%);
        }

        .section-card input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            position: relative;
        }

        .dark .section-card input[type="checkbox"] {
            border-color: rgba(255, 255, 255, 0.25);
        }

        .section-card input[type="checkbox"]:checked {
            background: rgb(99, 102, 241);
            border-color: rgb(99, 102, 241);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
            background-size: 14px;
            background-repeat: no-repeat;
            background-position: center;
            animation: checkmark 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes checkmark {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .contract-preview {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 0.8125rem;
            line-height: 1.75;
            white-space: pre-wrap;
            word-break: break-word;
            color: rgb(39, 39, 42);
            background:
                linear-gradient(180deg, #fefefe 0%, #fafaf9 100%);
        }

        .dark .contract-preview {
            color: rgb(228, 228, 231);
            background: linear-gradient(180deg, #18181b 0%, #0f0f11 100%);
        }

        .stepper-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            transition: all 0.3s;
        }

        .stepper-line {
            height: 2px;
            flex: 1;
            background: rgba(0, 0, 0, 0.1);
            transition: background 0.3s;
        }

        .dark .stepper-line { background: rgba(255, 255, 255, 0.1); }

        .stepper-line.active {
            background: linear-gradient(90deg, rgb(99, 102, 241), rgb(168, 85, 247));
        }

        .theme-switch {
            position: relative;
            width: 60px;
            height: 32px;
            border-radius: 16px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            cursor: pointer;
            transition: background 0.3s;
            overflow: hidden;
        }

        .dark .theme-switch {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .theme-switch-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .theme-switch-thumb {
            transform: translateX(28px);
            background: #1e293b;
        }

        .toast-container {
            position: fixed;
            top: 5rem;
            right: 1.5rem;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            pointer-events: none;
        }

        .toast {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
            pointer-events: auto;
            animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dark .toast {
            background: rgba(24, 24, 27, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .toast.hiding { animation: toastOut 0.3s ease-in forwards; }

        .toast-success { border-left: 4px solid rgb(34, 197, 94); }
        .toast-error { border-left: 4px solid rgb(239, 68, 68); }
        .toast-info { border-left: 4px solid rgb(59, 130, 246); }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); background-clip: padding-box; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0, 0, 0, 0.3); background-clip: padding-box; }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        .input-premium label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(0, 0, 0, 0.7);
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .dark .input-premium label { color: rgba(255, 255, 255, 0.8); }

        .preview-toolbar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 10px;
        }

        .dark .preview-toolbar { background: rgba(255, 255, 255, 0.05); }

        .toolbar-btn {
            padding: 0.375rem 0.625rem;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .toolbar-btn:hover { background: rgba(0, 0, 0, 0.06); }
        .dark .toolbar-btn:hover { background: rgba(255, 255, 255, 0.08); }

        .chip-premium {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: white;
            transition: all 0.2s;
        }

        .chip-premium:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .metric-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 18px;
            padding: 1.125rem;
            transition: all 0.3s;
        }

        .metric-card:hover {
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-2px);
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 2px solid rgb(99, 102, 241);
            outline-offset: 2px;
        }

        .shimmer {
            background: linear-gradient(90deg,
            rgba(0, 0, 0, 0.04) 0%,
            rgba(0, 0, 0, 0.08) 50%,
            rgba(0, 0, 0, 0.04) 100%);
            background-size: 1000px 100%;
            animation: shimmer 2s linear infinite;
        }

        .dropdown-premium {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 200px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideDown 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 60;
        }

        .dark .dropdown-premium {
            background: rgba(24, 24, 27, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .insight-success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.08) 0%, rgba(34, 197, 94, 0.02) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .insight-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.08) 0%, rgba(251, 146, 60, 0.02) 100%);
            border: 1px solid rgba(251, 146, 60, 0.2);
        }

        @media (max-width: 1279px) {
            .preview-sticky { position: static !important; }
        }
    </style>
</head>
<body class="min-h-screen text-neutral-900 dark:text-neutral-100">
@php
    $currentUser = auth()->user();
    $userName = $currentUser?->name ?: 'Пользователь';
    $userEmail = $currentUser?->email ?: 'email@example.com';
    $nameParts = preg_split('/\s+/u', trim($userName), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $userInitials = collect($nameParts)->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $userInitials = $userInitials !== '' ? $userInitials : 'П';

    $builderTranslations = [
        'ui' => [
            'searchCommands' => 'Поиск команд',
            'theme' => 'Тема',
            'download' => 'Скачать',
            'copy' => 'Копировать',
            'livePreview' => 'Предпросмотр',
            'draftContract' => 'Проект договора',
            'draftContractText' => 'Текст договора',
            'buildStatus' => 'Статус',
            'draftReady' => 'Готов',
            'buildStatusText' => 'Документ готов к скачиванию',
            'reminder' => 'Напоминание',
            'reviewRequired' => 'Требуется проверка',
            'reminderText' => 'Перед подписанием рекомендуется проверить данные и реквизиты сторон.',
            'buildPlan' => 'Структура договора',
            'txtDocument' => 'TXT документ',
            'pdfDocument' => 'PDF документ',
            'wordDocument' => 'Word документ',
            'copied' => 'Скопировано',
            'txtDownloaded' => 'TXT документ успешно скачан',
            'pdfDownloaded' => 'PDF документ успешно скачан',
            'pdfError' => 'Ошибка при создании PDF',
            'docxDownloaded' => 'DOCX документ успешно скачан',
            'docxError' => 'Ошибка при создании DOCX',
            'generating' => 'Генерация договора...',
            'commandPaletteInDevelopment' => 'Поиск команд находится в разработке',
            'rubCurrencySymbol' => '₽',
            'notSpecified' => 'Не указано',
            'withoutNumber' => 'Без номера',
            'party1' => 'Сторона 1',
            'party2' => 'Сторона 2',
            'applicableLaw' => 'действующим законодательством',
            'defaultPenaltyRule' => 'За просрочку исполнения обязательств виновная Сторона уплачивает пеню в размере 0.1% от суммы задолженности за каждый день просрочки.',
            'defaultSpecialTerms' => 'Дополнительные условия определяются по соглашению Сторон.',
            'notSelected' => 'Не выбрано',
        ],
        'templates' => [
            'service' => [
                'label' => 'Договор возмездного оказания услуг',
                'description' => 'Для оказания услуг и выполнения работ',
                'badge' => 'Основной',
                'roleA' => 'Заказчик',
                'roleB' => 'Исполнитель',
                'defaultSubject' => 'оказание услуг по разработке программного обеспечения',
            ],
            'supply' => [
                'label' => 'Договор поставки',
                'description' => 'Для поставки товаров и продукции',
                'badge' => 'Бизнес',
                'roleA' => 'Покупатель',
                'roleB' => 'Поставщик',
                'defaultSubject' => 'поставка товаров',
            ],
            'nda' => [
                'label' => 'Соглашение о конфиденциальности (NDA)',
                'description' => 'Для защиты конфиденциальной информации',
                'badge' => 'Конфиденциально',
                'roleA' => 'Раскрывающая сторона',
                'roleB' => 'Получающая сторона',
                'defaultSubject' => 'передача и защита конфиденциальной информации',
            ],
        ],
        'sections' => [
            'definitions' => [
                'label' => 'Определения и термины',
                'description' => 'Расшифровка ключевых понятий договора',
            ],
            'scope' => [
                'label' => 'Предмет договора',
                'description' => 'Описание предмета и обязательств сторон',
            ],
            'payment' => [
                'label' => 'Стоимость и порядок оплаты',
                'description' => 'Финансовые условия договора',
            ],
            'acceptance' => [
                'label' => 'Порядок приёмки-передачи',
                'description' => 'Процедура приёмки результатов работ',
            ],
            'liability' => [
                'label' => 'Ответственность сторон',
                'description' => 'Ответственность за нарушение обязательств',
            ],
            'ip_rights' => [
                'label' => 'Права на интеллектуальную собственность',
                'description' => 'Передача прав на результат работ',
            ],
            'termination' => [
                'label' => 'Срок действия договора',
                'description' => 'Срок действия и порядок расторжения',
            ],
            'confidentiality' => [
                'label' => 'Конфиденциальность',
                'description' => 'Защита информации и данных',
            ],
            'disputes' => [
                'label' => 'Разрешение споров',
                'description' => 'Порядок урегулирования разногласий',
            ],
            'force_majeure' => [
                'label' => 'Форс-мажор',
                'description' => 'Обстоятельства непреодолимой силы',
            ],
            'notices' => [
                'label' => 'Уведомления и связь',
                'description' => 'Порядок обмена уведомлениями',
            ],
            'entire_agreement' => [
                'label' => 'Заключительные положения',
                'description' => 'Финальные условия договора',
            ],
        ],
        'draft' => [
            'header' => [
                'ru' => ':customer, именуемое в дальнейшем «:roleA», в лице уполномоченного представителя, действующего на основании учредительных документов, с одной стороны, и :contractor, именуемое в дальнейшем «:roleB», в лице уполномоченного представителя, действующего на основании учредительных документов, с другой стороны, совместно именуемые «Стороны», заключили настоящий договор о нижеследующем:',
                'en' => ':customer, hereinafter referred to as ":roleA", represented by an authorized representative acting on the basis of constituent documents, of the one part, and :contractor, hereinafter referred to as ":roleB", represented by an authorized representative acting on the basis of constituent documents, of the other part, jointly referred to as the "Parties", have concluded this Agreement as follows:',
                'kk' => ':customer, бұдан былай «:roleA» деп аталатын, құрылтай құжаттары негізінде әрекет ететін уәкілетті өкілі арқылы, бір тараптан, және :contractor, бұдан былай «:roleB» деп аталатын, құрылтай құжаттары негізінде әрекет ететін уәкілетті өкілі арқылы, екінші тараптан, бірлесіп «Тараптар» деп аталатын, осы шартты төмендегілер туралы жасасты:',
            ],
            'definitions' => [
                'ru' => "1. ОПРЕДЕЛЕНИЯ И ТЕРМИНЫ

1.1. В настоящем договоре следующие термины имеют следующие значения:
— «Работы/Услуги» — действия Исполнителя, направленные на достижение результата, указанного в п. 2.1 настоящего договора;
— «Результат» — материальный или нематериальный итог выполнения Работ, передаваемый Заказчику;
— «Стороны» — Заказчик и Исполнитель, совместно именуемые;
— «Рабочий день» — день, не являющийся выходным или праздничным согласно законодательству места заключения договора.

1.2. Иные термины, используемые в договоре, толкуются в соответствии с действующим законодательством.",
                'en' => "1. DEFINITIONS AND TERMS

1.1. In this Agreement, the following terms have the following meanings:
— \"Works/Services\" — actions of the Contractor aimed at achieving the result specified in clause 2.1 of this Agreement;
— \"Result\" — material or intangible outcome of the Work performed, transferred to the Customer;
— \"Parties\" — Customer and Contractor, jointly referred to as;
— \"Business Day\" — a day that is not a weekend or holiday according to the legislation of the place of conclusion of the Agreement.

1.2. Other terms used in the Agreement shall be interpreted in accordance with applicable law.",
                'kk' => "1. АНЫҚТАМАЛАР ЖӘНЕ ТЕРМИНДЕР

1.1. Осы шартта келесі терминдер келесі мағыналарға ие:
— «Жұмыстар/Қызметтер» — осы шарттың 2.1-тармағында көрсетілген нәтижеге қол жеткізуге бағытталған Мердігердің әрекеттері;
— «Нәтиже» — Тапсырыс берушіге берілетін орындалған Жұмыстардың материалдық немесе материалдық емес қорытындысы;
— «Тараптар» — Тапсырыс беруші және Мердігер, бірлесіп аталатын;
— «Жұмыс күні» — шарт жасалған жердің заңнамасына сәйкес демалыс немесе мереке күні емес күн.

1.2. Шартта қолданылатын басқа терминдер қолданыстағы заңнамаға сәйкес түсіндіріледі.",
            ],
            'scope' => [
                'ru' => "2. ПРЕДМЕТ ДОГОВОРА

2.1. :roleB обязуется по заданию :roleA выполнить :subject, а :roleA обязуется принять результат выполненных работ и оплатить его в порядке и на условиях, предусмотренных настоящим договором.

2.2. Объём, содержание, сроки выполнения работ и иные условия могут определяться дополнительными соглашениями или техническими заданиями, являющимися неотъемлемой частью настоящего договора.

2.3. :roleB гарантирует надлежащее качество выполняемых работ и соответствие результата требованиям :roleA, указанным в техническом задании.

2.4. :roleB обязуется выполнять работы лично, если иное не согласовано Сторонами в письменной форме.",
                'en' => "2. SUBJECT OF THE AGREEMENT

2.1. :roleB undertakes to perform :subject as assigned by :roleA, and :roleA undertakes to accept the result of the work and pay for it in the manner and under the conditions provided for by this Agreement.

2.2. The scope, content, timing of work and other conditions may be determined by additional agreements or technical specifications that are an integral part of this Agreement.

2.3. :roleB guarantees the proper quality of the work performed and compliance of the result with the requirements of :roleA specified in the technical specification.

2.4. :roleB undertakes to perform the work personally, unless otherwise agreed by the Parties in writing.",
                'kk' => "2. ШАРТТЫҢ МӘНІ

2.1. :roleB :roleA тапсырмасы бойынша :subject орындауды міндеттейді, ал :roleA орындалған жұмыс нәтижесін қабылдауды және осы шартта көзделген тәртіппен және шарттармен төлеуді міндеттейді.

2.2. Жұмыстардың көлемі, мазмұны, орындалу мерзімдері және басқа шарттар қосымша келісімдермен немесе техникалық тапсырмалармен анықталуы мүмкін, олар осы шарттың ажырамас бөлігі болып табылады.

2.3. :roleB орындалатын жұмыстардың тиісті сапасын және нәтиженің техникалық тапсырмада көрсетілген :roleA талаптарына сәйкестігін кепілдейді.

2.4. :roleB жұмыстарды жеке орындауды міндеттейді, егер Тараптар жазбаша түрде басқаша келіспесе.",
            ],
            'payment' => [
                'ru' => "3. СТОИМОСТЬ И ПОРЯДОК ОПЛАТЫ

3.1. Стоимость работ по настоящему договору составляет :amount (сумма прописью) с учётом всех налогов и сборов.

3.2. Оплата производится в безналичной форме путём перечисления денежных средств на расчётный счёт :roleB.

3.3. :roleA обязуется произвести оплату в следующем порядке:
— авансовый платёж в размере 50% от стоимости работ — в течение 5 (пяти) рабочих дней с даты подписания настоящего договора;
— окончательный расчёт в размере 50% — в течение :days рабочих дней с момента подписания акта выполненных работ.

3.4. Обязательства по оплате считаются исполненными с момента поступления денежных средств на расчётный счёт :roleB.

3.5. В случае задержки оплаты :roleA уплачивает :roleB пеню в размере, указанном в разделе 5 настоящего договора.",
                'en' => "3. COST AND PAYMENT PROCEDURE

3.1. The cost of work under this Agreement is :amount (amount in words) including all taxes and fees.

3.2. Payment is made in non-cash form by transferring funds to the bank account of :roleB.

3.3. :roleA undertakes to make payment in the following order:
— advance payment in the amount of 50% of the cost of work — within 5 (five) business days from the date of signing this Agreement;
— final settlement in the amount of 50% — within :days business days from the date of signing the act of completed work.

3.4. Payment obligations are considered fulfilled from the moment funds are received to the bank account of :roleB.

3.5. In case of payment delay, :roleA pays :roleB a penalty in the amount specified in Section 5 of this Agreement.",
                'kk' => "3. ҚҰНЫ ЖӘНЕ ТӨЛЕМ ТӘРТІБІ

3.1. Осы шарт бойынша жұмыстардың құны :amount (сомасы жазбаша) барлық салықтар мен алымдарды ескере отырып құрайды.

3.2. Төлем :roleB есеп шотына ақша аудару арқылы қолма-қол ақшасыз нысанда жүзеге асырылады.

3.3. :roleA келесі тәртіппен төлем жасауды міндеттейді:
— жұмыс құнының 50% мөлшерінде аванстық төлем — осы шартқа қол қойылған күннен бастап 5 (бес) жұмыс күні ішінде;
— 50% мөлшерінде соңғы есептесу — орындалған жұмыс актісіне қол қойылған сәттен бастап :days жұмыс күні ішінде.

3.4. Төлем міндеттемелері ақша :roleB есеп шотына түскен сәттен бастап орындалған болып саналады.

3.5. Төлемді кешіктірген жағдайда :roleA осы шарттың 5-бөлімінде көрсетілген мөлшерде :roleB-ға айыппұл төлейді.",
            ],
            'acceptance' => [
                'ru' => "4. ПОРЯДОК ПРИЁМКИ-ПЕРЕДАЧИ

4.1. Результат работ передаётся Заказчику по Акту сдачи-приёмки, подписываемому обеими Сторонами.

4.2. :roleB обязуется уведомить :roleA о готовности результата работ в письменной форме.

4.3. :roleA обязуется в течение :days рабочих дней с момента получения уведомления и результата работ:
— рассмотреть результат и направить :roleB мотивированный отказ от приёмки с указанием недостатков; либо
— подписать Акт сдачи-приёмки.

4.4. При отсутствии мотивированного отказа в установленный срок Результат считается принятым, а обязательства :roleB — исполненными надлежащим образом.

4.5. В случае выявления недостатков :roleB обязуется устранить их в согласованный Сторонами срок за свой счёт.",
                'en' => "4. ACCEPTANCE AND TRANSFER PROCEDURE

4.1. The result of the work is transferred to the Customer under the Acceptance Act signed by both Parties.

4.2. :roleB undertakes to notify :roleA of the readiness of the work result in writing.

4.3. :roleA undertakes within :days business days from the date of receipt of the notification and the result of the work:
— to review the result and send :roleB a reasoned refusal to accept, indicating the deficiencies; or
— sign the Acceptance Act.

4.4. In the absence of a reasoned refusal within the established period, the Result is considered accepted, and the obligations of :roleB are considered properly fulfilled.

4.5. In case of deficiencies, :roleB undertakes to eliminate them within the period agreed by the Parties at its own expense.",
                'kk' => "4. ҚАБЫЛДАУ-ТАПСЫРУ ТӘРТІБІ

4.1. Жұмыс нәтижесі Тапсырыс берушіге екі Тарап қол қойған Тапсыру-қабылдау актісі бойынша беріледі.

4.2. :roleB жұмыс нәтижесінің дайындығы туралы :roleA-ны жазбаша түрде хабардар етуге міндеттенеді.

4.3. :roleA хабарлама мен жұмыс нәтижесін алған сәттен бастап :days жұмыс күні ішінде:
— нәтижені қарап, :roleB-ға кемшіліктерді көрсете отырып қабылдаудан дәлелді бас тартуды жіберуге; немесе
— Тапсыру-қабылдау актісіне қол қоюға міндеттенеді.

4.4. Белгіленген мерзімде дәлелді бас тарту болмаған жағдайда Нәтиже қабылданды деп саналады, ал :roleB міндеттемелері тиісінше орындалды деп саналады.

4.5. Кемшіліктер анықталған жағдайда :roleB оларды Тараптар келіскен мерзімде өз есебінен жоюға міндеттенеді.",
            ],
            'liability' => [
                'ru' => "5. ОТВЕТСТВЕННОСТЬ СТОРОН

5.1. За неисполнение либо ненадлежащее исполнение обязательств по настоящему договору Стороны несут ответственность в соответствии с действующим законодательством.

5.2. :penalty

5.3. Сторона, нарушившая обязательства по настоящему договору, обязана возместить другой Стороне причинённые убытки в полном объёме, включая упущенную выгоду.

5.4. Уплата неустойки не освобождает Стороны от исполнения обязательств по настоящему договору.

5.5. Общая ответственность :roleB по настоящему договору ограничивается суммой, фактически уплаченной :roleA по договору.",
                'en' => "5. LIABILITY OF THE PARTIES

5.1. For non-performance or improper performance of obligations under this Agreement, the Parties shall be liable in accordance with applicable law.

5.2. :penalty

5.3. The Party that has violated obligations under this Agreement shall compensate the other Party for damages in full, including lost profits.

5.4. Payment of a penalty does not release the Parties from performing obligations under this Agreement.

5.5. The total liability of :roleB under this Agreement is limited to the amount actually paid by :roleA under the Agreement.",
                'kk' => "5. ТАРАПТАРДЫҢ ЖАУАПКЕРШІЛІГІ

5.1. Осы шарт бойынша міндеттемелерді орындамағаны немесе тиісінше орындамағаны үшін Тараптар қолданыстағы заңнамаға сәйкес жауапкершілікке тартылады.

5.2. :penalty

5.3. Осы шарт бойынша міндеттемелерді бұзған Тарап екінші Тарапқа келтірілген зиянды толық көлемде, соның ішінде жіберіп алған пайданы өтеуге міндетті.

5.4. Айыппұл төлеу Тараптарды осы шарт бойынша міндеттемелерді орындаудан босатпайды.

5.5. :roleB-ның осы шарт бойынша жалпы жауапкершілігі :roleA шарт бойынша іс жүзінде төлеген сомамен шектеледі.",
            ],
            'ip_rights' => [
                'ru' => "6. ПРАВА НА ИНТЕЛЛЕКТУАЛЬНУЮ СОБСТВЕННОСТЬ

6.1. Исключительные права на Результат, созданный в рамках настоящего договора, переходят к :roleA с момента полной оплаты и подписания Акта приёмки.

6.2. :roleB сохраняет право использовать наработанные методики, библиотеки и инструменты общего назначения в своей дальнейшей деятельности.

6.3. До момента перехода прав :roleB обязуется не передавать Результат третьим лицам и не использовать его в собственных целях.

6.4. :roleB гарантирует, что Результат не нарушает права третьих лиц на объекты интеллектуальной собственности.",
                'en' => "6. INTELLECTUAL PROPERTY RIGHTS

6.1. Exclusive rights to the Result created under this Agreement shall pass to :roleA from the moment of full payment and signing of the Acceptance Act.

6.2. :roleB retains the right to use the developed methodologies, libraries and general-purpose tools in its further activities.

6.3. Until the transfer of rights, :roleB undertakes not to transfer the Result to third parties and not to use it for its own purposes.

6.4. :roleB guarantees that the Result does not violate the rights of third parties to intellectual property objects.",
                'kk' => "6. ЗИЯТКЕРЛІК МЕНШІКҚҰҚЫҚТАРЫ

6.1. Осы шарт аясында жасалған Нәтижеге ерекше құқықтар толық төлем және Қабылдау актісіне қол қойылған сәттен бастап :roleA-ға өтеді.

6.2. :roleB әзірленген әдістемелерді, кітапханалар мен жалпы мақсаттағы құралдарды өз қызметінде пайдалану құқығын сақтайды.

6.3. Құқықтар өткенге дейін :roleB Нәтижені үшінші тұлғаларға бермеуге және оны өз мақсаттарында пайдаланбауға міндеттенеді.

6.4. :roleB Нәтиженің зияткерлік меншік нысандарына үшінші тұлғалардың құқықтарын бұзбайтынына кепілдік береді.",
            ],
            'termination' => [
                'ru' => "7. СРОК ДЕЙСТВИЯ ДОГОВОРА

7.1. Настоящий договор вступает в силу с :startDate и действует до :endDate.

7.2. Настоящий договор может быть расторгнут:
— по взаимному соглашению Сторон;
— по инициативе одной из Сторон с письменным уведомлением другой Стороны не менее чем за 30 (тридцать) дней;
— при существенном нарушении условий договора одной из Сторон.

7.3. Существенным нарушением считается нарушение, которое влечёт для другой Стороны такой ущерб, что она в значительной степени лишается того, на что была вправе рассчитывать при заключении договора.

7.4. Расторжение договора не освобождает Стороны от исполнения обязательств, возникших до даты его прекращения.",
                'en' => "7. TERM OF THE AGREEMENT

7.1. This Agreement enters into force on :startDate and is valid until :endDate.

7.2. This Agreement may be terminated:
— by mutual agreement of the Parties;
— at the initiative of one of the Parties with written notice to the other Party at least 30 (thirty) days in advance;
— in case of material breach of the terms of the Agreement by one of the Parties.

7.3. A material breach is a breach that entails such damage to the other Party that it is largely deprived of what it was entitled to expect when concluding the Agreement.

7.4. Termination of the Agreement does not release the Parties from fulfilling obligations that arose before the date of its termination.",
                'kk' => "7. ШАРТТЫҢ ӘРЕКЕТ ЕТУ МЕРЗІМІ

7.1. Осы шарт :startDate бастап күшіне енеді және :endDate дейін әрекет етеді.

7.2. Осы шарт тоқтатылуы мүмкін:
— Тараптардың өзара келісімі бойынша;
— Тараптардың біреуінің бастамасымен екінші Тарапқа кемінде 30 (отыз) күн бұрын жазбаша хабарламамен;
— Тараптардың біреуі шарт шарттарын елеулі түрде бұзған жағдайда.

7.3. Елеулі бұзушылық деп екінші Тарапқа шарт жасасқан кезде күтуге құқылы болғанынан айтарлықтай айырылатындай зиян келтіретін бұзушылық саналады.

7.4. Шартты бұзу Тараптарды оның тоқтатылу күніне дейін пайда болған міндеттемелерді орындаудан босатпайды.",
            ],
            'confidentiality' => [
                'ru' => "8. КОНФИДЕНЦИАЛЬНОСТЬ

8.1. Стороны обязуются не разглашать сведения и информацию, полученные в ходе исполнения настоящего договора, третьим лицам без предварительного письменного согласия другой Стороны.

8.2. Конфиденциальной признаётся любая информация, переданная Сторонами в письменной, электронной либо иной форме, включая коммерческую, техническую, финансовую информацию.

8.3. К конфиденциальной информации не относятся сведения:
— ставшие общедоступными не по вине получающей Стороны;
— правомерно полученные от третьих лиц без обязательства о неразглашении;
— независимо разработанные получающей Стороной.

8.4. Обязательства по соблюдению конфиденциальности сохраняют силу в течение 5 (пяти) лет после прекращения действия настоящего договора.",
                'en' => "8. CONFIDENTIALITY

8.1. The Parties undertake not to disclose information received during the performance of this Agreement to third parties without prior written consent of the other Party.

8.2. Any information transmitted by the Parties in written, electronic or other form, including commercial, technical, financial information, is considered confidential.

8.3. Confidential information does not include information:
— that has become publicly available through no fault of the receiving Party;
— lawfully obtained from third parties without an obligation of non-disclosure;
— independently developed by the receiving Party.

8.4. Confidentiality obligations remain in effect for 5 (five) years after the termination of this Agreement.",
                'kk' => "8. ҚҰПИЯЛЫҚ

8.1. Тараптар осы шартты орындау барысында алынған мәліметтер мен ақпаратты екінші Тараптың алдын ала жазбаша келісімінсіз үшінші тұлғаларға жарияламауға міндеттенеді.

8.2. Тараптар арқылы жазбаша, электрондық немесе басқа нысанда берілген кез келген ақпарат, соның ішінде коммерциялық, техникалық, қаржылық ақпарат құпия болып саналады.

8.3. Құпия ақпаратқа мыналар жатпайды:
— алушы Тараптың кінәсінен емес жалпыға қол жетімді болған мәліметтер;
— жарияламау міндеттемесінсіз үшінші тұлғалардан заңды түрде алынған;
— алушы Тарап тәуелсіз әзірлеген.

8.4. Құпиялылықты сақтау міндеттемелері осы шарттың әрекеті тоқтатылғаннан кейін 5 (бес) жыл бойы күшін сақтайды.",
            ],
            'disputes' => [
                'ru' => "9. РАЗРЕШЕНИЕ СПОРОВ

9.1. Все споры и разногласия, возникающие между Сторонами в связи с исполнением настоящего договора, подлежат урегулированию путём переговоров.

9.2. Сторона, права которой нарушены, направляет другой Стороне письменную претензию с изложением существа нарушения и требований.

9.3. Сторона, получившая претензию, обязана рассмотреть её и дать мотивированный ответ в течение 15 (пятнадцати) рабочих дней с даты получения.

9.4. В случае невозможности достижения соглашения в претензионном порядке спор подлежит рассмотрению в судебном порядке в соответствии с :law.

9.5. Стороны соглашаются, что подсудность споров определяется по месту нахождения ответчика.",
                'en' => "9. DISPUTE RESOLUTION

9.1. All disputes and disagreements arising between the Parties in connection with the performance of this Agreement shall be resolved through negotiations.

9.2. The Party whose rights have been violated shall send a written claim to the other Party setting out the essence of the violation and the requirements.

9.3. The Party that received the claim shall review it and provide a reasoned response within 15 (fifteen) business days from the date of receipt.

9.4. If it is impossible to reach an agreement through the pre-trial procedure, the dispute shall be resolved in court in accordance with :law.

9.5. The Parties agree that the jurisdiction of disputes is determined by the location of the defendant.",
                'kk' => "9. ДАУЛАРДЫ ШЕШУ

9.1. Осы шартты орындауға байланысты Тараптар арасында туындайтын барлық даулар мен келіспеушіліктер келіссөздер арқылы шешілуге жатады.

9.2. Құқықтары бұзылған Тарап екінші Тарапқа бұзушылықтың мәні мен талаптарды баяндай отырып жазбаша шағым жібереді.

9.3. Шағымды алған Тарап оны қарап, алған күннен бастап 15 (он бес) жұмыс күні ішінде дәлелді жауап беруге міндетті.

9.4. Претензиялық тәртіппен келісімге қол жеткізу мүмкін болмаған жағдайда, дау :law сәйкес сот тәртібімен қаралуға жатады.

9.5. Тараптар даулардың қараулылығы жауапкердің орналасқан жері бойынша анықталатынымен келіседі.",
            ],
            'force_majeure' => [
                'ru' => "10. ФОРС-МАЖОР

10.1. Стороны освобождаются от ответственности за частичное или полное неисполнение обязательств, если оно вызвано обстоятельствами непреодолимой силы (пожар, наводнение, землетрясение, военные действия, акты государственных органов и т.п.), возникшими после заключения договора.

10.2. Сторона, не имеющая возможности исполнить обязательства, обязана уведомить другую Сторону в течение 3 (трёх) рабочих дней с момента наступления таких обстоятельств, указав их характер и предполагаемый срок действия.

10.3. Подтверждением наличия форс-мажорных обстоятельств являются документы, выданные компетентными государственными органами.

10.4. Если обстоятельства непреодолимой силы продолжаются более 30 (тридцати) дней, любая из Сторон вправе расторгнуть договор в одностороннем порядке без возмещения убытков.",
                'en' => "10. FORCE MAJEURE

10.1. The Parties shall be released from liability for partial or complete non-performance of obligations if it is caused by force majeure circumstances (fire, flood, earthquake, military actions, acts of state authorities, etc.) that arose after the conclusion of the Agreement.

10.2. The Party unable to fulfill its obligations shall notify the other Party within 3 (three) business days from the occurrence of such circumstances, indicating their nature and estimated duration.

10.3. Confirmation of the existence of force majeure circumstances are documents issued by competent state authorities.

10.4. If force majeure circumstances continue for more than 30 (thirty) days, any of the Parties has the right to terminate the Agreement unilaterally without compensation for damages.",
                'kk' => "10. ФОРС-МАЖОР

10.1. Тараптар шарт жасалғаннан кейін пайда болған жеңілмейтін күш жағдайлары (өрт, су тасқыны, жер сілкінісі, әскери әрекеттер, мемлекеттік органдардың актілері және т.б.) салдарынан міндеттемелерді ішінара немесе толық орындамағаны үшін жауапкершіліктен босатылады.

10.2. Міндеттемелерді орындау мүмкіндігі жоқ Тарап мұндай жағдайлар туындаған сәттен бастап 3 (үш) жұмыс күні ішінде екінші Тарапқа олардың сипаты мен болжамды әрекет ету мерзімін көрсете отырып хабарлауға міндетті.

10.3. Форс-мажор жағдайларының бар екенін растау құзыретті мемлекеттік органдар берген құжаттар болып табылады.

10.4. Егер жеңілмейтін күш жағдайлары 30 (отыз) күннен астам уақыт бойы жалғасса, кез келген Тарап шартты зиянды өтемей біржақты тәртіппен бұзуға құқылы.",
            ],
            'notices' => [
                'ru' => "11. УВЕДОМЛЕНИЯ И СВЯЗЬ

11.1. Все уведомления, требования и документы по договору направляются в письменной форме:
— курьером с подтверждением вручения;
— заказным письмом с уведомлением о вручении;
— по электронной почте с подтверждением получения.

11.2. Уведомление считается полученным:
— при личной передаче — в день вручения под роспись;
— при отправке почтой — на 10-й день после отправки;
— по электронной почте — в день отправки при наличии подтверждения прочтения.

11.3. Стороны обязаны незамедлительно уведомлять друг друга об изменении реквизитов, указанных в разделе 13 настоящего договора.

11.4. Уведомления направляются по адресам, указанным в разделе 13 настоящего договора.",
                'en' => "11. NOTICES AND COMMUNICATION

11.1. All notices, demands and documents under the Agreement shall be sent in writing:
— by courier with confirmation of delivery;
— by registered mail with acknowledgment of receipt;
— by e-mail with confirmation of receipt.

11.2. The notice is considered received:
— upon personal delivery — on the day of delivery against signature;
— when sent by mail — on the 10th day after sending;
— by e-mail — on the day of sending, provided there is confirmation of reading.

11.3. The Parties are obliged to immediately notify each other of any changes in the details specified in Section 13 of this Agreement.

11.4. Notices shall be sent to the addresses specified in Section 13 of this Agreement.",
                'kk' => "11. ХАБАРЛАМАЛАР ЖӘНЕ БАЙЛАНЫС

11.1. Шарт бойынша барлық хабарламалар, талаптар мен құжаттар жазбаша түрде жіберіледі:
— тапсырылғанын растаумен курьер арқылы;
— тапсырылғаны туралы хабарламамен тапсырыс хатпен;
— алынғанын растаумен электрондық пошта арқылы.

11.2. Хабарлама алынды деп саналады:
— жеке тапсыру кезінде — қолхатпен тапсырылған күні;
— поштамен жібергенде — жіберілгеннен кейін 10-шы күні;
— электрондық пошта арқылы — оқылғанын растау болған жағдайда жіберілген күні.

11.3. Тараптар осы шарттың 13-бөлімінде көрсетілген реквизиттердің өзгеруі туралы бір-бірін дереу хабардар етуге міндетті.

11.4. Хабарламалар осы шарттың 13-бөлімінде көрсетілген мекенжайларға жіберіледі.",
            ],
            'specialTerms' => [
                'ru' => "12. ОСОБЫЕ УСЛОВИЯ

12.1. :terms

12.2. Стороны признают, что настоящее соглашение содержит все договорённости между ними по предмету договора.",
                'en' => "12. SPECIAL TERMS

12.1. :terms

12.2. The Parties acknowledge that this Agreement contains all agreements between them on the subject matter of the Agreement.",
                'kk' => "12. АРНАЙЫ ШАРТТАР

12.1. :terms

12.2. Тараптар осы келісімде шарттың мәні бойынша олардың арасындағы барлық келісімдер бар екенін мойындайды.",
            ],
            'entire_agreement' => [
                'ru' => "13. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ

13.1. Настоящий договор содержит всё соглашение Сторон и отменяет все предыдущие устные и письменные договорённости по предмету договора.

13.2. Любые изменения и дополнения к настоящему договору действительны только в письменной форме, подписанной уполномоченными представителями обеих Сторон.

13.3. Если любое положение договора будет признано недействительным или неисполнимым, это не влияет на силу остальных положений, которые остаются в полной силе.

13.4. Во всём остальном, что не урегулировано настоящим договором, Стороны руководствуются :law.

13.5. Договор составлен в двух экземплярах, имеющих равную юридическую силу, по одному для каждой из Сторон.

13.6. Все приложения и дополнительные соглашения к настоящему договору являются его неотъемлемой частью.",
                'en' => "13. FINAL PROVISIONS

13.1. This Agreement contains the entire agreement of the Parties and cancels all previous oral and written agreements on the subject matter of the Agreement.

13.2. Any amendments and additions to this Agreement are valid only in writing, signed by authorized representatives of both Parties.

13.3. If any provision of the Agreement is found to be invalid or unenforceable, this does not affect the validity of the remaining provisions, which remain in full force.

13.4. In all other matters not regulated by this Agreement, the Parties shall be guided by :law.

13.5. The Agreement is drawn up in two copies having equal legal force, one for each of the Parties.

13.6. All appendices and additional agreements to this Agreement are an integral part thereof.",
                'kk' => "13. ҚОРЫТЫНДЫ ЕРЕЖЕЛЕР

13.1. Осы шарт Тараптардың барлық келісімін қамтиды және шарттың мәні бойынша барлық бұрынғы ауызша және жазбаша келісімдердің күшін жояды.

13.2. Осы шартқа кез келген өзгерістер мен толықтырулар екі Тараптың уәкілетті өкілдері қол қойған жазбаша нысанда ғана жарамды.

13.3. Егер шарттың кез келген ережесі жарамсыз немесе орындалмайтын деп танылса, бұл толық күшінде қалатын қалған ережелердің күшіне әсер етпейді.

13.4. Осы шартпен реттелмеген барлық басқа мәселелер бойынша Тараптар :law басшылыққа алады.

13.5. Шарт заңды күші тең екі данада жасалған, әр Тарап үшін бір данадан.

13.6. Осы шартқа барлық қосымшалар мен қосымша келісімдер оның ажырамас бөлігі болып табылады.",
            ],
            'signatures' => [
                'ru' => "14. АДРЕСА, РЕКВИЗИТЫ И ПОДПИСИ СТОРОН

:roleA:
:customer

Юридический адрес: ____________________
Почтовый адрес: ____________________
ИНН/БИН: ____________________
Расчётный счёт: ____________________
Банк: ____________________
БИК/МФО: ____________________

____________________ / ____________________ /
(подпись) (Ф.И.О.)

М.П.

:roleB:
:contractor

Юридический адрес: ____________________
Почтовый адрес: ____________________
ИНН/БИН: ____________________
Расчётный счёт: ____________________
Банк: ____________________
БИК/МФО: ____________________

____________________ / ____________________ /
(подпись) (Ф.И.О.)

М.П.",
                'en' => "14. ADDRESSES, DETAILS AND SIGNATURES OF THE PARTIES

:roleA:
:customer

Legal address: ____________________
Mailing address: ____________________
Tax ID: ____________________
Bank account: ____________________
Bank: ____________________
SWIFT: ____________________

____________________ / ____________________ /
(signature) (Full name)

Seal

:roleB:
:contractor

Legal address: ____________________
Mailing address: ____________________
Tax ID: ____________________
Bank account: ____________________
Bank: ____________________
SWIFT: ____________________

____________________ / ____________________ /
(signature) (Full name)

Seal",
                'kk' => "14. МЕКЕНЖАЙЛАР, РЕКВИЗИТТЕР ЖӘНЕ ТАРАПТАРДЫҢ ҚОЛДАРЫ

:roleA:
:customer

Заңды мекенжайы: ____________________
Пошта мекенжайы: ____________________
Салық нөмірі: ____________________
Есеп шоты: ____________________
Банк: ____________________
БИК/МФО: ____________________

____________________ / ____________________ /
(қолы) (Т.А.Ә.)

М.О.

:roleB:
:contractor

Заңды мекенжайы: ____________________
Пошта мекенжайы: ____________________
Салық нөмірі: ____________________
Есеп шоты: ____________________
Банк: ____________________
БИК/МФО: ____________________

____________________ / ____________________ /
(қолы) (Т.А.Ә.)

М.О.",
            ],
        ],
        'timeline' => [
            'template' => 'Шаблон: :value',
            'parties' => 'Стороны: :value',
            'term' => 'Срок действия: :value',
            'sections' => 'Разделы: :value',
        ],
    ];
@endphp

<div class="toast-container" id="toastContainer"></div>

<nav class="sticky top-0 z-40 glass-strong border-b border-black/5 dark:border-white/5">
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-base leading-tight gradient-text">LegalAI Pro</div>
                    <div class="text-[10px] text-neutral-500 dark:text-neutral-400 font-medium">Contract Builder</div>
                </div>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('welcome') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/5 transition">{{ __('ui.nav_home') }}</a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-neutral-900 dark:text-white bg-black/5 dark:bg-white/10">{{ __('ui.nav_features') }}</a>
                <x-language-switcher />

                <div class="w-px h-6 bg-black/10 dark:bg-white/10 mx-2"></div>

                <div class="theme-switch" id="themeToggle" role="button" tabindex="0" aria-label="{{ __('ui.theme_toggle') }}">
                    <div class="theme-switch-thumb">
                        <svg class="w-3.5 h-3.5 text-amber-500 block dark:hidden" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 3V4m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg class="w-3.5 h-3.5 text-indigo-300 hidden dark:block" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </div>
                </div>

                <button id="notificationsButton" class="relative p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition">
                    <svg class="w-5 h-5 text-neutral-600 dark:text-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 rounded-full text-[9px] font-bold flex items-center justify-center text-white ring-2 ring-white dark:ring-neutral-900">3</span>
                </button>

                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfile()" class="flex items-center gap-2 p-1 pr-3 rounded-full hover:bg-black/5 dark:hover:bg-white/5 transition" id="profileButton">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-md ring-2 ring-white dark:ring-neutral-900">
                            {{ $userInitials }}
                        </div>
                        <span class="hidden lg:block text-sm font-medium">{{ $userName }}</span>
                        <svg class="w-4 h-4 text-neutral-500 transition-transform" id="profileArrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="profileDropdown" class="hidden dropdown-premium">
                        <div class="p-4 border-b border-black/5 dark:border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md">
                                    {{ $userInitials }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold truncate">{{ $userName }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 truncate">{{ $userEmail }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('ui.profile') }}
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ __('ui.settings') }}
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                {{ __('ui.my_documents') }}
                            </a>
                        </div>
                        <div class="border-t border-black/5 dark:border-white/10 py-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    {{ __('ui.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg hover:bg-black/5 dark:hover:bg-white/5 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
</nav>

<main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    <section class="hero-premium rounded-[28px] p-6 lg:p-10 mb-8 text-white shadow-2xl shadow-indigo-500/20 animate-slide-up">
        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <span class="chip-premium">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="  0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    AI-Powered
                </span>
                <span class="chip-premium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Legally Verified
                </span>
                <span class="chip-premium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Multi-format Export
                </span>
            </div>

            <h1 class="text-3xl lg:text-5xl font-bold mb-3 tracking-tight leading-tight">
                {{ __('ui.dashboard_builder_title') }}
            </h1>
            <p class="text-white/80 text-base lg:text-lg mb-8 max-w-2xl leading-relaxed">
                {{ __('ui.dashboard_builder_text') }}
            </p>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                <div class="metric-card">
                    <div class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">{{ __('ui.dashboard_active_template') }}</div>
                    <div class="font-bold text-sm lg:text-base truncate" id="heroTemplateLabel">{{ __('ui.template_service') }}</div>
                </div>
                <div class="metric-card">
                    <div class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">{{ __('ui.dashboard_sections_enabled') }}</div>
                    <div class="font-bold text-2xl lg:text-3xl" id="heroSectionCount">4</div>
                </div>
                <div class="metric-card">
                    <div class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">{{ __('ui.dashboard_contract_sum') }}</div>
                    <div class="font-bold text-sm lg:text-base" id="heroAmount">120 000 ₽</div>
                </div>
                <div class="metric-card">
                    <div class="text-white/70 text-xs font-medium uppercase tracking-wider mb-1">{{ __('ui.dashboard_start_date') }}</div>
                    <div class="font-bold text-sm lg:text-base" id="heroStartDate">15.04.2026</div>
                </div>
            </div>
        </div>
    </section>

    <div class="glass rounded-2xl p-5 mb-6 flex items-center gap-3 lg:gap-6">
        <div class="flex items-center gap-3">
            <div class="stepper-dot bg-indigo-500 text-white" id="step1">1</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_choose_template') }}</div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">Тип договора</div>
            </div>
        </div>
        <div class="stepper-line" id="line1"></div>
        <div class="flex items-center gap-3">
            <div class="stepper-dot bg-black/5 dark:bg-white/10 text-neutral-500" id="step2">2</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_fill_params') }}</div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">Параметры</div>
            </div>
        </div>
        <div class="stepper-line" id="line2"></div>
        <div class="flex items-center gap-3">
            <div class="stepper-dot bg-black/5 dark:bg-white/10 text-neutral-500" id="step3">3</div>
            <div class="hidden sm:block">
                <div class="text-sm font-semibold">{{ __('ui.dashboard_enable_sections') }}</div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">Разделы</div>
            </div>
        </div>
        <div class="ml-auto flex items-center gap-2 text-xs text-neutral-500 dark:text-neutral-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            <span class="hidden sm:inline">Автосохранение</span>
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse-slow"></span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-7 space-y-6">
            <div class="glass rounded-3xl p-6 lg:p-8 animate-slide-up">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-full text-xs font-semibold mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            ШАГ 1
                        </div>
                        <h2 class="text-xl lg:text-2xl font-bold mb-1 tracking-tight">{{ __('ui.dashboard_choose_template') }}</h2>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('ui.dashboard_choose_template_text') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="templateCards">
                    @foreach ([
                        'service' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', 'gradient' => 'from-blue-500 to-indigo-600'],
                        'supply' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>', 'gradient' => 'from-emerald-500 to-teal-600'],
                        'nda' => ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>', 'gradient' => 'from-amber-500 to-orange-600']
                    ] as $template => $meta)
                        <button type="button" class="template-card-premium glass rounded-2xl p-5 text-left border-2 border-transparent {{ $template === 'service' ? 'active' : '' }}" data-template="{{ $template }}">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $meta['gradient'] }} flex items-center justify-center text-white shadow-lg mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $meta['icon'] !!}</svg>
                            </div>
                            <div class="font-bold mb-1.5">{{ $builderTranslations['templates'][$template]['label'] }}</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400 mb-3 leading-relaxed">{{ $builderTranslations['templates'][$template]['description'] }}</div>
                            <span class="inline-block px-2.5 py-1 bg-black/5 dark:bg-white/10 rounded-md text-xs font-semibold">
                                {{ $builderTranslations['templates'][$template]['badge'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="glass rounded-3xl p-6 lg:p-8 animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-full text-xs font-semibold mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                            ШАГ 2
                        </div>
                        <h2 class="text-xl lg:text-2xl font-bold mb-1 tracking-tight">{{ __('ui.dashboard_fill_params') }}</h2>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('ui.dashboard_fill_params_text') }}</p>
                    </div>
                    <button type="button" id="resetFormBtn" class="text-xs text-neutral-500 hover:text-red-500 transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Сбросить
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="input-premium">
                        <label>{{ __('ui.contract_number') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            <input id="contractNumber" type="text" value="LA-042/26" placeholder="Например: LA-001/26">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.city_of_signing') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <input id="contractCity" type="text" value="Алматы">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.start_date') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <input id="startDate" type="date" value="2026-04-15">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.end_date') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <input id="endDate" type="date" value="2026-12-31">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.customer_buyer') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <input id="customerName" type="text" value="TOO LegalAI Client">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.contractor_supplier') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <input id="contractorName" type="text" value="TOO LegalAI Pro">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.contract_subject') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <input id="contractSubject" type="text" value="{{ __('ui.service_default_subject') }}">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.amount') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <input id="contractAmount" type="number" min="0" step="1000" value="120000">
                        </div>
                    </div>
                    <div class="input-premium">
                        <label>{{ __('ui.payment_days') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <input id="paymentDays" type="number" min="1" value="5">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.governing_law') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <input id="governingLaw" type="text" value="Право Республики Казахстан">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.penalty') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <input id="penaltyRule" type="text" value="{{ __('ui.default_penalty_rule') }}">
                        </div>
                    </div>
                    <div class="input-premium md:col-span-2">
                        <label>{{ __('ui.special_terms') }}</label>
                        <div class="relative">
                            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <textarea id="specialTerms" rows="3" style="padding-top: 0.875rem;">Исполнитель предоставляет ежемесячный отчёт о выполненных работах и фиксирует замечания заказчика в течение 2 рабочих дней.</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass rounded-3xl p-6 lg:p-8 animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-pink-500/10 text-pink-600 dark:text-pink-400 rounded-full text-xs font-semibold mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                            ШАГ 3
                        </div>
                        <h2 class="text-xl lg:text-2xl font-bold mb-1 tracking-tight">{{ __('ui.dashboard_enable_sections') }}</h2>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('ui.dashboard_enable_sections_text') }}</p>
                    </div>
                    <div class="text-xs text-neutral-500 dark:text-neutral-400 bg-black/5 dark:bg-white/5 px-3 py-1.5 rounded-lg">
                        Выбрано: <span id="sectionsCounter" class="font-bold text-indigo-600 dark:text-indigo-400">4</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ([
                        'definitions' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'scope' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'payment' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'acceptance' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'liability' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        'ip_rights' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                        'termination' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'confidentiality' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
                    ] as $section => $icon)
                        <label class="section-card glass rounded-xl p-4 border-2 border-transparent {{ in_array($section, ['definitions', 'scope', 'payment', 'acceptance', 'liability', 'termination'], true) ? 'active' : '' }}">
                            <div class="flex items-start gap-3">
                                <input class="builder-section" type="checkbox" value="{{ $section }}" {{ in_array($section, ['definitions', 'scope', 'payment', 'acceptance', 'liability', 'termination'], true) ? 'checked' : '' }}>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icon !!}</svg>
                                        <div class="font-semibold text-sm">{{ $builderTranslations['sections'][$section]['label'] }}</div>
                                    </div>
                                    <div class="text-xs text-neutral-600 dark:text-neutral-400 leading-relaxed">{{ $builderTranslations['sections'][$section]['description'] }}</div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-2 mt-5 pt-5 border-t border-black/5 dark:border-white/5" id="activeSectionBadges"></div>
            </div>
        </div>

        <div class="xl:col-span-5">
            <div class="glass rounded-3xl p-6 preview-sticky sticky top-24 animate-slide-up" style="animation-delay: 0.15s">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-semibold mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse-slow"></span>
                            LIVE PREVIEW
                        </div>
                        <h2 class="text-xl font-bold mb-1 tracking-tight">{{ __('ui.draft_contract') }}</h2>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('ui.draft_contract_text') }}</p>
                    </div>
                </div>

                <div class="preview-toolbar mb-4 flex-wrap">
                    <button type="button" class="toolbar-btn" id="copyContractButton">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        {{ __('ui.copy') }}
                    </button>
                    <div class="w-px h-5 bg-black/10 dark:bg-white/10 mx-1"></div>
                    <div class="relative">
                        <button type="button" class="toolbar-btn bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:opacity-90" id="downloadDropdownButton">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ __('ui.download') }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div id="downloadDropdown" class="hidden dropdown-premium">
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition text-left" id="downloadTxtButton">
                                <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('ui.txt_document') }}
                            </button>
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition text-left" id="downloadPdfButton">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                {{ __('ui.pdf_document') }}
                            </button>
                            <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-black/5 dark:hover:bg-white/5 transition text-left" id="downloadDocxButton">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('ui.word_document') }}
                            </button>
                        </div>
                    </div>
                    <div class="ml-auto flex items-center gap-1">
                        <button type="button" class="toolbar-btn" id="fontSmaller" title="Уменьшить шрифт">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        </button>
                        <span class="text-xs font-mono text-neutral-600 dark:text-neutral-400 px-1" id="fontSizeLabel">13</span>
                        <button type="button" class="toolbar-btn" id="fontBigger" title="Увеличить шрифт">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                <div class="contract-preview rounded-xl p-5 lg:p-6 border border-black/5 dark:border-white/10 max-h-[500px] overflow-y-auto mb-5 shadow-inner">
                    <pre id="contractPreview" class="font-mono"></pre>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="insight-success rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="text-xs font-semibold text-green-700 dark:text-green-400 uppercase tracking-wider">{{ __('ui.build_status') }}</span>
                        </div>
                        <div class="font-bold text-sm" id="previewStatus">{{ __('ui.draft_ready') }}</div>
                    </div>
                    <div class="insight-warning rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">{{ __('ui.reminder') }}</span>
                        </div>
                        <div class="font-bold text-sm">{{ __('ui.review_required') }}</div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <div class="text-sm font-bold tracking-tight">{{ __('ui.build_plan') }}</div>
                    </div>
                    <div class="space-y-2" id="timeline"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const translations = @json($builderTranslations);
    const locale = @json(app()->getLocale());

    function showToast(message, type = 'info', duration = 3000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const icons = {
            success: '<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            error: '<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            info: '<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        };
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `${icons[type]}<div class="flex-1 text-sm font-medium">${message}</div>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    (function initTheme() {
        const saved = localStorage.getItem('legalai-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = saved || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'dark') document.documentElement.classList.add('dark');
    })();

    document.getElementById('themeToggle')?.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        document.documentElement.classList.toggle('dark', next === 'dark');
        localStorage.setItem('legalai-theme', next);
    });

    let profileOpen = false;
    function toggleProfile() {
        profileOpen = !profileOpen;
        document.getElementById('profileDropdown').classList.toggle('hidden', !profileOpen);
        document.getElementById('profileArrow').style.transform = profileOpen ? 'rotate(180deg)' : 'rotate(0)';
    }

    document.getElementById('notificationsButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        showToast('Уведомления: 3 новых события', 'info');
    });

    let languageSwitcherOpen = false;
    document.getElementById('languageSwitcherButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        languageSwitcherOpen = !languageSwitcherOpen;
        document.getElementById('languageSwitcherDropdown').classList.toggle('hidden', !languageSwitcherOpen);
        document.getElementById('languageSwitcherArrow').style.transform = languageSwitcherOpen ? 'rotate(180deg)' : 'rotate(0)';
    });

    document.addEventListener('click', (e) => {
        if (profileOpen && !document.getElementById('profileContainer').contains(e.target)) {
            profileOpen = false;
            document.getElementById('profileDropdown').classList.add('hidden');
            document.getElementById('profileArrow').style.transform = 'rotate(0)';
        }
        if (languageSwitcherOpen && !document.getElementById('languageSwitcher').contains(e.target)) {
            languageSwitcherOpen = false;
            document.getElementById('languageSwitcherDropdown').classList.add('hidden');
            document.getElementById('languageSwitcherArrow').style.transform = 'rotate(0)';
        }
        if (!e.target.closest('#downloadDropdown') && !e.target.closest('#downloadDropdownButton')) {
            document.getElementById('downloadDropdown')?.classList.add('hidden');
        }
    });

    document.getElementById('downloadDropdownButton')?.addEventListener('click', (e) => {
        e.stopPropagation();
        document.getElementById('downloadDropdown').classList.toggle('hidden');
    });

    const templateConfig = translations.templates || {};
    const sectionConfig = translations.sections || {};
    const state = { template: 'service' };

    const fieldIds = ['contractNumber','contractCity','startDate','endDate','customerName','contractorName','contractSubject','contractAmount','paymentDays','governingLaw','penaltyRule','specialTerms'];
    const fields = {};
    fieldIds.forEach(id => fields[id] = document.getElementById(id));

    const previewElement = document.getElementById('contractPreview');
    const sectionInputs = Array.from(document.querySelectorAll('.builder-section'));
    const templateCards = Array.from(document.querySelectorAll('[data-template]'));

    const STORAGE_KEY = 'legalai-contract-draft';

    function saveToLocalStorage() {
        const data = {
            template: state.template,
            fields: {},
            sections: getSelectedSections(),
            fontSize: currentFontSize
        };
        fieldIds.forEach(id => { if (fields[id]) data.fields[id] = fields[id].value; });
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(data)); } catch(e) {}
    }

    function loadFromLocalStorage() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            const data = JSON.parse(raw);
            if (data.template && templateConfig[data.template]) state.template = data.template;
            if (data.fields) {
                fieldIds.forEach(id => {
                    if (fields[id] && data.fields[id] !== undefined) fields[id].value = data.fields[id];
                });
            }
            if (data.sections) {
                sectionInputs.forEach(input => {
                    input.checked = data.sections.includes(input.value);
                    input.closest('.section-card')?.classList.toggle('active', input.checked);
                });
            }
            if (data.fontSize) {
                currentFontSize = data.fontSize;
                previewElement.style.fontSize = currentFontSize + 'px';
                document.getElementById('fontSizeLabel').textContent = currentFontSize;
            }
        } catch(e) {}
    }

    function resetForm() {
        if (!confirm('Сбросить все данные формы?')) return;
        localStorage.removeItem(STORAGE_KEY);
        location.reload();
    }

    document.getElementById('resetFormBtn')?.addEventListener('click', resetForm);

    function replaceTokens(template, replacements = {}) {
        return Object.entries(replacements).reduce((r, [k, v]) => r.replaceAll(`:${k}`, v), template);
    }

    function syncTemplateSelection() {
        templateCards.forEach(c => c.classList.toggle('active', c.dataset.template === state.template));
    }

    function getSelectedSections() {
        return sectionInputs.filter(i => i.checked).map(i => i.value);
    }

    function formatAmount(v) {
        return `${new Intl.NumberFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(Number(v || 0))} ${translations.ui?.rubCurrencySymbol || '₽'}`;
    }

    function formatDate(v) {
        if (!v) return translations.ui?.notSpecified || 'Не указано';
        try { return new Intl.DateTimeFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(new Date(v)); }
        catch { return translations.ui?.notSpecified || 'Не указано'; }
    }

    function buildSectionText(template, sections, values) {
        const blocks = [];
        const lang = locale || 'ru';
        const d = translations.draft || {};
        const get = (key) => d[key]?.[lang] || d[key]?.ru || '';

        if (sections.includes('definitions')) blocks.push(get('definitions'));
        if (sections.includes('scope')) blocks.push(replaceTokens(get('scope'), { roleB: template.roleB, subject: values.subject, roleA: template.roleA }));
        if (sections.includes('payment')) blocks.push(replaceTokens(get('payment'), { amount: formatAmount(values.amount), roleA: template.roleA, days: values.paymentDays, roleB: template.roleB }));
        if (sections.includes('acceptance')) blocks.push(replaceTokens(get('acceptance'), { roleB: template.roleB, roleA: template.roleA, days: values.paymentDays }));
        if (sections.includes('liability')) blocks.push(replaceTokens(get('liability'), { penalty: values.penaltyRule, roleB: template.roleB, roleA: template.roleA }));
        if (sections.includes('ip_rights')) blocks.push(replaceTokens(get('ip_rights'), { roleB: template.roleB, roleA: template.roleA }));
        if (sections.includes('termination')) blocks.push(replaceTokens(get('termination'), { startDate: formatDate(values.startDate), endDate: formatDate(values.endDate) }));
        if (sections.includes('confidentiality')) blocks.push(get('confidentiality'));
        if (sections.includes('disputes')) blocks.push(replaceTokens(get('disputes'), { law: values.governingLaw }));
        if (sections.includes('force_majeure')) blocks.push(get('force_majeure'));
        if (sections.includes('notices')) blocks.push(get('notices'));
        blocks.push(replaceTokens(get('specialTerms'), { terms: values.specialTerms }));
        blocks.push(replaceTokens(get('entire_agreement'), { law: values.governingLaw }));
        blocks.push(replaceTokens(get('signatures'), { roleA: template.roleA, customer: values.customerName, roleB: template.roleB, contractor: values.contractorName }));
        return blocks.join('\n\n');
    }

    function buildContractText() {
        const template = templateConfig[state.template];
        if (!template) return '';
        const ui = translations.ui || {};
        const values = {
            number: fields.contractNumber?.value.trim() || ui.withoutNumber || 'Без номера',
            city: fields.contractCity?.value.trim() || ui.notSpecified || 'Не указано',
            startDate: fields.startDate?.value,
            endDate: fields.endDate?.value,
            customerName: fields.customerName?.value.trim() || ui.party1 || 'Сторона 1',
            contractorName: fields.contractorName?.value.trim() || ui.party2 || 'Сторона 2',
            subject: fields.contractSubject?.value.trim() || template.defaultSubject,
            amount: fields.contractAmount?.value,
            paymentDays: fields.paymentDays?.value || '5',
            governingLaw: fields.governingLaw?.value.trim() || ui.applicableLaw || 'действующим законодательством',
            penaltyRule: fields.penaltyRule?.value.trim() || ui.defaultPenaltyRule,
            specialTerms: fields.specialTerms?.value.trim() || ui.defaultSpecialTerms
        };

        const d = translations.draft || {};
        const headerText = d.header?.[locale] || d.header?.ru || '';
        const header = `${template.label}\n№ ${values.number}\nг. ${values.city}\n${formatDate(values.startDate)}\n\n${replaceTokens(headerText, { customer: values.customerName, roleA: template.roleA, contractor: values.contractorName, roleB: template.roleB })}`;
        return `${header}\n\n${buildSectionText(template, getSelectedSections(), values)}`;
    }

    function renderBadges(selected) {
        const c = document.getElementById('activeSectionBadges');
        c.innerHTML = '';
        selected.forEach(s => {
            const b = document.createElement('span');
            b.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-indigo-500/10 to-purple-500/10 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-semibold border border-indigo-500/20';
            b.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>${sectionConfig[s]?.label || s}`;
            c.appendChild(b);
        });
    }

    function renderTimeline(selected) {
        const template = templateConfig[state.template];
        if (!template) return;
        const t = translations.timeline || {};
        const items = [
            replaceTokens(t.template || 'Шаблон: :value', { value: template.label }),
            replaceTokens(t.parties || 'Стороны: :value', { value: `${fields.customerName?.value.trim() || '—'} / ${fields.contractorName?.value.trim() || '—'}` }),
            replaceTokens(t.term || 'Срок: :value', { value: `${formatDate(fields.startDate?.value)} - ${formatDate(fields.endDate?.value)}` }),
            replaceTokens(t.sections || 'Разделы: :value', { value: selected.map(s => sectionConfig[s]?.label).join(', ') || '—' })
        ];
        const tl = document.getElementById('timeline');
        tl.innerHTML = '';
        items.forEach((item, i) => {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 p-2.5 rounded-lg text-xs hover:bg-black/5 dark:hover:bg-white/5 transition';
            div.innerHTML = `
                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0">${i + 1}</div>
                <div class="text-neutral-700 dark:text-neutral-300">${item}</div>
            `;
            tl.appendChild(div);
        });
    }

    function renderHero(selected) {
        const template = templateConfig[state.template];
        if (!template) return;
        document.getElementById('heroTemplateLabel').textContent = template.label;
        document.getElementById('heroSectionCount').textContent = String(selected.length);
        document.getElementById('heroAmount').textContent = formatAmount(fields.contractAmount?.value);
        document.getElementById('heroStartDate').textContent = formatDate(fields.startDate?.value);
    }

    function updateStepper(selected) {
        const hasTemplate = !!state.template;
        const hasFields = fields.contractNumber?.value && fields.customerName?.value && fields.contractorName?.value;
        const hasSections = selected.length > 0;

        const setStep = (id, active, lineId) => {
            const el = document.getElementById(id);
            if (active) {
                el.className = 'stepper-dot bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/30';
            } else {
                el.className = 'stepper-dot bg-black/5 dark:bg-white/10 text-neutral-500';
            }
            if (lineId) {
                document.getElementById(lineId)?.classList.toggle('active', active);
            }
        };

        setStep('step1', hasTemplate, null);
        setStep('step2', hasFields, 'line1');
        setStep('step3', hasSections, 'line2');
    }

    let debounceTimer;
    function renderBuilder() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const selected = getSelectedSections();
            previewElement.textContent = buildContractText();
            renderBadges(selected);
            renderTimeline(selected);
            renderHero(selected);
            updateStepper(selected);
            document.getElementById('sectionsCounter').textContent = selected.length;
            saveToLocalStorage();
        }, 150);
    }

    templateCards.forEach(c => c.addEventListener('click', () => {
        state.template = c.dataset.template;
        if (fields.contractSubject && templateConfig[state.template]) {
            fields.contractSubject.value = templateConfig[state.template].defaultSubject;
        }
        syncTemplateSelection();
        renderBuilder();
    }));

    Object.values(fields).forEach(f => {
        if (!f) return;
        f.addEventListener('input', renderBuilder);
        f.addEventListener('change', renderBuilder);
    });

    sectionInputs.forEach(input => {
        input.addEventListener('change', () => {
            input.closest('.section-card').classList.toggle('active', input.checked);
            renderBuilder();
        });
    });

    let currentFontSize = 13;
    document.getElementById('fontSmaller')?.addEventListener('click', () => {
        if (currentFontSize > 10) {
            currentFontSize -= 1;
            previewElement.style.fontSize = currentFontSize + 'px';
            document.getElementById('fontSizeLabel').textContent = currentFontSize;
            saveToLocalStorage();
        }
    });
    document.getElementById('fontBigger')?.addEventListener('click', () => {
        if (currentFontSize < 20) {
            currentFontSize += 1;
            previewElement.style.fontSize = currentFontSize + 'px';
            document.getElementById('fontSizeLabel').textContent = currentFontSize;
            saveToLocalStorage();
        }
    });

    document.getElementById('copyContractButton')?.addEventListener('click', async () => {
        if (!navigator.clipboard) return;
        await navigator.clipboard.writeText(previewElement.textContent);
        showToast('Текст скопирован в буфер обмена', 'success');
    });

    document.getElementById('downloadTxtButton')?.addEventListener('click', () => {
        const blob = new Blob([previewElement.textContent], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `contract-${fields.contractNumber?.value || 'draft'}.txt`;
        a.click();
        URL.revokeObjectURL(url);
        showToast('TXT документ успешно скачан', 'success');
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    document.getElementById('downloadPdfButton')?.addEventListener('click', async () => {
        try {
            await html2pdf().set({
                margin: [10, 10, 10, 10],
                filename: `contract-${fields.contractNumber?.value || 'draft'}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(previewElement).save();
            showToast('PDF документ успешно скачан', 'success');
        } catch (err) {
            console.error(err);
            showToast('Ошибка при создании PDF', 'error');
        }
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    document.getElementById('downloadDocxButton')?.addEventListener('click', async () => {
        try {
            if (typeof docx === 'undefined' && typeof window.docx === 'undefined') throw new Error('Библиотека docx не загружена');
            const DocxLib = window.docx || docx;
            const { Document, Packer, Paragraph } = DocxLib;
            const text = previewElement.textContent || '';
            if (!text.trim()) throw new Error('Нет текста');

            const lines = text.split('\n');
            const children = lines.map(line => {
                const trimmed = line.trim();
                if (!trimmed) return new Paragraph({ text: '' });
                const isHeader = /^[0-9]+\.?\s/.test(trimmed);
                return new Paragraph({ text: trimmed, bold: isHeader, size: isHeader ? 28 : 24, spacing: { after: isHeader ? 400 : 200, line: 360 } });
            });

            const doc = new Document({ sections: [{ properties: {}, children }] });
            const blob = await Packer.toBlob(doc);
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `contract-${(fields.contractNumber?.value || 'draft').replace(/[^а-яА-ЯёЁa-zA-Z0-9-_]/g, '')}.docx`;
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 100);
            showToast('DOCX документ успешно скачан', 'success');
        } catch (err) {
            console.error(err);
            showToast('Ошибка создания DOCX: ' + err.message, 'error');
        }
        document.getElementById('downloadDropdown').classList.add('hidden');
    });

    loadFromLocalStorage();
    syncTemplateSelection();
    renderBuilder();
</script>
</body>
</html>
