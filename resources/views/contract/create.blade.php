<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ui.dashboard_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://unpkg.com/docx@8.2.2/build/index.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        .form-modern-section {
            background: linear-gradient(180deg, rgba(255, 253, 248, 0.6) 0%, rgba(255, 253, 248, 0) 100%);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1.5rem;
            transition: border-color 0.2s ease;
        }
        [data-theme="dark"] .form-modern-section {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.6) 0%, rgba(30, 41, 59, 0) 100%);
        }
        .form-section-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed var(--line);
        }
        .form-section-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--brand);
            box-shadow: 0 0 0 3px rgba(20, 71, 230, 0.15);
        }
        .form-label-modern {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .form-control-modern {
            border-radius: 14px;
            border: 1.5px solid var(--line);
            background: var(--bg-paper);
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--ink);
        }
        .form-control-modern:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(20, 71, 230, 0.1);
            background: #fff;
        }
        [data-theme="dark"] .form-control-modern:focus {
            background: #1e293b;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .form-control-modern::placeholder {
            color: var(--muted);
            opacity: 0.5;
        }
        .form-help-text {
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .input-group-custom {
            position: relative;
        }
        .input-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            opacity: 0.6;
        }
        .form-card-badge {
            background: rgba(20, 71, 230, 0.08);
            color: var(--brand);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .form-grid-divider {
            height: 1px;
            background: var(--line);
            margin: 1.5rem 0;
        }
        :root {
            --bg-soft: #f3f1ea;
            --bg-paper: #fffdf8;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: rgba(31, 41, 55, 0.08);
            --brand: #1447e6;
            --brand-deep: #0d2d8a;
            --accent: #c77d2b;
            --success-soft: #e8f7ee;
            --warning-soft: #fff2dc;
            --surface-card-bg: rgba(255, 253, 248, 0.96);
            --preview-card-bg: linear-gradient(180deg, #fffefb 0%, #faf7f1 100%);
            --contract-sheet-bg: var(--bg-paper);
            --chip-bg: #eef3ff;
            --chip-color: var(--brand-deep);
            --hero-metric-bg: rgba(255, 255, 255, 0.1);
            --hero-metric-border: rgba(255, 255, 255, 0.12);
        }

        [data-theme="dark"] {
            --bg-soft: #0f172a;
            --bg-paper: #1e293b;
            --ink: #e2e8f0;
            --muted: #94a3b8;
            --line: rgba(148, 163, 184, 0.15);
            --brand: #3b82f6;
            --brand-deep: #93c5fd;
            --accent: #f59e0b;
            --success-soft: rgba(34, 197, 94, 0.15);
            --warning-soft: rgba(245, 158, 11, 0.15);
            --surface-card-bg: rgba(30, 41, 59, 0.96);
            --preview-card-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            --contract-sheet-bg: #1e293b;
            --chip-bg: rgba(59, 130, 246, 0.2);
            --chip-color: #dbeafe;
            --hero-metric-bg: rgba(255, 255, 255, 0.08);
            --hero-metric-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }

        [data-theme="light"] body {
            background:
                radial-gradient(circle at top left, rgba(20, 71, 230, 0.14), transparent 28%),
                radial-gradient(circle at top right, rgba(199, 125, 43, 0.14), transparent 24%),
                linear-gradient(180deg, #f8f6f0 0%, #f1efe7 100%);
        }

        [data-theme="dark"] body {
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.1), transparent 28%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.08), transparent 24%),
                linear-gradient(180deg, #020617 0%, #0f172a 50%, #1e293b 100%);
        }

        .nav-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #f8fafc;
        }

        .hero-card,
        .surface-card,
        .preview-card {
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .hero-card {
            background: linear-gradient(135deg, #11224f 0%, #173ca8 52%, #2358ff 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -60px -90px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .hero-metric {
            background: var(--hero-metric-bg);
            border: 1px solid var(--hero-metric-border);
            border-radius: 22px;
            backdrop-filter: blur(12px);
        }

        .surface-card {
            background: var(--surface-card-bg);
        }

        .preview-card {
            background: var(--preview-card-bg);
            position: sticky;
            top: 96px;
        }

        .template-card,
        .section-toggle,
        .timeline-item,
        .insight-card {
            border: 1px solid var(--line);
            border-radius: 22px;
            background: var(--bg-paper);
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        [data-theme="dark"] .template-card,
        [data-theme="dark"] .section-toggle,
        [data-theme="dark"] .timeline-item,
        [data-theme="dark"] .insight-card {
            background: #1e293b;
        }

        .template-card:hover,
        .section-toggle:hover {
            transform: translateY(-2px);
            border-color: rgba(20, 71, 230, 0.22);
            box-shadow: 0 12px 30px rgba(20, 71, 230, 0.08);
        }

        .template-card.active,
        .section-toggle.active {
            border-color: rgba(20, 71, 230, 0.38);
            box-shadow: 0 14px 34px rgba(20, 71, 230, 0.14);
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 0.95rem;
            background: var(--chip-bg);
            color: var(--chip-color);
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .contract-sheet {
            background: var(--contract-sheet-bg);
            border: 1px solid rgba(31, 41, 55, 0.08);
            border-radius: 24px;
            min-height: 720px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .contract-sheet pre {
            font-family: "Times New Roman", serif;
            font-size: 1rem;
            line-height: 1.7;
            white-space: pre-wrap;
            word-break: break-word;
            margin: 0;
            color: var(--ink);
        }

        .muted {
            color: var(--muted);
        }

        .form-control,
        .form-select {
            border-radius: 16px;
            border-color: rgba(31, 41, 55, 0.12);
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
            color: var(--ink);
            background-color: var(--bg-paper);
        }

        .theme-toggle {
            position: relative;
            width: 56px;
            height: 30px;
            border-radius: 15px;
            background: #334155;
            cursor: pointer;
            border: none;
            padding: 0;
        }

        .theme-toggle::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #f8fafc;
            transition: transform 0.3s ease;
        }

        .theme-toggle.active::before {
            transform: translateX(26px);
        }

        .theme-toggle-icons {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 6px;
            position: relative;
            z-index: 1;
        }

        .theme-toggle-icons svg {
            width: 14px;
            height: 14px;
        }

        .notification-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            z-index: 1000;
            overflow: hidden;
        }

        .loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.6s linear infinite;
        }
        /* Профиль: аватары */
        .profile-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1447e6, #3b82f6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1447e6, #3b82f6);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        /* Профиль: выпадающее меню */
        .profile-dropdown {
            background: var(--surface-card-bg);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1rem;
            color: var(--ink);
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.2s ease;
        }

        .profile-dropdown-item:hover {
            background: var(--bg-soft);
        }

        .profile-dropdown-item svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        /* Анимация стрелки профиля */
        #profileButton[aria-expanded="true"] #profileArrow {
            transform: rotate(180deg);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 991.98px) {
            .preview-card {
                position: static;
            }
        }
    </style>
</head>
<body>
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
@php
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

            'scope' => [
                'label' => 'Предмет договора',
                'description' => 'Описание предмета и обязательств сторон',
            ],

            'payment' => [
                'label' => 'Стоимость и порядок оплаты',
                'description' => 'Финансовые условия договора',
            ],

            'liability' => [
                'label' => 'Ответственность сторон',
                'description' => 'Ответственность за нарушение обязательств',
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
        ],

        'draft' => [

            'headerParties' => ':customer, именуемое в дальнейшем «:roleA», в лице уполномоченного представителя, действующего на основании учредительных документов, с одной стороны, и :contractor, именуемое в дальнейшем «:roleB», в лице уполномоченного представителя, действующего на основании учредительных документов, с другой стороны, совместно именуемые «Стороны», заключили настоящий договор о нижеследующем:',

            'scope' => '1. ПРЕДМЕТ ДОГОВОРА

1.1. :roleB обязуется по заданию :roleA выполнить :subject, а :roleA обязуется принять результат выполненных работ и оплатить его в порядке и на условиях, предусмотренных настоящим договором.

1.2. Объём, содержание, сроки выполнения работ и иные условия могут определяться дополнительными соглашениями или техническими заданиями, являющимися неотъемлемой частью настоящего договора.

1.3. :roleB гарантирует надлежащее качество выполняемых работ и соответствие результата требованиям :roleA.',

            'payment' => '2. СТОИМОСТЬ И ПОРЯДОК ОПЛАТЫ
2.1. Стоимость работ по настоящему договору составляет :amount.
2.2. Оплата производится в безналичной форме путём перечисления денежных средств на расчётный счёт :roleB.

2.3. :roleA обязуется произвести оплату в течение :days рабочих дней с момента подписания акта выполненных работ.

2.4. Обязательства по оплате считаются исполненными с момента поступления денежных средств на расчётный счёт :roleB.',

            'liability' => '3. ОТВЕТСТВЕННОСТЬ СТОРОН

3.1. За неисполнение либо ненадлежащее исполнение обязательств по настоящему договору Стороны несут ответственность в соответствии с действующим законодательством.

3.2. :penalty

3.3. Сторона, нарушившая обязательства по настоящему договору, обязана возместить другой Стороне причинённые убытки в полном объёме.

3.4. Уплата неустойки не освобождает Стороны от исполнения обязательств по настоящему договору.',

            'termination' => '4. СРОК ДЕЙСТВИЯ ДОГОВОРА

4.1. Настоящий договор вступает в силу с :startDate и действует до :endDate.

4.2. Настоящий договор может быть расторгнут:
— по взаимному соглашению Сторон;
— по инициативе одной из Сторон в случаях, предусмотренных законодательством;
— при существенном нарушении условий договора одной из Сторон.

4.3. Расторжение договора не освобождает Стороны от исполнения обязательств, возникших до даты его прекращения.',

            'confidentiality' => '5. КОНФИДЕНЦИАЛЬНОСТЬ

5.1. Стороны обязуются не разглашать сведения и информацию, полученные в ходе исполнения настоящего договора, третьим лицам без предварительного письменного согласия другой Стороны.

5.2. Конфиденциальной признаётся любая информация, переданная Сторонами в письменной, электронной либо иной форме.

5.3. Обязательства по соблюдению конфиденциальности сохраняют силу в течение 3 (трёх) лет после прекращения действия настоящего договора.',

            'disputes' => '6. РАЗРЕШЕНИЕ СПОРОВ

6.1. Все споры и разногласия, возникающие между Сторонами в связи с исполнением настоящего договора, подлежат урегулированию путём переговоров.

6.2. В случае невозможности достижения соглашения спор подлежит рассмотрению в судебном порядке в соответствии с :law.

6.3. До обращения в суд Стороны обязуются соблюдать претензионный порядок урегулирования споров.',

            'specialTerms' => '7. ОСОБЫЕ УСЛОВИЯ
7.1. :terms

7.2. Во всём остальном, что не урегулировано настоящим договором, Стороны руководствуются :law.

7.3. Все приложения и дополнительные соглашения к настоящему договору являются его неотъемлемой частью.',

            'signatures' => '8. АДРЕСА, РЕКВИЗИТЫ И ПОДПИСИ СТОРОН

:roleA:
:customer
Адрес: ____________________

Подпись: ____________________

:roleB:
:contractor

Адрес: ____________________

Подпись: ____________________',
        ],

        'timeline' => [
            'template' => 'Шаблон: :value',
            'parties' => 'Стороны: :value',
            'term' => 'Срок действия: :value',
            'sections' => 'Разделы: :value',
        ],

    ];
