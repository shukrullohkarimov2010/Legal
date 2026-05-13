<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LegalAI Pro - Анализ договора</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0f172a;
            --muted: #475569;
            --line: rgba(148, 163, 184, 0.28);
            --panel: rgba(255, 255, 255, 0.84);
            --teal: #0f766e;
            --teal-soft: rgba(15, 118, 110, 0.08);
            --rose-soft: rgba(225, 29, 72, 0.08);
            --amber-soft: rgba(245, 158, 11, 0.1);
            --emerald-soft: rgba(5, 150, 105, 0.1);
        }

        body {
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 24%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.15), transparent 28%),
                radial-gradient(circle at bottom right, rgba(249, 115, 22, 0.12), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef5f2 100%);
        }

        .glass {
            background: var(--panel);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(18px);
        }

        .soft-grid {
            background-image:
                linear-gradient(rgba(148, 163, 184, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.08) 1px, transparent 1px);
            background-size: 26px 26px;
        }

        .upload-zone {
            border: 2px dashed rgba(15, 118, 110, 0.3);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.78), rgba(240, 253, 250, 0.92)),
                var(--teal-soft);
            transition: 180ms ease;
        }

        .upload-zone:hover,
        .upload-zone.active {
            border-color: rgba(15, 118, 110, 0.8);
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(15, 118, 110, 0.12);
        }

        .metric-card {
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.7);
        }

        .section-card {
            border: 1px solid rgba(148, 163, 184, 0.2);
            background: rgba(255, 255, 255, 0.76);
        }

        .risk-high {
            background: var(--rose-soft);
            border-color: rgba(225, 29, 72, 0.18);
        }

        .risk-medium {
            background: var(--amber-soft);
            border-color: rgba(245, 158, 11, 0.22);
        }

        .risk-low {
            background: var(--emerald-soft);
            border-color: rgba(5, 150, 105, 0.18);
        }
    </style>
</head>
<body class="text-slate-900">
@include('admin.partials.header')

@php
    $report = $report ?? null;
    $riskLabelMap = [
        'Высокий' => 'bg-rose-600 text-white',
        'Средний' => 'bg-amber-500 text-white',
        'Низкий' => 'bg-emerald-600 text-white',
    ];
    $severityMap = [
        'high' => ['title' => 'Высокий', 'wrapper' => 'risk-high', 'badge' => 'bg-rose-600 text-white'],
        'medium' => ['title' => 'Средний', 'wrapper' => 'risk-medium', 'badge' => 'bg-amber-500 text-white'],
        'low' => ['title' => 'Низкий', 'wrapper' => 'risk-low', 'badge' => 'bg-emerald-600 text-white'],
    ];
    $riskWidth = max(0, min(100, (int) ($report['risk_score'] ?? 0)));
@endphp

