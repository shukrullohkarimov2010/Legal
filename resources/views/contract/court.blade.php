@php
    $tg = app()->getLocale() === 'tg';
    $t = $tg ? [
        'title' => 'Симулятори суд', 'home' => '← Асосӣ', 'h1a' => 'Симулятори', 'h1b' => 'суд',
        'lead' => 'Зеҳни сунъӣ нақши судя, тарафи муқобил ва шоҳидро бозӣ мекунад, шумо бошед ҳамчун ҳуқуқшинос баромад мекунед. Ба моддаҳо истинод кунед: система ҳар якро аз рӯи базаи қонунҳои ҶТ месанҷад ва кори шуморо баҳо медиҳад.',
        'note' => 'Ин симулятори таълимист ва машварати ҳуқуқиро иваз намекунад. Парвандаҳо тахайюлианд.',
        'pick' => 'Парвандаро интихоб кунед', 'cases' => 'Парвандаҳо', 'back' => '← Ба парвандаҳо', 'score' => 'Баҳо дар вақти воқеӣ',
        'cites' => 'Истинодҳои шумо',
    ] : [
        'title' => 'Симулятор суда', 'home' => '← Главная', 'h1a' => 'Симулятор', 'h1b' => 'суда',
        'lead' => 'ИИ играет судью, противную сторону и свидетеля, а вы выступаете в роли юриста. Ссылайтесь на статьи: система проверит каждую по базе законов РТ и оценит вашу работу.',
        'note' => 'Это учебный симулятор, он не заменяет юридическую консультацию. Дела вымышленные.',
        'pick' => 'Выберите дело', 'cases' => 'Дела', 'back' => '← К делам', 'score' => 'Оценка в реальном времени',
        'cites' => 'Ваши ссылки на статьи',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $tg ? 'tg' : 'ru' }}" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="description" content="{{ $t['lead'] }}">
<title>LegalAI Pro — {{ $t['title'] }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/court.css') }}?v={{ @filemtime(public_path('css/court.css')) }}">
</head>
<body>

<header class="top">
    <div class="wrap">
        <a href="/" class="brand">⚖️ LegalAI <b>PRO</b></a>
        <nav class="nav" aria-label="{{ $t['title'] }}">
            <a href="/">{{ $t['home'] }}</a>
            <div class="seg" role="group" aria-label="Язык / Забон">
                <button type="button" aria-pressed="{{ $tg ? 'false' : 'true' }}" onclick="location.href='{{ route('locale.switch', ['locale' => 'ru']) }}'">RU</button>
                <button type="button" aria-pressed="{{ $tg ? 'true' : 'false' }}" onclick="location.href='{{ route('locale.switch', ['locale' => 'tg']) }}'">ТҶ</button>
            </div>
            <button type="button" id="themeBtn" class="chip-btn" aria-label="Theme">☀️</button>
        </nav>
    </div>
</header>

<main class="wrap">

    <section id="screenCases" class="screen">
        <div class="hero">
            <h1>{{ $t['h1a'] }} <span>{{ $t['h1b'] }}</span></h1>
            <p>{{ $t['lead'] }}</p>
        </div>
        <div id="cases" class="cases" aria-label="{{ $t['cases'] }}"></div>
        <p class="note">{{ $t['note'] }}</p>
    </section>

    <section id="screenRoom" class="screen" hidden>
        <div class="room">
            <div class="stage">
                <div class="stage-head">
                    <button type="button" id="backToCases" class="chip-btn" style="border-color: var(--line-2)">{{ $t['back'] }}</button>
                    <div class="grow">
                        <h2 id="caseTitle"></h2>
                        <small id="caseRole"></small>
                    </div>
                </div>
                <details class="brief">
                    <summary id="briefSummary"></summary>
                    <p id="briefText" style="white-space: pre-wrap"></p>
                </details>
                <div id="scene" aria-live="polite"></div>
                <div class="composer">
                    <div id="actions" class="actions" role="group"></div>
                    <div class="input-row">
                        <textarea id="input" rows="2" maxlength="1200"></textarea>
                        <button type="button" id="send" class="btn"></button>
                    </div>
                    <div class="hintline" id="hintline"></div>
                    <div class="err" id="composeError" role="alert"></div>
                    <div style="margin-top: 12px; text-align: right">
                        <button type="button" id="finish" class="btn ok" disabled></button>
                    </div>
                </div>
                <details class="brief" open>
                    <summary id="protocolSummary"></summary>
                    <div id="log" class="log"></div>
                </details>
            </div>

            <aside class="side">
                <div class="card">
                    <h4>{{ $t['score'] }}</h4>
                    <div id="gauge" class="gauge">
                        <svg viewBox="0 0 160 160" aria-hidden="true"><circle class="track" cx="80" cy="80" r="70"></circle><circle class="fill" cx="80" cy="80" r="70"></circle></svg>
                        <div class="num"><b id="gaugeNum">—</b><span id="gaugeCap"></span></div>
                    </div>
                    <div class="round" id="round"></div>
                    <div id="bars"></div>
                    <div id="feedback" class="feedback"></div>
                </div>
                <div class="card" id="citesCard" hidden>
                    <h4>{{ $t['cites'] }}</h4>
                    <div id="cites" class="cites"></div>
                </div>
            </aside>
        </div>
    </section>

    <section id="screenVerdict" class="screen" hidden>
        <div class="verdict" id="verdictBody"></div>
    </section>

</main>

<script>
    window.COURT = {
        api: "{{ $pythonApiUrl ?? env('PYTHON_API_URL', 'http://127.0.0.1:5000') }}",
        lang: "{{ $tg ? 'tg' : 'ru' }}",
        codexUrl: "{{ route('contract.codex') }}",
    };
</script>
<script src="{{ asset('js/court-scene.js') }}?v={{ @filemtime(public_path('js/court-scene.js')) }}"></script>
<script src="{{ asset('js/court.js') }}?v={{ @filemtime(public_path('js/court.js')) }}"></script>
</body>
</html>