@endphp
<nav role="navigation" aria-label="{{ __('ui.main_navigation') }}" class="fixed top-0 left-0 right-0 z-50 bg-slate-900/95 backdrop-blur-lg border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ route('welcome') }}" class="flex items-center space-x-3 no-underline">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold text-white">LegalAI Pro</span>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="nav-link">{{ __('ui.nav_home') }}</a>
                <a href="{{ route('tasks.create') }}" class="nav-link">{{ __('ui.nav_features') }}</a>
                <a href="{{ route('tasks.calc') }}" class="nav-link">{{ __('ui.nav_calculator') }}</a>
                <a href="{{ route('tasks.chat') }}" class="nav-link">{{ __('ui.nav_ai_chat') }}</a>
                <x-language-switcher />

                <button class="theme-toggle" id="themeToggle" aria-label="{{ __('ui.theme_toggle') }}" title="{{ __('ui.theme_toggle') }}">
                    <div class="theme-toggle-icons">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 3V4m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </div>
                </button>

                <div class="relative">
                    <button id="notificationsButton" class="text-slate-400 hover:text-white transition relative p-2" aria-label="{{ __('ui.notifications') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-[10px] flex items-center justify-center text-white">3</span>
                    </button>
                    <div id="notificationPanel" class="notification-panel hidden">
                        <div class="p-4 border-b border-slate-700">
                            <h4 class="font-semibold text-white">{{ __('ui.notifications') }}</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-slate-700">
                                <p class="text-sm text-white">{{ __('ui.notification_analysis_done') }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ __('ui.minutes_ago_2') }}</p>
                            </div>
                            <div class="p-4 border-b border-slate-700">
                                <p class="text-sm text-white">{{ __('ui.notification_high_risk') }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ __('ui.minutes_ago_15') }}</p>
                            </div>
                            <div class="p-4">
                                <p class="text-sm text-white">{{ __('ui.notification_expiring') }}</p>
                                <p class="text-xs text-slate-400 mt-1">{{ __('ui.hour_ago_1') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопка профиля -->
                <div class="relative" id="profileContainer">
                    <button onclick="toggleProfile()" class="flex items-center space-x-2 hover:bg-slate-800/50 rounded-lg px-2 py-1.5 transition" id="profileButton" aria-label="{{ __('ui.profile') }}">
                        <div class="profile-avatar-sm">{{ $userInitials }}</div>
                        <div class="hidden lg:block text-left">
                            <div class="text-sm font-medium text-white leading-tight">{{ $userName }}</div>
                            <div class="text-[11px] text-slate-400">{{ $userEmail }}</div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 ml-1 transition-transform duration-200" id="profileArrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Выпадающее меню профиля -->
                    <div id="profileDropdown" class="hidden profile-dropdown absolute right-0 mt-2 w-72 z-50">
                        <div class="p-4 border-b" style="border-color: var(--line); background: var(--surface-card-bg);">
                            <div class="flex items-center space-x-3">
                                <div class="profile-avatar">{{ $userInitials }}</div>
                                <div>
                                    <div class="text-sm font-semibold" style="color: var(--ink);">{{ $userName }}</div>
                                    <div class="profile-email-badge" style="color: var(--muted); font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $userEmail }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="py-2" style="background: var(--surface-card-bg);">
                            <a href="{{ route('profile.edit') }}" class="profile-dropdown-item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('ui.profile') }}
                            </a>
                            <a href="#" class="profile-dropdown-item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('ui.settings') }}
                            </a>
                            <a href="#" class="profile-dropdown-item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ __('ui.my_documents') }}
                            </a>
                        </div>
                        <div class="border-t py-2" style="border-color: var(--line); background: var(--surface-card-bg);">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="profile-dropdown-item text-red-400 hover:text-red-300 w-full text-left">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('ui.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>            </div>

            <button id="mobileMenuButton" class="md:hidden text-slate-400 hover:text-white p-2" aria-label="{{ __('ui.menu') }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-700 bg-slate-900/95 backdrop-blur-lg">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ route('welcome') }}" class="block py-2 text-slate-300 hover:text-white transition no-underline">{{ __('ui.nav_home') }}</a>
            <a href="{{ route('tasks.create') }}" class="block py-2 text-slate-300 hover:text-white transition no-underline">{{ __('ui.nav_features') }}</a>
            <a href="{{ route('tasks.calc') }}" class="block py-2 text-slate-300 hover:text-white transition no-underline">{{ __('ui.nav_calculator') }}</a>
            <a href="{{ route('tasks.chat') }}" class="block py-2 text-slate-300 hover:text-white transition no-underline">{{ __('ui.nav_ai_chat') }}</a>
            <button id="commandPaletteButton" class="w-full text-left py-2 text-slate-300 hover:text-white transition">🔍 {{ __('ui.search_commands') }}</button>
            <div class="flex items-center space-x-3 py-2">
                <span class="text-slate-300 text-sm">{{ __('ui.theme') }}:</span>
                <button class="theme-toggle" id="themeToggleMobile" aria-label="{{ __('ui.theme_toggle') }}">
                    <div class="theme-toggle-icons">
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 3V4m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <svg fill="currentColor" viewBox="0 0 24 24"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </div>
                </button>
            </div>

        </div>
    </div>
</nav>
<br><br>
<main class="py-4 py-lg-5" style="padding-top: 5.5rem;">
    <div class="container">
        <section class="hero-card p-4 p-lg-5 mb-4 mb-lg-5">
            <div class="row g-4 align-items-center position-relative">
                <div class="col-lg-8">
                    <span class="badge rounded-pill text-bg-light text-primary px-3 py-2">Dashboard / {{ __('ui.dashboard_builder_badge') }}</span>
                    <h1 class="display-5 fw-bold mt-4 mb-3">{{ __('ui.dashboard_builder_title') }}</h1>
                    <p class="text-white-50 fs-5 mb-4">{{ __('ui.dashboard_builder_text') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="chip">{{ __('ui.dashboard_chip_templates') }}</span>
                        <span class="chip">{{ __('ui.dashboard_chip_preview') }}</span>
                        <span class="chip">{{ __('ui.dashboard_chip_export') }}</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="hero-metric p-3 h-100">
                                <div class="text-white-50 small">{{ __('ui.dashboard_active_template') }}</div>
                                <div class="fs-5 fw-bold mt-2" id="heroTemplateLabel">{{ __('ui.template_service') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-metric p-3 h-100">
                                <div class="text-white-50 small">{{ __('ui.dashboard_sections_enabled') }}</div>
                                <div class="fs-4 fw-bold mt-2" id="heroSectionCount">4</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-metric p-3 h-100">
                                <div class="text-white-50 small">{{ __('ui.dashboard_contract_sum') }}</div>
                                <div class="fs-5 fw-bold mt-2" id="heroAmount">120 000 {{ __('ui.rub_currency_symbol') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="hero-metric p-3 h-100">
                                <div class="text-white-50 small">{{ __('ui.dashboard_start_date') }}</div>
                                <div class="fs-5 fw-bold mt-2" id="heroStartDate">15.04.2026</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="surface-card p-4 p-lg-5 mb-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                        <div>
                            <div class="text-uppercase text-primary small fw-bold">{{ __('ui.dashboard_step1') }}</div>
                            <h2 class="h3 mt-2 mb-1">{{ __('ui.dashboard_choose_template') }}</h2>
                            <p class="muted mb-0">{{ __('ui.dashboard_choose_template_text') }}</p>
                        </div>
                        <div class="badge text-bg-light px-3 py-2">{{ __('ui.dashboard_3_scenarios') }}</div>
                    </div>

                    <div class="row g-3" id="templateCards">
                        @foreach (['service', 'supply', 'nda'] as $template)
                            <div class="col-md-4">
                                <button type="button" class="template-card w-100 text-start p-4 {{ $template === 'service' ? 'active' : '' }}" data-template="{{ $template }}">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            <div class="fw-bold">{{ $builderTranslations['templates'][$template]['label'] }}</div>
                                            <div class="small muted mt-2">{{ $builderTranslations['templates'][$template]['description'] }}</div>
                                        </div>
                                        <span class="badge {{ $template === 'service' ? 'text-bg-primary' : ($template === 'supply' ? 'text-bg-light' : 'text-bg-warning') }}">
                                            {{ $builderTranslations['templates'][$template]['badge'] }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="surface-card p-4 p-lg-5 mb-4">
                    <div class="text-uppercase text-primary small fw-bold">{{ __('ui.dashboard_step2') }}</div>
                    <h2 class="h3 mt-2 mb-1">{{ __('ui.dashboard_fill_params') }}</h2>
                    <p class="muted mb-4">{{ __('ui.dashboard_fill_params_text') }}</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contractNumber" class="form-label fw-semibold">{{ __('ui.contract_number') }}</label>
                            <input id="contractNumber" type="text" class="form-control" value="LA-042/26">
                        </div>
                        <div class="col-md-6">
                            <label for="contractCity" class="form-label fw-semibold">{{ __('ui.city_of_signing') }}</label>
                            <input id="contractCity" type="text" class="form-control" value="Алматы">
                        </div>
                        <div class="col-md-6">
                            <label for="startDate" class="form-label fw-semibold">{{ __('ui.start_date') }}</label>
                            <input id="startDate" type="date" class="form-control" value="2026-04-15">
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label fw-semibold">{{ __('ui.end_date') }}</label>
                            <input id="endDate" type="date" class="form-control" value="2026-12-31">
                        </div>
                        <div class="col-md-6">
                            <label for="customerName" class="form-label fw-semibold">{{ __('ui.customer_buyer') }}</label>
                            <input id="customerName" type="text" class="form-control" value="TOO LegalAI Client">
                        </div>
                        <div class="col-md-6">
                            <label for="contractorName" class="form-label fw-semibold">{{ __('ui.contractor_supplier') }}</label>
                            <input id="contractorName" type="text" class="form-control" value="TOO LegalAI Pro">
                        </div>
                        <div class="col-md-6">
                            <label for="contractSubject" class="form-label fw-semibold">{{ __('ui.contract_subject') }}</label>
                            <input id="contractSubject" type="text" class="form-control" value="{{ __('ui.service_default_subject') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="contractAmount" class="form-label fw-semibold">{{ __('ui.amount') }}</label>
                            <input id="contractAmount" type="number" min="0" step="1000" class="form-control" value="120000">
                        </div>
                        <div class="col-md-3">
                            <label for="paymentDays" class="form-label fw-semibold">{{ __('ui.payment_days') }}</label>
                            <input id="paymentDays" type="number" min="1" class="form-control" value="5">
                        </div>
                        <div class="col-md-6">
                            <label for="governingLaw" class="form-label fw-semibold">{{ __('ui.governing_law') }}</label>
                            <input id="governingLaw" type="text" class="form-control" value="Право Республики Казахстан">
                        </div>
                        <div class="col-md-6">
                            <label for="penaltyRule" class="form-label fw-semibold">{{ __('ui.penalty') }}</label>
                            <input id="penaltyRule" type="text" class="form-control" value="{{ __('ui.default_penalty_rule') }}">
                        </div>
                        <div class="col-12">
                            <label for="specialTerms" class="form-label fw-semibold">{{ __('ui.special_terms') }}</label>
                            <textarea id="specialTerms" class="form-control" rows="4">Исполнитель предоставляет ежемесячный отчёт о выполненных работах и фиксирует замечания заказчика в течение 2 рабочих дней.</textarea>
                        </div>
                    </div>
                </div>

                <div class="surface-card p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                        <div>
                            <div class="text-uppercase text-primary small fw-bold">{{ __('ui.dashboard_step3') }}</div>
                            <h2 class="h3 mt-2 mb-1">{{ __('ui.dashboard_enable_sections') }}</h2>
                            <p class="muted mb-0">{{ __('ui.dashboard_enable_sections_text') }}</p>
                        </div>
                        <div class="small muted">{{ __('ui.dashboard_sections_note') }}</div>
                    </div>

                    <div class="row g-3">
                        @foreach (['scope', 'payment', 'liability', 'termination', 'confidentiality', 'disputes'] as $section)
                            <div class="col-md-6">
                                <label class="section-toggle d-block p-4 {{ in_array($section, ['scope', 'payment', 'liability', 'termination'], true) ? 'active' : '' }}">
                                    <div class="form-check">
                                        <input class="form-check-input builder-section" type="checkbox" value="{{ $section }}" {{ in_array($section, ['scope', 'payment', 'liability', 'termination'], true) ? 'checked' : '' }}>
                                        <span class="form-check-label fw-bold">{{ $builderTranslations['sections'][$section]['label'] }}</span>
                                    </div>
                                    <div class="small muted mt-2">{{ $builderTranslations['sections'][$section]['description'] }}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4" id="activeSectionBadges"></div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="preview-card p-4 p-lg-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <div class="text-uppercase text-primary small fw-bold">{{ __('ui.live_preview') }}</div>
                            <h2 class="h3 mt-2 mb-1">{{ __('ui.draft_contract') }}</h2>
                            <p class="muted mb-0">{{ __('ui.draft_contract_text') }}</p>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="copyContractButton">{{ __('ui.copy') }}</button>
                            <div class="dropdown">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="downloadDropdownButton">
                                    {{ __('ui.download') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="downloadDropdownButton">
                                    <li><button class="dropdown-item" id="downloadTxtButton" type="button">{{ __('ui.txt_document') }}</button></li>
                                    <li><button class="dropdown-item" id="downloadPdfButton" type="button">{{ __('ui.pdf_document') }}</button></li>
                                    <li><button class="dropdown-item" id="downloadDocxButton" type="button">{{ __('ui.word_document') }}</button></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="contract-sheet p-4 p-lg-5">
                        <pre id="contractPreview"></pre>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-sm-6">
                            <div class="insight-card p-4 h-100" style="background: var(--success-soft);">
                                <div class="small text-success-emphasis">{{ __('ui.build_status') }}</div>
                                <div class="fw-bold fs-5 mt-2" id="previewStatus">{{ __('ui.draft_ready') }}</div>
                                <div class="small muted mt-2">{{ __('ui.build_status_text') }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="insight-card p-4 h-100" style="background: var(--warning-soft);">
                                <div class="small text-warning-emphasis">{{ __('ui.reminder') }}</div>
                                <div class="fw-bold fs-5 mt-2">{{ __('ui.review_required') }}</div>
                                <div class="small muted mt-2">{{ __('ui.reminder_text') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-uppercase text-primary small fw-bold mb-3">{{ __('ui.build_plan') }}</div>
                        <div class="d-grid gap-2" id="timeline"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('downloadDocxButton').addEventListener('click', async () => {

        try {

            const contractText = document.getElementById('contractPreview').innerText;

            const doc = new docx.Document({
                sections: [
                    {
                        properties: {},
                        children: [
                            new docx.Paragraph({
                                text: contractText,
                            }),
                        ],
                    },
                ],
            });

            const blob = await docx.Packer.toBlob(doc);

            const link = document.createElement('a');

            link.href = URL.createObjectURL(blob);

            link.download = 'contract.docx';

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

        } catch (error) {

            console.error(error);

            alert('Ошибка DOCX');

        }

    });
    // Переключение профиля
    let profileOpen = false;
    function toggleProfile() {
        const dropdown = document.getElementById('profileDropdown');
        const button = document.getElementById('profileButton');
        const arrow = document.getElementById('profileArrow');

        profileOpen = !profileOpen;
        dropdown.classList.toggle('hidden', !profileOpen);
        button?.setAttribute('aria-expanded', profileOpen);

        // Закрыть другие открытые панели
        if (profileOpen) {
            notificationPanel?.classList.add('hidden');
        }
    }

    // Закрытие профиля при клике вне
    document.addEventListener('click', (event) => {
        const profileContainer = document.getElementById('profileContainer');
        const dropdown = document.getElementById('profileDropdown');

        if (profileOpen && !profileContainer?.contains(event.target)) {
            profileOpen = false;
            dropdown?.classList.add('hidden');
            document.getElementById('profileButton')?.setAttribute('aria-expanded', 'false');
        }

        // Закрытие уведомлений
        if (!notificationPanel.contains(event.target) && !event.target.closest('#notificationsButton')) {
            notificationPanel.classList.add('hidden');
        }
    });
    const translations = @json($builderTranslations);
    const locale = @json(app()->getLocale());

    const notificationPanel = document.getElementById('notificationPanel');


    function replaceTokens(template, replacements = {}) {
        return Object.entries(replacements).reduce((result, [key, value]) => {
            return result.replaceAll(`:${key}`, value);
        }, template);
    }

    (function initTheme() {
        const savedTheme = localStorage.getItem('legalai-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', initialTheme);
        syncToggleButtons(initialTheme === 'dark');
    })();

    function syncToggleButtons(isDark) {
        document.getElementById('themeToggle')?.classList.toggle('active', isDark);
        document.getElementById('themeToggleMobile')?.classList.toggle('active', isDark);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', nextTheme);
        localStorage.setItem('legalai-theme', nextTheme);
        syncToggleButtons(nextTheme === 'dark');
    }

    document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
    document.getElementById('themeToggleMobile')?.addEventListener('click', toggleTheme);

    document.getElementById('notificationsButton')?.addEventListener('click', () => {
        notificationPanel.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!notificationPanel.contains(event.target) && !event.target.closest('#notificationsButton')) {
            notificationPanel.classList.add('hidden');
        }
    });

    const mobileMenu = document.getElementById('mobileMenu');
    document.getElementById('mobileMenuButton')?.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    document.getElementById('commandPaletteButton')?.addEventListener('click', () => {
        alert(translations.ui.commandPaletteInDevelopment);
    });

    const templateConfig = translations.templates;
    const sectionConfig = translations.sections;
    const state = { template: 'service' };

    const fields = {
        contractNumber: document.getElementById('contractNumber'),
        contractCity: document.getElementById('contractCity'),
        startDate: document.getElementById('startDate'),
        endDate: document.getElementById('endDate'),
        customerName: document.getElementById('customerName'),
        contractorName: document.getElementById('contractorName'),
        contractSubject: document.getElementById('contractSubject'),
        contractAmount: document.getElementById('contractAmount'),
        paymentDays: document.getElementById('paymentDays'),
        governingLaw: document.getElementById('governingLaw'),
        penaltyRule: document.getElementById('penaltyRule'),
        specialTerms: document.getElementById('specialTerms'),
    };

    const previewElement = document.getElementById('contractPreview');
    const previewStatus = document.getElementById('previewStatus');
    const sectionInputs = Array.from(document.querySelectorAll('.builder-section'));
    const templateCards = Array.from(document.querySelectorAll('[data-template]'));

    function syncTemplateSelection() {
        templateCards.forEach((card) => {
            card.classList.toggle('active', card.dataset.template === state.template);
        });
    }

    function getSelectedSections() {
        return sectionInputs.filter((input) => input.checked).map((input) => input.value);
    }

    function formatAmount(value) {
        const numericValue = Number(value || 0);
        return `${new Intl.NumberFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(numericValue)} ${translations.ui.rubCurrencySymbol}`;
    }

    function formatDate(value) {
        if (!value) {
            return translations.ui.notSpecified;
        }

        try {
            return new Intl.DateTimeFormat(locale === 'en' ? 'en-US' : 'ru-RU').format(new Date(value));
        } catch {
            return translations.ui.notSpecified;
        }
    }

    function buildSectionText(template, sections, values) {
        const blocks = [];

        if (sections.includes('scope')) {
            blocks.push(replaceTokens(translations.draft.scope, {
                roleB: template.roleB,
                subject: values.subject,
                roleALower: template.roleA.toLowerCase(),
                roleA: template.roleA,
            }));
        }

        if (sections.includes('payment')) {
            blocks.push(replaceTokens(translations.draft.payment, {
                amount: formatAmount(values.amount),
                roleA: template.roleA,
                days: values.paymentDays,
                roleBLower: template.roleB.toLowerCase(),
            }));
        }

        if (sections.includes('liability')) {
            blocks.push(replaceTokens(translations.draft.liability, {
                penalty: values.penaltyRule,
            }));
        }

        if (sections.includes('termination')) {
            blocks.push(replaceTokens(translations.draft.termination, {
                startDate: formatDate(values.startDate),
                endDate: formatDate(values.endDate),
            }));
        }

        if (sections.includes('confidentiality')) {
            blocks.push(translations.draft.confidentiality);
        }

        if (sections.includes('disputes')) {
            blocks.push(replaceTokens(translations.draft.disputes, {
                law: values.governingLaw,
            }));
        }

        blocks.push(replaceTokens(translations.draft.specialTerms, {
            terms: values.specialTerms,
            law: values.governingLaw,
        }));

        blocks.push(replaceTokens(translations.draft.signatures, {
            roleA: template.roleA,
            customer: values.customerName,
            roleB: template.roleB,
            contractor: values.contractorName,
        }));

        return blocks.join('\n\n');
    }
    function buildContractText() {
        const template = templateConfig[state.template];
        const values = {
            number: fields.contractNumber.value.trim() || translations.ui.withoutNumber,
            city: fields.contractCity.value.trim() || translations.ui.notSpecified,
            startDate: fields.startDate.value,
            endDate: fields.endDate.value,
            customerName: fields.customerName.value.trim() || translations.ui.party1,
            contractorName: fields.contractorName.value.trim() || translations.ui.party2,
            subject: fields.contractSubject.value.trim() || template.defaultSubject,
            amount: fields.contractAmount.value,
            paymentDays: fields.paymentDays.value || '5',
            governingLaw: fields.governingLaw.value.trim() || translations.ui.applicableLaw,
            penaltyRule: fields.penaltyRule.value.trim() || translations.ui.defaultPenaltyRule,
            specialTerms: fields.specialTerms.value.trim() || translations.ui.defaultSpecialTerms,
        };

        const header = `${template.label} № ${values.number}\nг. ${values.city}\n${formatDate(values.startDate)}\n\n${replaceTokens(translations.draft.headerParties, {
            customer: values.customerName,
            roleA: template.roleA,
            contractor: values.contractorName,
            roleB: template.roleB,
        })}`;

        return `${header}\n\n${buildSectionText(template, getSelectedSections(), values)}`;
    }

    function renderBadges(selectedSections) {
        const container = document.getElementById('activeSectionBadges');
        container.innerHTML = '';

        selectedSections.forEach((section) => {
            const badge = document.createElement('span');
            badge.className = 'badge rounded-pill text-bg-light border px-3 py-2';
            badge.textContent = sectionConfig[section].label;
            container.appendChild(badge);
        });
    }

    function renderTimeline(selectedSections) {
        const template = templateConfig[state.template];
        const items = [
            replaceTokens(translations.timeline.template, { value: template.label }),
            replaceTokens(translations.timeline.parties, {
                value: `${fields.customerName.value.trim() || translations.ui.party1} / ${fields.contractorName.value.trim() || translations.ui.party2}`,
            }),
            replaceTokens(translations.timeline.term, {
                value: `${formatDate(fields.startDate.value)} - ${formatDate(fields.endDate.value)}`,
            }),
            replaceTokens(translations.timeline.sections, {
                value: selectedSections.map((section) => sectionConfig[section].label).join(', ') || translations.ui.notSelected,
            }),
        ];

        const timeline = document.getElementById('timeline');
        timeline.innerHTML = '';

        items.forEach((item) => {
            const div = document.createElement('div');
            div.className = 'timeline-item p-3';
            div.textContent = item;
            timeline.appendChild(div);
        });
    }

    function renderHero(selectedSections) {
        const template = templateConfig[state.template];
        document.getElementById('heroTemplateLabel').textContent = template.label;
        document.getElementById('heroSectionCount').textContent = String(selectedSections.length);
        document.getElementById('heroAmount').textContent = formatAmount(fields.contractAmount.value);
        document.getElementById('heroStartDate').textContent = formatDate(fields.startDate.value);
    }

    function renderBuilder() {
        const selectedSections = getSelectedSections();
        previewElement.textContent = buildContractText();
        renderBadges(selectedSections);
        renderTimeline(selectedSections);
        renderHero(selectedSections);
    }

    templateCards.forEach((card) => {
        card.addEventListener('click', () => {
            state.template = card.dataset.template;
            fields.contractSubject.value = templateConfig[state.template].defaultSubject;
            syncTemplateSelection();
            renderBuilder();
        });
    });

    Object.values(fields).forEach((field) => {
        field.addEventListener('input', renderBuilder);
        field.addEventListener('change', renderBuilder);
    });

    sectionInputs.forEach((input) => {
        input.addEventListener('change', () => {
            input.closest('.section-toggle').classList.toggle('active', input.checked);
            renderBuilder();
        });
    });

    document.getElementById('copyContractButton')?.addEventListener('click', async () => {
        if (!navigator.clipboard) {
            return;
        }

        await navigator.clipboard.writeText(previewElement.textContent);
        previewStatus.textContent = translations.ui.copied;
        setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 1800);
    });

    document.getElementById('downloadTxtButton')?.addEventListener('click', () => {
        const blob = new Blob([previewElement.textContent], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = `contract-${fields.contractNumber.value || 'draft'}.txt`;
        anchor.click();
        URL.revokeObjectURL(url);
        previewStatus.textContent = translations.ui.txtDownloaded;
        setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 1800);
    });

    document.getElementById('downloadPdfButton')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const originalHtml = button.innerHTML;
        button.innerHTML = `<span class="loading-spinner"></span> ${translations.ui.generating}`;
        button.disabled = true;

        try {
            await html2pdf().set({
                margin: [10, 10, 10, 10],
                filename: `contract-${fields.contractNumber.value || 'draft'}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
            }).from(document.querySelector('.contract-sheet')).save();

            previewStatus.textContent = translations.ui.pdfDownloaded;
            setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 1800);
        } catch (error) {
            console.error('PDF generation error:', error);
            previewStatus.textContent = translations.ui.pdfError;
            setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 2000);
        } finally {
            button.innerHTML = originalHtml;
            button.disabled = false;
        }
    });

    document.getElementById('downloadDocxButton')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const originalHtml = button.innerHTML;
        button.innerHTML = `<span class="loading-spinner"></span> ${translations.ui.generating}`;
        button.disabled = true;

        try {
            const { Document, Packer, Paragraph, TextRun, HeadingLevel } = docx;
            const lines = previewElement.textContent.split('\n');
            const children = [];

            lines.forEach((line) => {
                if (!line.trim()) {
                    children.push(new Paragraph({ text: '' }));
                    return;
                }

                const isSectionHeader = /^\d+\.?\s/.test(line.trim()) && !/^\d+\.\d+/.test(line.trim());

                children.push(new Paragraph({
                    children: [
                        new TextRun({
                            text: line.trim(),
                            bold: isSectionHeader,
                            size: isSectionHeader ? 28 : 24,
                            font: 'Times New Roman',
                        }),
                    ],
                    heading: isSectionHeader ? HeadingLevel.HEADING_2 : undefined,
                    spacing: { after: 80 },
                }));
            });

            const doc = new Document({
                sections: [{ properties: {}, children }],
            });

            const blob = await Packer.toBlob(doc);
            const url = URL.createObjectURL(blob);
            const anchor = document.createElement('a');
            anchor.href = url;
            anchor.download = `contract-${fields.contractNumber.value || 'draft'}.docx`;
            anchor.click();
            URL.revokeObjectURL(url);

            previewStatus.textContent = translations.ui.docxDownloaded;
            setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 1800);
        } catch (error) {
            console.error('DOCX generation error:', error);
            previewStatus.textContent = translations.ui.docxError;
            setTimeout(() => { previewStatus.textContent = translations.ui.draftReady; }, 2000);
        } finally {
            button.innerHTML = originalHtml;
            button.disabled = false;
        }
    });

    syncTemplateSelection();
    renderBuilder();
</script>
</body>
</html>