<main class="px-4 pb-12 pt-24 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="grid gap-6 lg:grid-cols-[1.15fr,0.85fr]">
            <div class="glass soft-grid rounded-[32px] p-8 sm:p-10">
                <div class="inline-flex items-center rounded-full bg-teal-50 px-4 py-2 text-sm font-bold text-teal-700 ring-1 ring-teal-100">
                    LegalAI Pro
                </div>
                <h1 class="mt-6 max-w-4xl text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl">
                    Загрузите договор и получите структурированный отчет по рискам, условиям и ключевым пунктам
                </h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-slate-600">
                    Страница стала чище и полезнее: теперь основной сценарий загрузки заметнее, а результат анализа собран в компактные блоки с рисками, рекомендациями и фрагментом текста документа.
                </p>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-slate-950 px-5 py-5 text-white">
                        <div class="text-sm text-slate-300">Поддержка</div>
                        <div class="mt-2 text-lg font-bold">PDF, DOC, DOCX, TXT</div>
                    </div>
                    <div class="metric-card rounded-3xl px-5 py-5">
                        <div class="text-sm text-slate-500">Режим</div>
                        <div class="mt-2 text-lg font-bold text-slate-950">Серверный анализ</div>
                    </div>
                    <div class="metric-card rounded-3xl px-5 py-5">
                        <div class="text-sm text-slate-500">Результат</div>
                        <div class="mt-2 text-lg font-bold text-slate-950">Сводка, риски и рекомендации</div>
                    </div>
                </div>

                <div class="mt-8 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/70 px-4 py-4 ring-1 ring-slate-200/70">
                        Документы в текстовых форматах анализируются по содержимому.
                    </div>
                    <div class="rounded-2xl bg-white/70 px-4 py-4 ring-1 ring-slate-200/70">
                        Для PDF и DOCX выводится базовый отчет, если текст еще не извлечен.
                    </div>
                </div>
            </div>

            <div class="glass rounded-[32px] p-8">
                <div class="mb-6">
                    <div class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Загрузка</div>
                    <h2 class="mt-2 text-2xl font-extrabold text-slate-950">Новый анализ договора</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Перетащите файл в область ниже или выберите его вручную. Максимальный размер файла: 15 МБ.
                    </p>
                </div>

                <form action="{{ route('tasks.report') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div id="uploadZone" class="upload-zone rounded-[28px] px-6 py-10 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-700 text-3xl font-bold text-white shadow-lg shadow-teal-900/20">
                            +
                        </div>
                        <h3 class="mt-5 text-2xl font-bold text-slate-950">Загрузка договора</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            Поддерживаются форматы `pdf`, `doc`, `docx`, `txt`, `md`, `html`, `json`.
                        </p>

                        <input id="contractFile" name="contract" type="file" class="hidden" accept=".pdf,.doc,.docx,.txt,.md,.html,.json" required>

                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <button id="pickFileBtn" type="button" class="rounded-2xl bg-teal-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-teal-800">
                                Выбрать файл
                            </button>
                            <button type="submit" class="rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Сформировать отчет
                            </button>
                        </div>
                    </div>

                    <div id="selectedFileBox" class="hidden rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Выбранный файл</div>
                                <div id="selectedFileName" class="mt-2 text-lg font-bold text-slate-950"></div>
                            </div>
                            <div id="selectedFileMeta" class="text-sm text-slate-500"></div>
                        </div>
                    </div>

                    @error('contract')
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                            {{ $message }}
                        </div>
                    @enderror
                </form>

                @if ($report)
                    <div class="mt-6 rounded-3xl bg-slate-950 p-5 text-white">
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Последний отчет</div>
                        <div class="mt-2 text-lg font-bold">{{ $report['file']['original_name'] }}</div>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-300">
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $report['file']['extension'] ?: 'FILE' }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $report['file']['size_human'] }}</span>
                            <span class="rounded-full bg-white/10 px-3 py-1">{{ $report['analysis_status'] }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if ($report)
            <section class="grid gap-6 lg:grid-cols-[0.92fr,1.08fr]">
                <div class="glass rounded-[32px] p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold uppercase tracking-[0.24em] text-teal-700">Общий итог</div>
                            <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Отчет по договору</h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">
                                Краткая оценка документа, типа договора и уровня потенциальных рисков.
                            </p>
                        </div>
                        <span class="rounded-full px-4 py-2 text-sm font-bold {{ $riskLabelMap[$report['risk_label']] ?? $riskLabelMap['Низкий'] }}">
                            {{ $report['risk_label'] }} риск
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-slate-950 p-5 text-white">
                            <div class="text-sm text-slate-300">Риск документа</div>
                            <div class="mt-2 text-4xl font-extrabold">{{ $report['risk_score'] }}%</div>
                        </div>
                        <div class="metric-card rounded-3xl p-5">
                            <div class="text-sm text-slate-500">Предполагаемый тип</div>
                            <div class="mt-2 text-2xl font-bold text-slate-950">{{ $report['contract_type'] }}</div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="mb-2 flex items-center justify-between text-sm font-medium text-slate-600">
                            <span>Шкала риска</span>
                            <span>{{ $report['risk_label'] }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                            <span class="block h-full rounded-full bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-500" style="width: {{ $riskWidth }}%;"></span>
                        </div>
                    </div>

                    <div class="mt-8 rounded-[28px] bg-white p-6 ring-1 ring-slate-200">
                        <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Резюме</div>
                        <p class="mt-3 text-base leading-8 text-slate-700">{{ $report['summary'] }}</p>
                    </div>
                </div>

                <div class="glass rounded-[32px] p-8">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Метрики</div>
                            <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Ключевые показатели</h3>
                        </div>
                        <div class="rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700">
                            На основе серверного анализа
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="metric-card rounded-3xl p-5">
                            <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Символов</div>
                            <div class="mt-3 text-3xl font-extrabold text-slate-950">{{ number_format($report['char_count'], 0, '.', ' ') }}</div>
                        </div>
                        <div class="metric-card rounded-3xl p-5">
                            <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Слов</div>
                            <div class="mt-3 text-3xl font-extrabold text-slate-950">{{ number_format($report['word_count'], 0, '.', ' ') }}</div>
                        </div>
                        <div class="metric-card rounded-3xl p-5">
                            <div class="text-xs uppercase tracking-[0.18em] text-slate-400">Проблемных зон</div>
                            <div class="mt-3 text-3xl font-extrabold text-slate-950">{{ $report['issues_count'] }}</div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-3xl bg-slate-950 p-6 text-white">
                            <div class="text-sm uppercase tracking-[0.18em] text-slate-300">Статус проверки</div>
                            <div class="mt-3 text-xl font-bold">{{ $report['analysis_status'] }}</div>
                            <p class="mt-2 text-sm leading-6 text-slate-300">{{ $report['analysis_note'] }}</p>
                        </div>
                        <div class="rounded-3xl bg-white p-6 ring-1 ring-slate-200">
                            <div class="text-sm uppercase tracking-[0.18em] text-slate-400">Файл</div>
                            <div class="mt-3 text-lg font-bold text-slate-950">{{ $report['file']['original_name'] }}</div>
                            <div class="mt-3 text-sm leading-6 text-slate-500">
                                MIME: {{ $report['file']['mime_type'] }}<br>
                                Путь: {{ $report['file']['stored_path'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="glass rounded-[32px] p-8">
                    <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Риски</div>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Найденные проблемные пункты</h3>
                    <div class="mt-6 space-y-4">
                        @foreach ($report['risks'] as $index => $risk)
                            @php
                                $palette = $severityMap[$risk['severity']] ?? $severityMap['low'];
                            @endphp
                            <article class="section-card {{ $palette['wrapper'] }} rounded-[26px] p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">
                                        Риск {{ $index + 1 }}
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] {{ $palette['badge'] }}">
                                        {{ $palette['title'] }}
                                    </span>
                                </div>
                                <p class="mt-3 text-sm leading-7 text-slate-700">{{ $risk['label'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="glass rounded-[32px] p-8">
                    <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Рекомендации</div>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Что улучшить</h3>
                    <div class="mt-6 space-y-4">
                        @foreach ($report['recommendations'] as $index => $recommendation)
                            <article class="section-card rounded-[26px] p-5">
                                <div class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Шаг {{ $index + 1 }}</div>
                                <p class="mt-3 text-sm leading-7 text-slate-700">{{ $recommendation }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="glass rounded-[32px] p-8">
                    <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Обзор</div>
                    <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Ключевые данные</h3>
                    <dl class="mt-6 space-y-4">
                        @foreach ($report['facts'] as $fact)
                            <div class="section-card rounded-[26px] p-4">
                                <dt class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $fact['label'] }}</dt>
                                <dd class="mt-2 text-sm font-semibold leading-6 text-slate-900">{{ $fact['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </section>

            <section class="glass rounded-[32px] p-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Текст документа</div>
                        <h3 class="mt-2 text-2xl font-extrabold text-slate-950">Извлеченный фрагмент</h3>
                    </div>
                    <div class="rounded-full px-4 py-2 text-sm font-semibold {{ $report['has_text_preview'] ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $report['has_text_preview'] ? 'Текст извлечен' : 'Нужен парсер для полного текста' }}
                    </div>
                </div>

                <div class="mt-6 rounded-[28px] bg-slate-950 p-6 text-sm leading-7 text-slate-200">
                    {{ $report['preview_text'] }}
                </div>
            </section>
        @endif
    </div>
</main>

<script>
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('contractFile');
    const pickFileBtn = document.getElementById('pickFileBtn');
    const selectedFileBox = document.getElementById('selectedFileBox');
    const selectedFileName = document.getElementById('selectedFileName');
    const selectedFileMeta = document.getElementById('selectedFileMeta');

    const formatBytes = (bytes) => {
        if (!bytes) {
            return '0 Б';
        }

        const units = ['Б', 'КБ', 'МБ', 'ГБ'];
        const power = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const value = bytes / (1024 ** power);
        const precision = value >= 10 || power === 0 ? 0 : 1;

        return `${value.toFixed(precision)} ${units[power]}`;
    };

    const renderSelectedFile = (file) => {
        if (!file) {
            selectedFileBox.classList.add('hidden');
            selectedFileName.textContent = '';
            selectedFileMeta.textContent = '';
            return;
        }

        selectedFileName.textContent = file.name;
        selectedFileMeta.textContent = `${formatBytes(file.size)}${file.type ? ` • ${file.type}` : ''}`;
        selectedFileBox.classList.remove('hidden');
    };

    pickFileBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        renderSelectedFile(fileInput.files[0] ?? null);
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        uploadZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            uploadZone.classList.add('active');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        uploadZone.addEventListener(eventName, (event) => {
            event.preventDefault();
            uploadZone.classList.remove('active');
        });
    });

    uploadZone.addEventListener('drop', (event) => {
        const files = event.dataTransfer.files;
        if (!files.length) {
            return;
        }

        fileInput.files = files;
        renderSelectedFile(files[0]);
    });
</script>
</body>
</html>
