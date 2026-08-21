<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="LegalAI Pro — AI-платформа для анализа юридических документов по законодательству Республики Таджикистан.">
<meta property="og:title" content="LegalAI Pro — юридический AI Таджикистана">
<meta property="og:type" content="website">
<meta name="theme-color" content="#0B1120">

@php
/* ================================================================
   ИКОНКИ (outline, heroicons-style) — единый стиль по всему проекту
   ================================================================ */
$icons = [
    'shield'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    'search'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
    'pencil'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
    'bolt'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
    'chat'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>',
    'calc'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m-6 4h6M9 15h.01M12 15h.01M15 15h.01M9 18h.01M12 18h.01M15 18h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>',
    'book'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
    'compare'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>',
    'scale'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>',
    'home'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
    'briefcase'=> '<path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
    'cube'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
    'lock'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>',
    'card'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
    'doc'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
    'check'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
    'warning'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
    'globe'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>',
    'send'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
    'sun'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'moon'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>',
    'menu'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>',
    'x'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',
    'chevron'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>',
    'arrow'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>',
    'upload'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>',
    'download' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>',
    'play'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'bell'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
    'clock'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    'star'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
    'up'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>',
];

$toolIcons = ['search', 'pencil', 'bolt', 'chat', 'calc', 'book', 'compare', 'scale'];
$docIcons  = ['home', 'briefcase', 'cube', 'shield', 'card', 'doc'];

/* ================================================================
   ЛОКАЛИЗАЦИЯ: ru / tg / en (единый массив $L)
   ================================================================ */
$L = match (app()->getLocale()) {

/* ---------------------------- TOJIKI ---------------------------- */
'tg' => [
    'nav' => ['home' => 'Саҳифаи асосӣ', 'dash' => 'Дашборд', 'calc' => 'Калкулятор', 'chat' => 'Чати ИИ', 'login' => 'Ворид шудан', 'notif' => 'Огоҳиномаҳо', 'menu' => 'Меню', 'search' => 'Ҷустуҷӯ'],
    'hero' => [
        'badge' => 'AI-таҳлил бо дақиқии то 95%',
        'title1' => 'AI-платформа',
        'title2' => 'барои кор бо ҳуҷҷатҳои ҳуқуқӣ',
        'typed' => ['аз рӯи қонунгузории Тоҷикистон', 'бо ишора ба моддаҳои кодексҳо', 'барои бизнес ва ҳуқуқшиносон'],
        'sub' => 'Санҷиши худкори шартномаҳо дар 60 сония. Ошкор кардани хавфҳои пинҳон, номувофиқатӣ ба қонунгузории ҶТ ва шартҳои номусоид. То 90% вақти ҳуқуқшиносон сарфа мешавад.',
        'cta1' => 'Бақайдгирӣ барои боркунӣ', 'cta2' => 'Видео-намоиш',
        'trust' => ['Рамзгузории AES-256', 'Мувофиқ ба Қонуни ҶТ «Дар бораи маълумоти шахсӣ»', 'Дастгирии 24/7'],
        'stat1' => 'Вақти миёнаи таҳлил', 'stat2' => 'Дақиқии муайянкунӣ',
    ],
    'ticker' => ['Кодекси гражданӣ', 'Кодекси меҳнат', 'Кодекси андоз', 'Кодекси ҷиноят', 'КҲМ ҶТ', 'Қонун дар бораи ҶДММ', 'Иҷора', 'Мерос', 'NDA', 'Таҳвил'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'санад дар база'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'дақиқӣ'], ['v' => 60, 's' => '', 'l' => 'сония таҳлил'], ['v' => 8, 's' => '', 'l' => 'абзори AI']],
    'prob' => [
        'badge' => 'Шинос аст?', 'title' => 'Ҳар як шартнома бе санҷиш', 'accent' => 'пул ва асабро мегирад',
        'sub' => 'Мо ҳисоб кардем, ки имзои шартнома «кӯр-кӯрона» ба тиҷорат дар Тоҷикистон чӣ қадар арзиш дорад.',
        'w_label' => 'Бе AI', 'w_title' => 'Рӯзи имрӯзаи шумо', 'a_label' => 'Бо AI', 'a_title' => 'Рӯзи шумо бо LegalAI Pro', 'cta' => 'Ҳамин тавр озмудан →',
        'problems' => [
            ['pain' => 'Шартнома «кӯр-кӯрона» имзо мешавад', 'detail' => 'Ҳуқуқшинос дар сафар аст, муомила таъҷил дорад. Имзо кардед — дар банди 5.3 неустойка 0.5% барои ҳар рӯзи таъхир аст.', 'cost' => 'Хавфҳои пинҳон: то 240 000 TJS'],
            ['pain' => 'Ҳуқуқшинос 6–8 соат барои як шартнома сарф мекунад', 'detail' => 'Хониши дастии 30+ саҳифа, муқоиса бо КГ, КМ ва КН ҶТ, санҷиши тараф аз феҳристҳо.', 'cost' => '3 000+ TJS барои як ҳуҷҷат'],
            ['pain' => 'Контрагент беэътимод баромад', 'detail' => 'Вақте маълум мешавад, ки пешпардохт рафтааст ва дар феҳрист қайди барҳамдиҳӣ ҳаст.', 'cost' => 'Зарари бебозгашт: то 100% маблағ'],
        ],
        'solutions' => [
            ['win' => 'AI 14 аз 14 хавфро дар 90 сония меёбад', 'detail' => 'Шартҳои вазнин, ҷаримаҳо ва зиддиятҳо бо кодексҳои ҶТ рост дар матн.', 'metric' => 'Дақиқӣ 98.7%'],
            ['win' => 'Санҷиш — бо арзиши як пиёла қаҳва', 'detail' => 'Таҳлили номаҳдуди шартномаҳо дар тарофи Pro.', 'metric' => 'ROI то 5 000%'],
            ['win' => 'Контрагент аз феҳристҳои давлатӣ санҷида шуд', 'detail' => 'Феҳристи шахсони ҳуқуқӣ, кумитаи андоз, хадамоти иҷро.', 'metric' => '0 ширкати якрӯза'],
        ],
    ],
    'tools' => [
        'badge' => '8 абзор', 'title' => 'Як платформа —', 'accent' => 'тамоми давраи ҳуқуқӣ',
        'sub' => 'Аз санҷиши шартномаи тайёр то сохтани ҳуҷҷати нав ва ҳисоби неустойка.', 'cta' => 'Кушодан →',
        'items' => [
            ['t' => 'Таҳлили шартномаҳо бо AI', 'd' => 'Санҷиши сатр ба сатр барои мувофиқат бо КГ, КМ ва КН ҶТ бо risk-score ва ишора ба моддаҳо.', 'b' => ['Шартҳои вазнин, ҷаримаҳо ва ӯҳдадориҳои пинҳон', 'Баҳогузории хавф: баланд / миёна / паст', 'Ҳисобот бо ишора ба моддаи вайроншуда']],
            ['t' => 'Конструктори шартномаҳо', 'd' => 'Устоди зина ба зина: шартномаро блок ба блок ҷамъ мекунед, система формулаҳои дурусти ҶТ-ро мегузорад.', 'b' => ['Интихоби намуд ва сохтори шартнома', 'Майдонҳо: тарафҳо, маблағ, мӯҳлат, масъулият', 'Содирот ба DOCX ва PDF']],
            ['t' => 'Генератори шартномаҳо', 'd' => 'Китобхонаи шаблонҳо аз рӯи қонунгузории ҶТ: тағйирёбандаҳоро пур кардед — ҳуҷҷати тайёр.', 'b' => ['25+ шаблон: иҷора, таҳвил, хизмат, NDA', 'Пуркунии фаврии реквизитҳо', 'Формулаҳо, санҷидашуда аз рӯи КГ ҶТ']],
            ['t' => 'AI-чати ҳуқуқӣ', 'd' => 'Ҷавоб ба саволҳои ҳуқуқӣ аз рӯи қонунгузории ҶТ бо ишора ба моддаҳои кодексҳо.', 'b' => ['Саволҳои гражданӣ, меҳнатӣ, андозӣ, оилавӣ', 'Таърихи гуфтугӯ ва саволҳои зуд', 'Илова кардани ҳуҷҷат рост дар чат']],
            ['t' => 'Калкуляторҳои ҳуқуқӣ', 'd' => 'Неустойка, андоз, боҷи давлатӣ, мерос, алимент, рухсатӣ — аз рӯи меъёрҳои ҶТ.', 'b' => ['Ҳисоби фаврӣ бо формула', 'Ишора ба меъёри қонун', 'Натиҷа ба сомонӣ']],
            ['t' => 'Базаи дониш', 'd' => 'Қонунгузории амалкунандаи ҶТ: кодексҳо, қонунҳо ва санадҳои зерқонунӣ бо ҷустуҷӯи пурра.', 'b' => ['Кодексҳо ва қонунҳо ба тоҷикӣ ва русӣ', 'Синхронизатсия бо манбаъҳои расмӣ', 'Филтрҳо аз рӯи соҳаи ҳуқуқ ва хатчӯбҳо']],
            ['t' => 'Муқоисаи шартномаҳо', 'd' => 'Diff-и сатр ба сатри ду нусха бо баҳогузории хавфи тағйирот.', 'b' => ['Илова / ҳазф / тағйир ёфт', 'Нишон додани ислоҳоти хавфнок', 'Ҳисобот дар бораи фарқиятҳо']],
            ['t' => 'Тавсифи ҳуқуқӣ (КҶ / КМ)', 'd' => 'Вазъиятро тавсиф кунед — AI моддаи дахлдорро пешниҳод карда, аломатҳои таркибро мекушояд.', 'b' => ['Таҳлили аломатҳои таркиб', 'Ишора ба моддаҳои КҶ ва КҲМ ҶТ', 'Замина: санксияҳо ва таҷриба'], 'note' => 'Хусусияти маълумотӣ дорад ва тавсифи расмӣ нест.'],
        ],
    ],
    'how' => [
        'title' => 'Ин чӣ гуна', 'accent' => 'кор мекунад', 'sub' => 'Се қадам аз савол ё файл то натиҷа бо ишора ба моддаҳо.', 'try' => 'Ҳозир санҷед →',
        'steps' => [
            ['title' => 'Ҳуҷҷатро бор кунед ё савол диҳед', 'desc' => 'PDF, DOCX ё TXT то 50 МБ — ё савол дар AI-чат.', 'd' => ['PDF, DOCX, TXT то 50 МБ', 'OCR барои сканҳо', 'Савол дар чат — бе файл']],
            ['title' => 'AI бо базаи қонунгузории ҶТ муқоиса мекунад', 'desc' => 'Модель матнро аз рӯи КГ, КМ, КН, КҶ, КҲМ ва 20 000+ санад таҳлил мекунад.', 'd' => ['5 кодекси ҶТ', '20 000+ санади меъёрӣ', 'Навсозӣ пас аз нашри расмӣ']],
            ['title' => 'Натиҷа бо ишора ба моддаҳо гиред', 'desc' => 'Ҳисобот, ислоҳот ё ҷавоб бо ишора ба меъёрҳои мушаххас.', 'd' => ['Ҳисобот PDF / DOCX', 'Ишора ба моддаҳои кодексҳо', 'Таърихи ҳамаи санҷишҳо']],
        ],
    ],
    'demo' => [
        'badge' => 'Намунаи зинда', 'title' => 'Савол диҳед —', 'accent' => 'AI бо ишора ба қонун ҷавоб медиҳад',
        'sub' => 'Намунаи гуфтугӯ дар AI-чат: савол дар бораи неустойка дар шартномаи таҳвил.',
        'user_q' => 'Банди 5.3 неустойка 0.5% дар як рӯзро муқаррар мекунад. Ин қонунӣ аст?',
        'ans' => ['Аз ҷиҳати расмӣ тарафҳо озоданд (мод. 421 КГ ҶТ), аммо 0.5% дар як рӯз — ин 182.5% дар як сол аст.', 'Суд метавонад неустойкаи номутаносибро аз рӯи мод. 350 КГ ҶТ кам кунад.', 'Тавсия: то 0.1% дар як рӯз маҳдуд кунед ва лимити 10% гузоред.'],
        'cite' => 'КГ ҶТ: мод. 421, мод. 350',
        'quick' => [
            ['q' => 'Хавфҳои ин банд кадоманд?', 'a' => ['Хавфи асосӣ — ситонидани неустойкаи номутаносиб: 182.5% дар як сол.', 'Тарафи дигар метавонад аз суд кам кардан пурсад (мод. 350 КГ ҶТ).']],
            ['q' => 'Неустойкаро чӣ тавр кам кардан мумкин?', 'a' => ['Лимит гузоред: на зиёда аз 0.1% дар як рӯз ва на беш аз 10% аз маблағ.', 'Бо тартиби судӣ аз рӯи мод. 350 КГ ҶТ кам мешавад.']],
            ['q' => 'КГ ҶТ чӣ мегӯяд?', 'a' => ['Мод. 421 озодии шартномаро мустаҳкам мекунад, мод. 350 ба суд имкон медиҳад неустойкаро кам кунад.']],
        ],
        'hint' => 'Ба савол пахш кунед — AI ҷавоб медиҳад', 'typing' => 'AI базаи қонунгузориро таҳлил мекунад…', 'note' => 'Ҳолати намоишӣ: ҷавоб дар асоси парвандаҳои воқеӣ.',
    ],
    'docs' => [
        'title' => 'Бо кадом ҳуҷҷатҳо кор мекунем', 'sub' => 'Шартномаҳо ва ҳуҷҷатҳои тиҷорат, шаҳрвандон ва ҳуқуқшиносон — аз рӯи стандартҳои ҶТ.',
        'types' => [['Шартномаи иҷора', 'Манзил, офис, таҷҳизот'], ['Шартномаи меҳнатӣ', 'Аз рӯи меъёрҳои КМ ҶТ'], ['Шартномаи таҳвил', 'Савдои хориҷӣ ва дохилӣ'], ['NDA', 'Махфият ва ҳифзи маълумот'], ['Шартномаи қарз', 'Бонкҳо ва ташкилотҳои қарзӣ'], ['Ҳуҷҷатҳои таъсисӣ', 'Оинномаҳо ва созишномаҳо']],
    ],
    'examples' => [
        'title' => 'Намунаҳои истифода', 'sub' => 'Ҳолатҳои воқеӣ, ки LegalAI Pro ҳал мекунад.',
        'items' => [
            ['t' => 'Иҷораи манзил', 'x' => 'Санҷиши шартномаи иҷора: депозит, мӯҳлат, таъмир ва масъулияти тарафҳо.'],
            ['t' => 'Шартномаи меҳнатӣ', 'x' => 'Мувофиқат бо КМ ҶТ: озмоиш, рухсатӣ, маош ва ихроҷ.'],
            ['t' => 'Шартномаи қарз', 'x' => 'Таҳлили фоизҳо, ҷаримаҳо ва шартҳои кабалии бонк ё МКО.'],
            ['t' => 'Шартномаи таҳвил', 'x' => 'Санадҳои интиқол, мӯҳлати пардохт ва ҷавобгарии тарафҳо.'],
        ],
    ],
    'about' => [
        'title' => 'Платформае, ки дар асоси ҳуқуқи Тоҷикистон сохта шудааст',
        'p1' => 'LegalAI Pro дар асоси базаи амалкунандаи меъёрии ҳуқуқии Ҷумҳурии Тоҷикистон омӯзонида шудааст: кодексҳо, қонунҳо ва санадҳои зерқонунӣ. База пас аз нашри расмии тағйирот мунтазам навсозӣ мешавад.',
        'p2' => 'Ҳар як хулоса бо ишора ба моддаи мушаххаси кодекс ё қонун ҳамроҳ аст — шумо ҳамеша метавонед манбаъро санҷед. Ҳуҷҷатҳо ва гуфтугӯҳо рамзгузорӣ мешаванд ва ба дархости шумо ҳазф мегарданд.',
        'p3' => 'Муҳим: хидмат хусусияти маълумотӣ дорад ва ҷойгузини маслиҳати адвокат ё нотариус нест.',
    ],
    'testimonials' => [
        'title' => 'Корбарон чӣ мегӯянд', 'sub' => 'Баҳоҳои воқеии бизнес ва ҳуқуқшиносон.',
        'items' => [
            ['x' => 'Таҳлили шартномаҳо аз як рӯз то як дақиқа кам шуд. Хавфҳоро пеш аз имзо мебинем.', 'n' => 'Фирӯза С.', 'r' => 'Директори молиявӣ, Душанбе'],
            ['x' => 'Ишора ба моддаҳои кодексҳо боварӣ медиҳад. Барои ҳуқуқшинос — ёрдамчии беҳамто.', 'n' => 'Баҳром К.', 'r' => 'Ҳуқуқшиноси ширкат'],
            ['x' => 'Калкулятори неустойка ва генератори шартномаҳо вақти маро ҳар рӯз сарфа мекунанд.', 'n' => 'Нилуфар Р.', 'r' => 'Соҳибкор, Хуҷанд'],
        ],
    ],
    'pricing' => [
        'title' => 'Тарофҳо', 'sub' => 'Оддӣ ва шаффоф. Бе пардохтҳои пинҳон.', 'popular' => 'Маъмул',
        'items' => [
            ['n' => 'Озмунӣ', 'p' => '0', 'per' => 'TJS / моҳ', 'f' => ['3 таҳлил дар рӯз', 'AI-чати базавӣ', 'Калкулятори неустойка', 'Дастгирии email'], 'c' => 'Оғоз кунед'],
            ['n' => 'Pro', 'p' => '199', 'per' => 'TJS / моҳ', 'f' => ['Таҳлили номаҳдуд', 'Ҳамаи 8 абзор', 'Базаи дониши пурра', 'Экспорт DOCX / PDF', 'Дастгирии 24/7'], 'c' => 'Интихоб кунед'],
            ['n' => 'Enterprise', 'p' => 'Фармоишӣ', 'per' => '', 'f' => ['API ва интегратсия', 'Ҳуҷҷатҳои маҳрамона', 'Менеҷери шахсӣ', 'SLA 99.9%'], 'c' => 'Тамос гиред'],
        ],
    ],
    'faq' => [
        'badge' => 'Саволу ҷавоб', 'title' => 'Оё', 'accent' => 'савол доред?', 'sub' => 'Ҷавобҳо дар бораи платформа, дақиқӣ ва маълумоти шумо',
        'items' => [
            ['q' => 'Таҳлили AI аз рӯи қонунгузории Тоҷикистон то чӣ андоза дақиқ аст?', 'a' => 'Модель дар асоси санадҳои меъёрии ҶТ омӯзонида шудааст ва дақиқии 98.7% нишон медиҳад. База пас аз нашри расмии тағйирот навсозӣ мешавад.'],
            ['q' => 'Оё ҳуҷҷатҳои генераторро бе ҳуқуқшинос истифода бурдан мумкин аст?', 'a' => 'Барои муомилоти типикии камхавф — ҳа. Барои муомилоти калон ҳуҷҷатро ба ҳуқуқшинос нишон диҳед; барои нотариалӣ нотариус ҳатмист.'],
            ['q' => 'Оё тавсиф аз рӯи моддаҳои КҶ ҶТ хулосаи расмӣ аст?', 'a' => 'Не. AI таҳлили маълумотӣ медиҳад. Тавсифи расмиро танҳо тафтишот, прокурор ва суд медиҳанд.'],
            ['q' => 'Оё нигоҳ доштани ҳуҷҷатҳо бехатар аст?', 'a' => 'Маълумот бо AES-256 рамзгузорӣ мешавад; ҳуҷҷатҳо пас аз таҳлил ҳазф мешаванд. Сервис ба Қонуни ҶТ «Дар бораи маълумоти шахсӣ» мувофиқ аст.'],
            ['q' => 'Оё база ба тоҷикӣ ва русӣ дастрас аст?', 'a' => 'Ҳа. Кодексҳо ва қонунҳо бо ҳар ду забон пешниҳод шудаанд; ҷустуҷӯ ҳар ду забонро мефаҳмад.'],
            ['q' => 'Кадом форматҳо дастгирӣ мешаванд?', 'a' => 'PDF, DOCX ва TXT то 50 МБ. Вақти миёнаи таҳлил 60 сония аст.'],
        ],
    ],
    'notice' => ['title' => 'Огоҳии ҳуқуқӣ', 'text' => 'Натиҷаҳои таҳлили AI, ҷавобҳои чат ва ҳисобҳо хусусияти маълумотӣ доранд ва хулосаи ҳуқуқӣ ё санади процессуалӣ нестанд.', 'text2' => 'Барои намояндагӣ дар суд ва амалҳои нотариалӣ бо ҳуқуқшиноси амалкунанда машварат кунед.'],
    'cta' => ['title' => 'Аввалин шартномаро ҳозир санҷед', 'sub' => '3 таҳлили ройгон — бе корти бонкӣ.', 'btn' => 'Оғози ройгон'],
    'footer' => ['about' => 'Платформаи ҳуқуқии AI барои Тоҷикистон: таҳлили шартномаҳо, ҳуҷҷатҳо, ҳисобҳо ва базаи қонунгузории ҶТ.', 'product' => 'Маҳсулот', 'resources' => 'Захираҳо', 'company' => 'Ширкат', 'res' => ['Базаи дониш', 'Блог', 'Вебинарҳо', 'Вазъи система'], 'comp' => ['Дар бораи мо', 'Тамос', 'Вазифаҳо', 'Шарикон'], 'copyright' => '© :year LegalAI Pro. Ҳамаи ҳуқуқҳо ҳифз шудаанд.', 'privacy' => 'Сиёсати махфият', 'terms' => 'Шартҳои истифода', 'support' => 'Дастгирӣ'],
    'dash' => [
        'upload' => 'Боркунӣ', 'analytics' => 'Аналитика', 'assistant' => 'AI-ёрдамчӣ', 'compare' => 'Муқоиса', 'history' => 'Таърих', 'settings' => 'Танзимот', 'new_tpl' => 'Шаблони нав',
        'up_title' => 'Боркунии ҳуҷҷат', 'drop' => 'Файлро ба ин ҷо кашед', 'or' => 'ё пахш кунед — PDF, DOCX, TXT то 50 МБ', 'choose' => 'Интихоби файл', 'analyze' => 'Оғози таҳлил',
        'progress' => 'Раванди таҳлил', 'st1' => 'Шинохти матн', 'st2' => 'Ҷустуҷӯи хавфҳо', 'st3' => 'Тартиби ҳисобот',
        'overall' => 'Хавфи умумӣ', 'found' => 'Хавфҳо ёфт шуд', 'time' => 'Вақти таҳлил', 'chart' => 'Тақсимоти хавфҳо', 'problems' => 'Мушкилоти ошкоршуда', 'recs' => 'Тавсияҳои AI', 'dl' => 'Зеркашии PDF', 'mail' => 'Фиристодан ба email',
        'act' => 'Фаъолияти санҷишҳо', 'types' => 'Намудҳои шартномаҳо', 'clear' => 'Тоза кардан', 'hello' => 'Салом! Ман AI-ҳуқуқшинос аз рӯи қонунгузории ҶТ ҳастам. Савол диҳед ё ҳуҷҷат илова кунед.',
        'ph' => 'Саволи худро аз рӯи ҳуқуқи ҶТ нависед…', 'docA' => 'Ҳуҷҷати A', 'docB' => 'Ҳуҷҷати B', 'cmp_run' => 'Муқоиса кардан', 'cmp_res' => 'Натиҷаҳои муқоиса', 'cmp_dl' => 'Зеркашии ҳисобот',
        'hist' => 'Таърихи таҳлилҳо', 'set_title' => 'Танзимоти профил', 'email_l' => 'Email', 'lang_l' => 'Забони интерфейс', 'notif_l' => 'Огоҳинома ба email', 'auto_l' => 'Таҳлили худкор', 'save' => 'Захира кардан',
    ],
    'js' => [
        'theme_light' => 'Мавзӯи равшан фаъол шуд', 'theme_dark' => 'Мавзӯи торик фаъол шуд',
        'file_ok' => 'Файл бор шуд', 'file_type' => 'Форматҳо: PDF, DOCX, TXT', 'file_size' => 'Файл набояд аз 50 МБ зиёд бошад',
        'done' => 'Таҳлил анҷом ёфт', 'cmp_need' => 'Ҳар ду ҳуҷҷатро бор кунед', 'cmp_run' => 'Муқоиса…', 'cmp_done' => 'Муқоиса анҷом ёфт',
        'saved' => 'Танзимот захира шуд', 'tpl' => 'Шаблон сохта шуд', 'sent' => 'Ҳисобот фиристода шуд', 'help' => 'Кӯмак лозим аст? Ба дастгирӣ нависед!', 'chat_clear' => 'Чат тоза шуд',
        'responses' => [
            'Дар асоси таҳлил, ба бандҳои масъулияти тарафҳо диққат диҳед. Формулаҳои ҳозира хавфи иловагӣ эҷод мекунанд.',
            'Шартнома дар маҷмӯъ ба КГ ҶТ мувофиқ аст, аммо чанд бандро барои ҳифзи беҳтар уточнить лозим аст.',
            'Ман 4 хавфи эҳтимолӣ ёфтам. Мухимтаринаш — масъулияти номаҳдуд бе лимит.',
            'Банди форс-мажор илова кунед ва мӯҳлатҳои пардохтро мушаххас кунед.',
        ],
        'risk_items' => [
            ['l' => 'high', 't' => 'Ҷаримаҳо аз 0.5% дар рӯз зиёд — то 0.1% кам кардан тавсия мешавад'],
            ['l' => 'medium', 't' => 'Банди форс-мажор вуҷуд надорад'],
            ['l' => 'medium', 't' => 'Мӯҳлатҳои пардохт мушаххас нашудаанд'],
            ['l' => 'low', 't' => 'Мӯҳлати махфият уточнение мехоҳад'],
        ],
        'recs' => ['Ҷаримаҳоро бо контрагент мувофиқ кунед', 'Бахши форс-мажор илова кунед', 'Мӯҳлатҳои пардохтро бо рӯзҳо нишон диҳед', 'Пеш аз имзо бо ҳуқуқшинос машварат кунед'],
        'high' => 'Баланд', 'medium' => 'Миёна', 'low' => 'Паст', 'overall_v' => 'Хавфи миёна',
        'labels' => ['Молиявӣ', 'Ҳуқуқӣ', 'Амалиётӣ', 'Обрӯӣ'], 'months' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'], 'types_l' => ['Таҳвил', 'Иҷора', 'Хизмат', 'Подряд', 'Дигар'],
    ],
],

/* ---------------------------- ENGLISH ---------------------------- */
'en' => [
    'nav' => ['home' => 'Home', 'dash' => 'Dashboard', 'calc' => 'Calculator', 'chat' => 'AI Chat', 'login' => 'Sign in', 'notif' => 'Notifications', 'menu' => 'Menu', 'search' => 'Search'],
    'hero' => [
        'badge' => 'AI analysis with up to 95% accuracy',
        'title1' => 'AI platform',
        'title2' => 'for legal document workflows',
        'typed' => ['built on Tajikistan law', 'with article-level citations', 'for business and lawyers'],
        'sub' => 'Automated contract review in 60 seconds. Detection of hidden risks, non-compliance with the legislation of Tajikistan and unfavorable terms. Saves up to 90% of lawyers’ time.',
        'cta1' => 'Register to upload', 'cta2' => 'Demo video',
        'trust' => ['AES-256 encryption', 'Compliant with the RT Law "On Personal Data"', '24/7 support'],
        'stat1' => 'Average analysis time', 'stat2' => 'Detection accuracy',
    ],
    'ticker' => ['Civil Code', 'Labor Code', 'Tax Code', 'Criminal Code', 'CAO of RT', 'LLC Law', 'Lease', 'Inheritance', 'NDA', 'Supply'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'acts in the base'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'accuracy'], ['v' => 60, 's' => '', 'l' => 'sec per analysis'], ['v' => 8, 's' => '', 'l' => 'AI tools']],
    'prob' => [
        'badge' => 'Sound familiar?', 'title' => 'Every contract signed unchecked', 'accent' => 'costs you money and nerves',
        'sub' => 'We calculated what signing without review actually costs businesses in Tajikistan.',
        'w_label' => 'Without AI', 'w_title' => 'Your day today', 'a_label' => 'With AI', 'a_title' => 'Your day with LegalAI Pro', 'cta' => 'Try it yourself →',
        'problems' => [
            ['pain' => 'Contracts get signed blindly', 'detail' => 'The lawyer is away, the deal is burning. You signed it — and clause 5.3 sets a 0.5% daily penalty.', 'cost' => 'Hidden risk: up to 240,000 TJS'],
            ['pain' => 'A lawyer spends 6–8 hours per contract', 'detail' => 'Manual review of 30+ pages, cross-checks with the Civil, Labor and Tax Codes.', 'cost' => '3,000+ TJS per document'],
            ['pain' => 'The counterparty turned out unreliable', 'detail' => 'You find out after the advance is gone and the registry shows liquidation.', 'cost' => 'Irrecoverable loss: up to 100%'],
        ],
        'solutions' => [
            ['win' => 'AI finds 14 of 14 risks in 90 seconds', 'detail' => 'Unfair terms, penalties and contradictions with RT codes highlighted in the text.', 'metric' => 'Accuracy 98.7%'],
            ['win' => 'Review for the price of a cup of coffee', 'detail' => 'Unlimited contract checks on the Pro plan.', 'metric' => 'ROI up to 5,000%'],
            ['win' => 'Counterparty checked via state registries', 'detail' => 'Legal entities registry, tax committee, enforcement service.', 'metric' => '0 shell companies'],
        ],
    ],
    'tools' => [
        'badge' => '8 tools', 'title' => 'One platform —', 'accent' => 'the full legal cycle',
        'sub' => 'From reviewing a ready contract to building a new document and calculating penalties.', 'cta' => 'Open →',
        'items' => [
            ['t' => 'AI contract review', 'd' => 'Line-by-line check against the Civil, Labor and Tax Codes of RT with a risk score and article references.', 'b' => ['Unfair terms, penalties, hidden obligations', 'Risk rating: high / medium / low', 'Report citing the violated article']],
            ['t' => 'Contract builder', 'd' => 'A step-by-step wizard: assemble the contract block by block while the system inserts RT-valid wording.', 'b' => ['Contract type and structure selection', 'Fields: parties, amounts, terms, liability', 'DOCX and PDF export']],
            ['t' => 'Contract generator', 'd' => 'A library of templates aligned with RT legislation: fill in the variables and get a finished document.', 'b' => ['25+ templates: lease, supply, services, NDA', 'Instant fill-in of details', 'Wording verified against the Civil Code']],
            ['t' => 'Legal AI chat', 'd' => 'Answers to legal questions under RT legislation with references to specific articles.', 'b' => ['Civil, labor, tax and family questions', 'Dialogue history and quick questions', 'Attach a document right in chat']],
            ['t' => 'Legal calculators', 'd' => 'Penalties, taxes, state duty, inheritance, alimony, vacation pay — under RT rules.', 'b' => ['Instant calculation with the formula', 'Reference to the legal norm', 'Clear result in somoni']],
            ['t' => 'Knowledge base', 'd' => 'Current legislation of Tajikistan: codes, laws and by-laws with full-text search.', 'b' => ['Codes and laws in Tajik and Russian', 'Sync with official sources', 'Filters by branch of law and bookmarks']],
            ['t' => 'Contract comparison', 'd' => 'Line-by-line diff of two versions with legal risk assessment of the changes.', 'b' => ['Added / removed / modified', 'Highlighting of risky edits', 'Report on key differences']],
            ['t' => 'Legal qualification (CC / CAO)', 'd' => 'Describe a situation — the AI suggests the applicable article and breaks down the offense elements.', 'b' => ['Analysis of offense elements', 'References to CC and CAO articles', 'Context: sanctions and practice'], 'note' => 'Informational only and not an official qualification.'],
        ],
    ],
    'how' => [
        'title' => 'How it', 'accent' => 'works', 'sub' => 'Three steps from a file or a question to results with article references.', 'try' => 'Try it now →',
        'steps' => [
            ['title' => 'Upload a document or ask a question', 'desc' => 'PDF, DOCX or TXT up to 50 MB — or a question in the AI chat.', 'd' => ['PDF, DOCX, TXT up to 50 MB', 'OCR for scanned copies', 'Chat question — no file needed']],
            ['title' => 'AI checks it against the RT legal base', 'desc' => 'The model analyzes the text against the Civil, Labor, Tax, Criminal Codes, CAO and 20,000+ acts.', 'd' => ['5 codes of Tajikistan', '20,000+ regulatory acts', 'Updates after official publication']],
            ['title' => 'Get results with article references', 'desc' => 'A report, edits or an answer citing specific legal norms.', 'd' => ['PDF / DOCX report', 'References to code articles', 'History of all checks']],
        ],
    ],
    'demo' => [
        'badge' => 'Live example', 'title' => 'Ask —', 'accent' => 'the AI answers citing the law',
        'sub' => 'A sample conversation in the Legal AI chat: a question about a penalty clause in a supply contract.',
        'user_q' => 'Clause 5.3 sets a penalty of 0.5% per day. Is this lawful?',
        'ans' => ['Formally the parties are free in contract terms (Art. 421 CC RT), but 0.5% per day equals 182.5% per year.', 'The court may reduce a disproportionate penalty under Art. 350 CC RT.', 'Recommendation: cap it at 0.1% per day with a 10% overall limit.'],
        'cite' => 'Civil Code of RT: Art. 421, Art. 350',
        'quick' => [
            ['q' => 'What are the risks of this clause?', 'a' => ['The main risk is recovery of a disproportionate penalty: 182.5% per year.', 'The other party may ask the court to reduce it (Art. 350 CC RT).']],
            ['q' => 'How can the penalty be reduced?', 'a' => ['Add a limit: no more than 0.1% per day and no more than 10% of the amount.', 'In court it is reduced under Art. 350 CC RT once disproportion is proven.']],
            ['q' => 'What does the Civil Code of RT say?', 'a' => ['Art. 421 establishes freedom of contract, but Art. 350 allows the court to reduce a disproportionate penalty.']],
        ],
        'hint' => 'Click a question — the AI will answer', 'typing' => 'AI is analyzing the legal base…', 'note' => 'Demo mode: the answer is generated from real case patterns.',
    ],
    'docs' => [
        'title' => 'Documents we work with', 'sub' => 'Contracts and documents for businesses, individuals and lawyers — aligned with RT legislation.',
        'types' => [['Lease agreement', 'Housing, office, equipment'], ['Employment contract', 'Under the RT Labor Code'], ['Supply agreement', 'Cross-border and domestic'], ['NDA', 'Confidentiality and data protection'], ['Loan agreement', 'Banks and microfinance'], ['Corporate documents', 'Charters and founder agreements']],
    ],
    'examples' => [
        'title' => 'Usage examples', 'sub' => 'Real situations LegalAI Pro solves.',
        'items' => [
            ['t' => 'Apartment lease', 'x' => 'Review of a lease: deposit, term, repairs and liability of the parties.'],
            ['t' => 'Employment contract', 'x' => 'Compliance with the Labor Code of RT: probation, vacation, salary and dismissal.'],
            ['t' => 'Loan agreement', 'x' => 'Analysis of interest, penalties and unfair terms of a bank or MFO.'],
            ['t' => 'Supply agreement', 'x' => 'Delivery acts, payment terms and liability of the parties.'],
        ],
    ],
    'about' => [
        'title' => 'A platform built on Tajikistan law',
        'p1' => 'LegalAI Pro is trained on the current regulatory base of the Republic of Tajikistan: codes, laws and by-laws. The base is regularly synchronized with official sources.',
        'p2' => 'Every conclusion comes with a reference to a specific article of a code or law — you can always verify the primary source. Documents and conversations are encrypted and deleted on your request.',
        'p3' => 'Important: the service is informational and does not replace the advice of an advocate or notary.',
    ],
    'testimonials' => [
        'title' => 'What users say', 'sub' => 'Real feedback from business and lawyers.',
        'items' => [
            ['x' => 'Contract review went from one day to one minute. We see the risks before signing.', 'n' => 'Firuza S.', 'r' => 'Finance director, Dushanbe'],
            ['x' => 'Article-level citations build trust. A perfect assistant for a lawyer.', 'n' => 'Bahrom K.', 'r' => 'Corporate lawyer'],
            ['x' => 'The penalty calculator and contract generator save me time every day.', 'n' => 'Nilufar R.', 'r' => 'Entrepreneur, Khujand'],
        ],
    ],
    'pricing' => [
        'title' => 'Pricing', 'sub' => 'Simple and transparent. No hidden fees.', 'popular' => 'Popular',
        'items' => [
            ['n' => 'Trial', 'p' => '0', 'per' => 'TJS / mo', 'f' => ['3 analyses per day', 'Basic AI chat', 'Penalty calculator', 'Email support'], 'c' => 'Start'],
            ['n' => 'Pro', 'p' => '199', 'per' => 'TJS / mo', 'f' => ['Unlimited analyses', 'All 8 tools', 'Full knowledge base', 'DOCX / PDF export', '24/7 support'], 'c' => 'Choose'],
            ['n' => 'Enterprise', 'p' => 'Custom', 'per' => '', 'f' => ['API and integrations', 'Confidential documents', 'Personal manager', 'SLA 99.9%'], 'c' => 'Contact us'],
        ],
    ],
    'faq' => [
        'badge' => 'FAQ', 'title' => 'Have', 'accent' => 'questions?', 'sub' => 'Answers about the platform, accuracy and your data',
        'items' => [
            ['q' => 'How accurate is the AI analysis for Tajikistan law?', 'a' => 'The model is fine-tuned on RT regulatory acts and reaches 98.7% accuracy on control samples. The base is updated after official publication of amendments.'],
            ['q' => 'Can documents from the generator be used as final without a lawyer?', 'a' => 'For typical low-risk transactions — yes. For large or unusual deals, show the final document to a lawyer; for notarial transactions a notary is mandatory.'],
            ['q' => 'Is the qualification under articles of the Criminal Code of RT an official opinion?', 'a' => 'No. The AI provides an informational breakdown. Official qualification is made only by the investigator, the prosecutor and the court.'],
            ['q' => 'Is it safe to store documents and chat history in the service?', 'a' => 'Data is encrypted with AES-256 in transit and at rest; documents are deleted after analysis. The service complies with the RT Law "On Personal Data".'],
            ['q' => 'Is the legal base available in Tajik and Russian at the same time?', 'a' => 'Yes. Codes and laws are provided in both languages; search understands queries in both.'],
            ['q' => 'Which formats are supported?', 'a' => 'PDF, DOCX and TXT up to 50 MB. Average analysis time is 60 seconds.'],
        ],
    ],
    'notice' => ['title' => 'Legal disclaimer', 'text' => 'AI analysis results, chat answers and calculations are informational only and do not constitute a legal opinion or procedural document.', 'text2' => 'For court representation and notarial acts, always consult a practicing lawyer (advocate).'],
    'cta' => ['title' => 'Check your first contract now', 'sub' => '3 free analyses — no bank card required.', 'btn' => 'Start for free'],
    'footer' => ['about' => 'Legal AI platform for Tajikistan: contract review, documents, calculations and the RT legislation base.', 'product' => 'Product', 'resources' => 'Resources', 'company' => 'Company', 'res' => ['Knowledge base', 'Blog', 'Webinars', 'System status'], 'comp' => ['About us', 'Contacts', 'Careers', 'Partners'], 'copyright' => '© :year LegalAI Pro. All rights reserved.', 'privacy' => 'Privacy policy', 'terms' => 'Terms of use', 'support' => 'Support'],
    'dash' => [
        'upload' => 'Upload', 'analytics' => 'Analytics', 'assistant' => 'AI assistant', 'compare' => 'Compare', 'history' => 'History', 'settings' => 'Settings', 'new_tpl' => 'New template',
        'up_title' => 'Upload a document', 'drop' => 'Drag a file here', 'or' => 'or click to choose — PDF, DOCX, TXT up to 50 MB', 'choose' => 'Choose file', 'analyze' => 'Start analysis',
        'progress' => 'Analysis process', 'st1' => 'Text recognition', 'st2' => 'Risk search', 'st3' => 'Report generation',
        'overall' => 'Overall risk', 'found' => 'Risks found', 'time' => 'Analysis time', 'chart' => 'Risk distribution', 'problems' => 'Detected issues', 'recs' => 'AI recommendations', 'dl' => 'Download PDF', 'mail' => 'Send by email',
        'act' => 'Review activity', 'types' => 'Contract types', 'clear' => 'Clear', 'hello' => 'Hello! I am your AI lawyer for Tajikistan law. Ask a question or attach a document.',
        'ph' => 'Ask a question about RT law…', 'docA' => 'Document A', 'docB' => 'Document B', 'cmp_run' => 'Compare documents', 'cmp_res' => 'Comparison results', 'cmp_dl' => 'Download report',
        'hist' => 'Analysis history', 'set_title' => 'Profile settings', 'email_l' => 'Email', 'lang_l' => 'Interface language', 'notif_l' => 'Email notifications', 'auto_l' => 'Automatic analysis', 'save' => 'Save changes',
    ],
    'js' => [
        'theme_light' => 'Light theme enabled', 'theme_dark' => 'Dark theme enabled',
        'file_ok' => 'File uploaded', 'file_type' => 'Allowed formats: PDF, DOCX, TXT', 'file_size' => 'File must not exceed 50 MB',
        'done' => 'Analysis complete', 'cmp_need' => 'Upload both documents to compare', 'cmp_run' => 'Comparing…', 'cmp_done' => 'Comparison complete',
        'saved' => 'Settings saved', 'tpl' => 'Template created', 'sent' => 'Report sent', 'help' => 'Need help? Contact support!', 'chat_clear' => 'Chat cleared',
        'responses' => [
            'Based on the analysis, pay attention to the liability clauses. Current wording may create additional risks.',
            'The contract generally complies with the Civil Code of RT, but a few clauses should be clarified for better protection.',
            'I found 4 potential risks. The most critical is unlimited liability without a cap.',
            'Add a force majeure clause and specify payment terms to avoid ambiguous interpretation.',
        ],
        'risk_items' => [
            ['l' => 'high', 't' => 'Penalties exceed 0.5% per day — recommend reducing to 0.1%'],
            ['l' => 'medium', 't' => 'No force majeure clause'],
            ['l' => 'medium', 't' => 'Payment terms are not specified'],
            ['l' => 'low', 't' => 'Confidentiality term needs clarification'],
        ],
        'recs' => ['Negotiate the penalty amount with the counterparty', 'Add a force majeure section', 'Specify payment terms in days from signing', 'Consult a lawyer before signing'],
        'high' => 'High', 'medium' => 'Medium', 'low' => 'Low', 'overall_v' => 'Medium risk',
        'labels' => ['Financial', 'Legal', 'Operational', 'Reputational'], 'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], 'types_l' => ['Supply', 'Lease', 'Services', 'Work', 'Other'],
    ],
],

/* ---------------------------- РУССКИЙ ---------------------------- */
default => [
    'nav' => ['home' => 'Главная', 'dash' => 'Дашборд', 'calc' => 'Калькулятор', 'chat' => 'Чат ИИ', 'login' => 'Войти', 'notif' => 'Уведомления', 'menu' => 'Меню', 'search' => 'Поиск'],
    'hero' => [
        'badge' => 'AI-анализ с точностью до 95%',
        'title1' => 'AI-платформа',
        'title2' => 'для работы с юридическими документами',
        'typed' => ['по законодательству Таджикистана', 'со ссылками на статьи кодексов', 'для бизнеса и юристов'],
        'sub' => 'Автоматическая проверка контрактов за 60 секунд. Выявление скрытых рисков, несоответствий законодательству Республики Таджикистан и невыгодных условий. Экономия до 90% времени юристов.',
        'cta1' => 'Зарегистрироваться для загрузки', 'cta2' => 'Демо-видео',
        'trust' => ['Шифрование AES-256', 'Соответствие Закону РТ «О персональных данных»', '24/7 поддержка'],
        'stat1' => 'Среднее время анализа', 'stat2' => 'Точность выявления',
    ],
    'ticker' => ['Гражданский кодекс', 'Трудовой кодекс', 'Налоговый кодекс', 'Уголовный кодекс', 'КоАП РТ', 'Закон об ООО', 'Аренда', 'Наследство', 'NDA', 'Поставка'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'актов в базе'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'точность'], ['v' => 60, 's' => '', 'l' => 'секунд анализ'], ['v' => 8, 's' => '', 'l' => 'AI-инструментов']],
    'prob' => [
        'badge' => 'Знакомо?', 'title' => 'Каждый договор без проверки', 'accent' => 'стоит денег и нервов',
        'sub' => 'Мы посчитали, во что бизнесу в Таджикистане обходится подписание документов «не глядя».',
        'w_label' => 'Без AI', 'w_title' => 'Ваш сегодняшний день', 'a_label' => 'С AI', 'a_title' => 'Ваш день с LegalAI Pro', 'cta' => 'Хочу так же — попробовать →',
        'problems' => [
            ['pain' => 'Подписываете договор «вслепую»', 'detail' => 'Юрист в отпуске, дедлайн горит, контрагент торопит. Подписали — а в п. 5.3 неустойка 0.5%/день.', 'cost' => 'Скрытые риски: до 240 000 TJS'],
            ['pain' => 'Юрист тратит 6–8 часов на один договор', 'detail' => 'Ручная вычитка 30+ страниц, сверка с ГК, ТК и НК РТ, проверка контрагента по реестрам.', 'cost' => '3 000+ TJS за один документ'],
            ['pain' => 'Контрагент оказался однодневкой', 'detail' => 'Узнаёте об этом, когда деньги уже ушли, а в реестре — отметка о ликвидации.', 'cost' => 'Невозвратные потери: до 100% суммы'],
        ],
        'solutions' => [
            ['win' => 'AI находит 14 из 14 рисков за 90 секунд', 'detail' => 'Подсветка кабальных пунктов, штрафов и ловушек прямо в тексте.', 'metric' => 'Точность 98.7%'],
            ['win' => 'Анализ за стоимость чашки кофе', 'detail' => 'Безлимитная проверка договоров на тарифе Pro.', 'metric' => 'ROI до 5 000%'],
            ['win' => 'Контрагент проверен по госреестрам', 'detail' => 'Реестр юридических лиц, налоговый комитет, служба исполнения.', 'metric' => '0 однодневок'],
        ],
    ],
    'tools' => [
        'badge' => '8 инструментов', 'title' => 'Одна платформа —', 'accent' => 'весь юридический цикл',
        'sub' => 'От проверки готового договора до сборки нового документа и расчёта неустойки.', 'cta' => 'Открыть →',
        'items' => [
            ['t' => 'AI-анализ договоров', 'd' => 'Построчная проверка договора на соответствие ГК, ТК и НК РТ с risk-score и ссылками на статьи.', 'b' => ['Кабальные условия, штрафы и скрытые обязательства', 'Оценка риска: высокий / средний / низкий', 'Отчёт с указанием нарушенной статьи закона']],
            ['t' => 'Конструктор договоров', 'd' => 'Пошаговый мастер: собираете договор блок за блоком, система подставляет валидные для РТ формулировки.', 'b' => ['Выбор типа договора и структуры', 'Поля: стороны, суммы, сроки, ответственность', 'Экспорт в DOCX и PDF']],
            ['t' => 'Генератор договоров', 'd' => 'Библиотека шаблонов под законодательство РТ: заполнили переменные — получили готовый документ.', 'b' => ['25+ шаблонов: аренда, поставка, услуги, NDA', 'Мгновенное заполнение реквизитов', 'Формулировки, проверенные по ГК РТ']],
            ['t' => 'Юридический AI-чат', 'd' => 'Ответы на правовые вопросы по законодательству РТ со ссылками на конкретные статьи кодексов.', 'b' => ['Гражданские, трудовые, налоговые, семейные вопросы', 'История диалога и быстрые вопросы', 'Можно прикрепить документ прямо в чат']],
            ['t' => 'Юридические калькуляторы', 'd' => 'Неустойка, налоги, госпошлина, наследство, алименты, отпускные — по нормам РТ.', 'b' => ['Мгновенный расчёт с формулой', 'Ссылка на норму закона', 'Понятный результат в сомони']],
            ['t' => 'База знаний', 'd' => 'Актуальное законодательство РТ: кодексы, законы и подзаконные акты с полнотекстовым поиском.', 'b' => ['Кодексы и законы на таджикском и русском', 'Синхронизация с официальными источниками', 'Фильтры по отрасли права и закладки']],
            ['t' => 'Сравнение договоров', 'd' => 'Построчный diff двух версий документа с оценкой юридических рисков изменений.', 'b' => ['Добавлено / удалено / изменено', 'Подсветка рискованных правок', 'Отчёт о ключевых расхождениях']],
            ['t' => 'Квалификация по УК / КоАП РТ', 'd' => 'Опишите ситуацию — AI предложит применимую статью и разберёт состав правонарушения.', 'b' => ['Разбор признаков состава', 'Ссылки на статьи УК и КоАП РТ', 'Контекст: санкции и практика'], 'note' => 'Носит справочный характер и не является официальной квалификацией.'],
        ],
    ],
    'how' => [
        'title' => 'Как это', 'accent' => 'работает', 'sub' => 'Три шага от файла или вопроса до результата со ссылками на статьи.', 'try' => 'Попробуйте сейчас →',
        'steps' => [
            ['title' => 'Загрузите документ или задайте вопрос', 'desc' => 'PDF, DOCX или TXT до 50 МБ — либо вопрос в AI-чате.', 'd' => ['PDF, DOCX, TXT до 50 МБ', 'OCR для отсканированных копий', 'Вопрос в чате — без файла']],
            ['title' => 'AI сверяет с базой законодательства РТ', 'desc' => 'Модель анализирует текст по ГК, ТК, НК, УК, КоАП и 20 000+ нормативных актов.', 'd' => ['5 кодексов РТ', '20 000+ нормативных актов', 'Обновления после официальных публикаций']],
            ['title' => 'Получите результат со ссылками на статьи', 'desc' => 'Отчёт, правки или ответ с указанием конкретных норм закона.', 'd' => ['Отчёт PDF / DOCX', 'Ссылки на статьи кодексов', 'История всех проверок']],
        ],
    ],
    'demo' => [
        'badge' => 'Живой пример', 'title' => 'Спросите —', 'accent' => 'AI ответит со ссылкой на закон',
        'sub' => 'Пример реального диалога в юридическом AI-чате: вопрос о неустойке в договоре поставки.',
        'user_q' => 'Пункт 5.3 устанавливает неустойку 0.5% в день. Это законно?',
        'ans' => ['Формально стороны свободны в условиях договора (ст. 421 ГК РТ), но 0.5% в день — это 182.5% годовых.', 'Суд вправе снизить несоразмерную неустойку по ст. 350 ГК РТ.', 'Рекомендация: ограничить 0.1% в день и установить общий лимит 10%.'],
        'cite' => 'ГК РТ: ст. 421, ст. 350',
        'quick' => [
            ['q' => 'Какие риски в этом пункте?', 'a' => ['Главный риск — взыскание несоразмерной неустойки: 182.5% годовых.', 'Вторая сторона может потребовать снижения в суде по ст. 350 ГК РТ.']],
            ['q' => 'Как снизить неустойку?', 'a' => ['Добавьте лимит: не более 0.1% в день и не свыше 10% от суммы.', 'В судебном порядке снижается по ст. 350 ГК РТ при несоразмерности.']],
            ['q' => 'Что говорит ГК РТ?', 'a' => ['Ст. 421 закрепляет свободу договора, но ст. 350 позволяет суду снижать несоразмерную неустойку.']],
        ],
        'hint' => 'Нажмите на вопрос — AI ответит', 'typing' => 'AI анализирует базу законодательства…', 'note' => 'Демо-режим: ответ сгенерирован по мотивам реальных кейсов.',
    ],
    'docs' => [
        'title' => 'С какими документами мы работаем', 'sub' => 'Договоры и документы бизнеса, физлиц и юристов — по стандартам законодательства РТ.',
        'types' => [['Договор аренды', 'Жильё, офис, оборудование'], ['Трудовой договор', 'По нормам ТК РТ'], ['Договор поставки', 'ВЭД и внутренние поставки'], ['NDA', 'Конфиденциальность и защита данных'], ['Кредитный договор', 'Банки и МКО'], ['Учредительные документы', 'Уставы и соглашения учредителей']],
    ],
    'examples' => [
        'title' => 'Примеры использования', 'sub' => 'Реальные ситуации, которые решает LegalAI Pro.',
        'items' => [
            ['t' => 'Аренда квартиры', 'x' => 'Проверка договора аренды: депозит, срок, ремонт и ответственность сторон.'],
            ['t' => 'Трудовой договор', 'x' => 'Соответствие ТК РТ: испытательный срок, отпуск, зарплата и увольнение.'],
            ['t' => 'Кредитный договор', 'x' => 'Анализ процентов, штрафов и кабальных условий банка или МКО.'],
            ['t' => 'Договор поставки', 'x' => 'Акты приёмки, сроки оплаты и ответственность сторон.'],
        ],
    ],
    'about' => [
        'title' => 'Платформа, построенная на праве Таджикистана',
        'p1' => 'LegalAI Pro обучен на актуальной нормативной базе Республики Таджикистан: кодексы, законы и подзаконные акты. База регулярно синхронизируется с официальными источниками после публикации изменений.',
        'p2' => 'Каждый вывод системы сопровождается ссылкой на конкретную статью кодекса или закона — вы всегда можете проверить первоисточник. Документы и переписка шифруются и удаляются по вашему запросу.',
        'p3' => 'Важно: сервис носит информационный характер и не заменяет консультацию адвоката или нотариуса.',
    ],
    'testimonials' => [
        'title' => 'Что говорят пользователи', 'sub' => 'Реальные отзывы бизнеса и юристов.',
        'items' => [
            ['x' => 'Проверка договора сократилась с одного дня до одной минуты. Риски видим до подписания.', 'n' => 'Фируза С.', 'r' => 'Финансовый директор, Душанбе'],
            ['x' => 'Ссылки на статьи кодексов вызывают доверие. Незаменимый помощник для юриста.', 'n' => 'Бахром К.', 'r' => 'Юрист компании'],
            ['x' => 'Калькулятор неустойки и генератор договоров экономят мне время каждый день.', 'n' => 'Нилуфар Р.', 'r' => 'Предприниматель, Худжанд'],
        ],
    ],
    'pricing' => [
        'title' => 'Тарифы', 'sub' => 'Просто и прозрачно. Без скрытых платежей.', 'popular' => 'Популярный',
        'items' => [
            ['n' => 'Пробный', 'p' => '0', 'per' => 'TJS / мес', 'f' => ['3 анализа в день', 'Базовый AI-чат', 'Калькулятор неустойки', 'Email-поддержка'], 'c' => 'Начать'],
            ['n' => 'Pro', 'p' => '199', 'per' => 'TJS / мес', 'f' => ['Безлимитный анализ', 'Все 8 инструментов', 'Полная база знаний', 'Экспорт DOCX / PDF', 'Поддержка 24/7'], 'c' => 'Выбрать'],
            ['n' => 'Enterprise', 'p' => 'По запросу', 'per' => '', 'f' => ['API и интеграции', 'Конфиденциальные документы', 'Персональный менеджер', 'SLA 99.9%'], 'c' => 'Связаться'],
        ],
    ],
    'faq' => [
        'badge' => 'Частые вопросы', 'title' => 'Есть', 'accent' => 'вопросы?', 'sub' => 'Ответы о платформе, точности и ваших данных',
        'items' => [
            ['q' => 'Насколько точен AI-анализ по законодательству Таджикистана?', 'a' => 'Модель дообучена на нормативных актах РТ и показывает точность 98.7% на контрольных выборках. База обновляется после официальных публикаций изменений.'],
            ['q' => 'Можно ли использовать документы генератора как окончательные — без юриста?', 'a' => 'Для типовых сделок с низким риском — да. Для крупных или нестандартных сделок покажите итоговый документ юристу, а для нотариальных сделок нотариус обязателен.'],
            ['q' => 'Квалификация по статьям УК РТ — это официальное заключение?', 'a' => 'Нет. AI даёт справочный разбор признаков состава и указывает применимые статьи. Официальную квалификацию проводят только следователь, прокурор и суд.'],
            ['q' => 'Безопасно ли хранить документы и переписку в сервисе?', 'a' => 'Данные шифруются AES-256 при передаче и хранении, документы удаляются после анализа. Сервис соответствует Закону РТ «О персональных данных».'],
            ['q' => 'База законов доступна на таджикском и русском одновременно?', 'a' => 'Да. Кодексы и законы представлены на обоих языках, поиск понимает запросы на обоих языках.'],
            ['q' => 'Какие форматы поддерживаются?', 'a' => 'PDF, DOCX и TXT до 50 МБ. Среднее время анализа — 60 секунд.'],
        ],
    ],
    'notice' => ['title' => 'Правовая оговорка', 'text' => 'Результаты AI-анализа, ответы чата и расчёты носят информационный характер и не являются юридическим заключением или процессуальным документом.', 'text2' => 'Для представительства в суде и нотариальных действий обязательно проконсультируйтесь с практикующим юристом (адвокатом).'],
    'cta' => ['title' => 'Проверьте первый договор прямо сейчас', 'sub' => '3 бесплатных анализа — без банковской карты.', 'btn' => 'Начать бесплатно'],
    'footer' => ['about' => 'Юридическая AI-платформа для Таджикистана: анализ договоров, документы, расчёты и база законодательства РТ.', 'product' => 'Продукт', 'resources' => 'Ресурсы', 'company' => 'Компания', 'res' => ['База знаний', 'Блог', 'Вебинары', 'Статус системы'], 'comp' => ['О нас', 'Контакты', 'Карьера', 'Партнёрам'], 'copyright' => '© :year LegalAI Pro. Все права защищены.', 'privacy' => 'Политика конфиденциальности', 'terms' => 'Условия использования', 'support' => 'Поддержка'],
    'dash' => [
        'upload' => 'Загрузка', 'analytics' => 'Аналитика', 'assistant' => 'AI-ассистент', 'compare' => 'Сравнение', 'history' => 'История', 'settings' => 'Настройки', 'new_tpl' => 'Новый шаблон',
        'up_title' => 'Загрузка документа', 'drop' => 'Перетащите файл сюда', 'or' => 'или нажмите для выбора — PDF, DOCX, TXT до 50 МБ', 'choose' => 'Выбрать файл', 'analyze' => 'Начать анализ',
        'progress' => 'Процесс анализа', 'st1' => 'Распознавание текста', 'st2' => 'Поиск рисков', 'st3' => 'Генерация отчёта',
        'overall' => 'Общий риск', 'found' => 'Найдено рисков', 'time' => 'Время анализа', 'chart' => 'Распределение рисков', 'problems' => 'Выявленные проблемы', 'recs' => 'Рекомендации AI', 'dl' => 'Скачать PDF', 'mail' => 'Отправить на email',
        'act' => 'Активность проверок', 'types' => 'Типы договоров', 'clear' => 'Очистить', 'hello' => 'Здравствуйте! Я ваш AI-юрист по законодательству РТ. Задайте вопрос или прикрепите документ.',
        'ph' => 'Задайте вопрос по праву РТ…', 'docA' => 'Документ A', 'docB' => 'Документ B', 'cmp_run' => 'Сравнить документы', 'cmp_res' => 'Результаты сравнения', 'cmp_dl' => 'Скачать отчёт',
        'hist' => 'История анализов', 'set_title' => 'Настройки профиля', 'email_l' => 'Email', 'lang_l' => 'Язык интерфейса', 'notif_l' => 'Уведомления на email', 'auto_l' => 'Автоматический анализ', 'save' => 'Сохранить изменения',
    ],
    'js' => [
        'theme_light' => 'Светлая тема включена', 'theme_dark' => 'Тёмная тема включена',
        'file_ok' => 'Файл загружен', 'file_type' => 'Допустимые форматы: PDF, DOCX, TXT', 'file_size' => 'Файл не должен превышать 50 МБ',
        'done' => 'Анализ завершён', 'cmp_need' => 'Загрузите оба документа для сравнения', 'cmp_run' => 'Сравнение…', 'cmp_done' => 'Сравнение завершено',
        'saved' => 'Настройки сохранены', 'tpl' => 'Шаблон создан', 'sent' => 'Отчёт отправлен', 'help' => 'Нужна помощь? Напишите в поддержку!', 'chat_clear' => 'Чат очищен',
        'responses' => [
            'На основе анализа договора рекомендую обратить внимание на пункты об ответственности сторон. Текущие формулировки могут создать дополнительные риски.',
            'Договор в целом соответствует ГК РТ, однако есть несколько пунктов, которые стоит уточнить для лучшей защиты ваших интересов.',
            'Я обнаружил 4 потенциальных риска в этом договоре. Наиболее критичный — неограниченная ответственность без верхнего лимита.',
            'Рекомендую добавить пункт о форс-мажорных обстоятельствах и уточнить сроки оплаты для избежания неоднозначного толкования.',
        ],
        'risk_items' => [
            ['l' => 'high', 't' => 'Штрафные санкции превышают 0.5% в день — рекомендуется снизить до 0.1%'],
            ['l' => 'medium', 't' => 'Отсутствует пункт о форс-мажорных обстоятельствах'],
            ['l' => 'medium', 't' => 'Не указаны сроки оплаты — рекомендуется добавить конкретные даты'],
            ['l' => 'low', 't' => 'Пункт о конфиденциальности требует уточнения сроков действия'],
        ],
        'recs' => ['Согласовать размер штрафных санкций с контрагентом', 'Добавить раздел о форс-мажорных обстоятельствах', 'Указать конкретные сроки оплаты в днях с момента подписания', 'Проконсультироваться с юристом перед подписанием'],
        'high' => 'Высокий', 'medium' => 'Средний', 'low' => 'Низкий', 'overall_v' => 'Средний риск',
        'labels' => ['Финансовые', 'Юридические', 'Операционные', 'Репутационные'], 'months' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'], 'types_l' => ['Поставка', 'Аренда', 'Услуги', 'Подряд', 'Другие'],
    ],
],
};
@endphp

<title>{{ $L['hero']['title1'] }} — {{ $L['hero']['title2'] }} | LegalAI Pro</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
/* ================================================================
   1. ДИЗАЙН-ТОКЕНЫ (светлая база — как на макете; тёмная — опция)
   ================================================================ */
:root {
    --bg-primary: #F7F9FC;
    --bg-secondary: #FFFFFF;
    --card-bg: rgba(255, 255, 255, 0.85);
    --card-solid: #FFFFFF;
    --card-border: rgba(15, 23, 42, 0.08);
    --card-hover-border: rgba(59, 130, 246, 0.35);
    --text-primary: #0F172A;
    --text-secondary: #64748B;
    --text-muted: #94A3B8;
    --accent-primary: #3B82F6;
    --accent-gradient: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 50%, #EC4899 100%);
    --accent-glow: rgba(99, 102, 241, 0.22);
    --success: #10B981;
    --warning: #F59E0B;
    --error: #EF4444;
    --glass-blur: blur(14px);
    --shadow-card: 0 1px 2px rgba(15,23,42,.05), 0 8px 24px rgba(15,23,42,.07);
    --shadow-hover: 0 12px 40px rgba(59,130,246,.16), 0 4px 12px rgba(15,23,42,.06);
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-full: 9999px;
    --t-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --t-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --t-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    --nav-bg: rgba(255, 255, 255, 0.86);
    --grid-line: rgba(15, 23, 42, 0.045);
    --soft-blue: #EFF4FF;
    --soft-blue-t: #2563EB;
}

/* ---------- Тёмная тема ---------- */
body.dark-theme {
    --bg-primary: #0B1120;
    --bg-secondary: #111827;
    --card-bg: rgba(17, 24, 39, 0.7);
    --card-solid: #111827;
    --card-border: rgba(55, 65, 81, 0.5);
    --card-hover-border: rgba(59, 130, 246, 0.45);
    --text-primary: #F9FAFB;
    --text-secondary: #9CA3AF;
    --text-muted: #6B7280;
    --shadow-card: 0 8px 32px rgba(0,0,0,.3), 0 2px 8px rgba(0,0,0,.2);
    --shadow-hover: 0 20px 60px rgba(0,0,0,.4), 0 4px 16px rgba(59,130,246,.15);
    --nav-bg: rgba(11, 17, 32, 0.85);
    --grid-line: rgba(148, 163, 184, 0.05);
    --soft-blue: rgba(59, 130, 246, 0.12);
    --soft-blue-t: #93C5FD;
}

/* ================================================================
   2. БАЗА
   ================================================================ */
* {
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    background: var(--bg-primary);
    color: var(--text-primary);
    overflow-x: hidden;
    line-height: 1.6;
    position: relative;
    transition: background var(--t-normal), color var(--t-normal);
}

h1, h2, h3, h4 {
    color: var(--text-primary);
    letter-spacing: -0.02em;
}

::selection {
    background: var(--accent-primary);
    color: #fff;
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

::-webkit-scrollbar-thumb {
    background: var(--text-muted);
    border-radius: var(--radius-full);
}

*:focus-visible {
    outline: 2px solid var(--accent-primary);
    outline-offset: 2px;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.c1 { color: var(--text-primary); }
.c2 { color: var(--text-secondary); }
.c3 { color: var(--text-muted); }

/* ================================================================
   3. ЖИВОЙ ФОН: сетка + частицы + дрейфующие градиенты
   ================================================================ */
.bg-grid {
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background-image:
        linear-gradient(var(--grid-line) 1px, transparent 1px),
        linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
    background-size: 56px 56px;
    mask-image: radial-gradient(ellipse 100% 90% at 50% 10%, black 40%, transparent 90%);
    -webkit-mask-image: radial-gradient(ellipse 100% 90% at 50% 10%, black 40%, transparent 90%);
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background:
        radial-gradient(600px circle at 18% 28%, rgba(59,130,246,.10) 0%, transparent 55%),
        radial-gradient(520px circle at 82% 68%, rgba(139,92,246,.08) 0%, transparent 55%),
        radial-gradient(420px circle at 42% 82%, rgba(236,72,153,.05) 0%, transparent 55%);
    animation: bgShift 22s ease-in-out infinite alternate;
}

@keyframes bgShift {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(28px, -22px) scale(1.02); }
    100% { transform: translate(-22px, 20px) scale(0.98); }
}

.particles {
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 5px;
    height: 5px;
    background: rgba(59, 130, 246, 0.35);
    border-radius: 50%;
    animation: floatParticle linear infinite;
}

.particle:nth-child(1) { left: 8%; animation-duration: 21s; }
.particle:nth-child(2) { left: 18%; animation-duration: 18s; animation-delay: 2s; }
.particle:nth-child(3) { left: 28%; animation-duration: 23s; animation-delay: 4s; }
.particle:nth-child(4) { left: 38%; animation-duration: 17s; animation-delay: 1s; }
.particle:nth-child(5) { left: 48%; animation-duration: 20s; animation-delay: 3s; }
.particle:nth-child(6) { left: 58%; animation-duration: 22s; animation-delay: 5s; }
.particle:nth-child(7) { left: 68%; animation-duration: 16s; animation-delay: .5s; }
.particle:nth-child(8) { left: 78%; animation-duration: 24s; animation-delay: 2.5s; }
.particle:nth-child(9) { left: 88%; animation-duration: 19s; animation-delay: 4.5s; }
.particle:nth-child(10) { left: 94%; animation-duration: 21s; animation-delay: 6s; }

@keyframes floatParticle {
    0% { transform: translateY(105vh); opacity: 0; }
    10%, 90% { opacity: .7; }
    100% { transform: translateY(-8vh); opacity: 0; }
}

/* ================================================================
   4. СЛУЖЕБНЫЕ ЭЛЕМЕНТЫ: прогресс скролла, back-to-top
   ================================================================ */
#scrollProgress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0%;
    z-index: 10001;
    background: var(--accent-gradient);
    box-shadow: 0 0 12px var(--accent-glow);
}

#backToTop {
    position: fixed;
    bottom: 104px;
    right: 32px;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: 1px solid var(--card-border);
    background: var(--card-solid);
    color: var(--accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 9997;
    opacity: 0;
    transform: translateY(16px);
    pointer-events: none;
    transition: all var(--t-normal);
    box-shadow: var(--shadow-card);
}

#backToTop.show {
    opacity: 1;
    transform: none;
    pointer-events: auto;
}

#backToTop:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}

/* ================================================================
   5. НАВИГАЦИЯ
   ================================================================ */
nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: var(--nav-bg);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--card-border);
}

.nav-link {
    color: var(--text-secondary);
    font-size: .92rem;
    font-weight: 500;
    padding: 6px 2px;
    position: relative;
    transition: color var(--t-fast);
}

.nav-link:hover {
    color: var(--text-primary);
}

.nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    border-radius: 2px;
    background: var(--accent-gradient);
    transition: width var(--t-normal);
}

.nav-link:hover::after {
    width: 100%;
}

.lang-btn {
    display: flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem .9rem;
    border: 1px solid var(--card-border);
    border-radius: 10px;
    background: var(--card-solid);
    font-size: .8rem;
    font-weight: 700;
    color: var(--text-primary);
    cursor: pointer;
    transition: all .2s;
}

.lang-btn:hover {
    border-color: var(--card-hover-border);
}

.dropdown-panel {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 8px;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    box-shadow: var(--shadow-hover);
    overflow: hidden;
    animation: dropIn .25s ease;
    z-index: 10001;
}

@keyframes dropIn {
    from { opacity: 0; transform: translateY(-8px) scale(.98); }
    to { opacity: 1; transform: none; }
}

.bell-btn {
    position: relative;
    color: var(--text-secondary);
    padding: .45rem;
    border-radius: 10px;
    transition: all .2s;
    cursor: pointer;
    background: none;
    border: none;
}

.bell-btn:hover {
    color: var(--text-primary);
    background: var(--soft-blue);
}

.bell-badge {
    position: absolute;
    top: 0;
    right: 0;
    width: 16px;
    height: 16px;
    background: #EF4444;
    border-radius: 50%;
    font-size: 10px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

/* ================================================================
   6. КНОПКИ / ПОЛЯ / БЕЙДЖИ
   ================================================================ */
.btn-primary {
    background: var(--accent-gradient);
    color: #fff;
    border: none;
    padding: .8rem 1.6rem;
    border-radius: var(--radius-md);
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px var(--accent-glow);
    transition: transform var(--t-normal), box-shadow var(--t-normal);
    text-decoration: none;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
    transition: left .55s ease;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 34px var(--accent-glow);
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
}

.btn-white {
    background: var(--card-solid);
    color: var(--text-primary);
    border: 1px solid var(--card-border);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .6rem;
    font-weight: 600;
    border-radius: var(--radius-md);
    padding: .8rem 1.6rem;
    transition: all .25s;
    box-shadow: var(--shadow-card);
    text-decoration: none;
}

.btn-white:hover {
    transform: translateY(-2px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}

.badge-pill {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: var(--soft-blue);
    color: var(--soft-blue-t);
    border: 1px solid rgba(59,130,246,.2);
    border-radius: 999px;
    padding: .45rem 1rem;
    font-size: .8rem;
    font-weight: 600;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10B981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .55; transform: scale(1.25); }
}

input, select, textarea {
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    color: var(--text-primary);
    padding: .7rem 1rem;
    border-radius: 12px;
    outline: none;
    width: 100%;
    font-size: .9rem;
    transition: all .2s;
}

input:focus, select:focus, textarea:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}

/* ================================================================
   7. КАРТОЧКИ / СТЕКЛО
   ================================================================ */
.card-gradient {
    background: var(--card-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border: 1px solid var(--card-border);
    box-shadow: var(--shadow-card);
    border-radius: var(--radius-lg);
    transition: transform var(--t-normal), box-shadow var(--t-normal), border-color var(--t-normal);
}

.card-hover:hover {
    transform: translateY(-6px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}

.feature-card {
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59,130,246,.08), transparent);
    transition: left .6s ease;
    pointer-events: none;
}

.feature-card:hover::before {
    left: 100%;
}

.info-section {
    background: var(--card-bg);
    backdrop-filter: var(--glass-blur);
    border: 1px solid var(--card-border);
    box-shadow: var(--shadow-card);
    border-radius: var(--radius-xl);
    padding: 3rem;
    position: relative;
    overflow: hidden;
}

.info-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(59,130,246,.5), transparent);
}

.icon-tile {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 6px 16px var(--accent-glow);
    transition: transform .3s;
}

.card-hover:hover .icon-tile {
    transform: scale(1.08) rotate(-3deg);
}

.bullet {
    display: flex;
    gap: .5rem;
    align-items: flex-start;
    font-size: .82rem;
    color: var(--text-secondary);
}

.bullet svg {
    width: 14px;
    height: 14px;
    color: var(--accent-primary);
    flex-shrink: 0;
    margin-top: 3px;
}

.tool-cta {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .85rem;
    font-weight: 700;
    color: var(--accent-primary);
    background: none;
    border: none;
    cursor: pointer;
    transition: gap .2s, color .2s;
}

.tool-cta:hover {
    gap: .7rem;
    color: #8B5CF6;
}

/* ================================================================
   8. ГРАДИЕНТНЫЙ ТЕКСТ / TYPEWRITER
   ================================================================ */
.text-gradient {
    background: var(--accent-gradient);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradMove 7s linear infinite;
}

@keyframes gradMove {
    to { background-position: 200% center; }
}

.type-cursor {
    display: inline-block;
    width: 3px;
    height: 1em;
    background: #8B5CF6;
    margin-left: 4px;
    vertical-align: -0.12em;
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    50% { opacity: 0; }
}

/* ================================================================
   9. HERO: свечение, tilt, плавающие карточки
   ================================================================ */
.hero-glow {
    position: absolute;
    inset: -8%;
    z-index: 0;
    background:
        radial-gradient(50% 50% at 60% 40%, rgba(139,92,246,.22), transparent 70%),
        radial-gradient(40% 40% at 30% 70%, rgba(59,130,246,.18), transparent 70%);
    filter: blur(30px);
    animation: glowB 6s ease-in-out infinite;
}

@keyframes glowB {
    0%, 100% { opacity: .7; }
    50% { opacity: 1; }
}

.hero-img {
    border-radius: 20px;
    box-shadow: 0 24px 70px rgba(15,23,42,.22);
    position: relative;
    z-index: 1;
    transition: transform .2s ease;
}

body.dark-theme .hero-img {
    box-shadow: 0 24px 70px rgba(0,0,0,.5);
}

.stat-badge {
    position: absolute;
    z-index: 2;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: .9rem 1.1rem;
    box-shadow: var(--shadow-hover);
    display: flex;
    align-items: center;
    gap: .8rem;
    animation: float 5s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* ================================================================
   10. TICKER (бегущая строка кодексов)
   ================================================================ */
.ticker {
    overflow: hidden;
    border-top: 1px solid var(--card-border);
    border-bottom: 1px solid var(--card-border);
    background: var(--bg-secondary);
    padding: 14px 0;
}

.ticker-track {
    display: flex;
    gap: 3rem;
    width: max-content;
    animation: tickerMove 32s linear infinite;
}

.ticker:hover .ticker-track {
    animation-play-state: paused;
}

@keyframes tickerMove {
    to { transform: translateX(-50%); }
}

.ticker-item {
    display: flex;
    align-items: center;
    gap: .55rem;
    white-space: nowrap;
    font-size: .85rem;
    font-weight: 500;
    color: var(--text-secondary);
}

/* ================================================================
   11. PROBLEM / SOLUTION
   ================================================================ */
.pain {
    border: 1px solid rgba(244,63,94,.15);
    background: rgba(244,63,94,.04);
    border-radius: 14px;
    padding: 1rem;
    transition: all .25s;
}

.pain:hover {
    border-color: rgba(244,63,94,.35);
    transform: translateX(4px);
}

.win {
    border: 1px solid rgba(16,185,129,.18);
    background: rgba(16,185,129,.05);
    border-radius: 14px;
    padding: 1rem;
    transition: all .25s;
}

.win:hover {
    border-color: rgba(16,185,129,.4);
    transform: translateX(4px);
}

.chip-cost {
    display: inline-block;
    font-size: .72rem;
    font-weight: 700;
    padding: .2rem .6rem;
    border-radius: 8px;
}

/* ================================================================
   12. STEPS / ЛИНИЯ
   ================================================================ */
.step-line {
    height: 2px;
    background: var(--accent-gradient);
    opacity: .35;
    position: relative;
    overflow: hidden;
}

.step-line::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.8), transparent);
    animation: sweep 2.6s ease-in-out infinite;
}

@keyframes sweep {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* ================================================================
   13. ЧAT
   ================================================================ */
.chat-box {
    display: flex;
    flex-direction: column;
    height: 440px;
}

.chat-scroll {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.msg {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    animation: msgIn .3s ease;
}

@keyframes msgIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: none; }
}

.msg.user {
    flex-direction: row-reverse;
}

.msg-ava {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.msg-ava.ai { background: var(--accent-gradient); }
.msg-ava.usr { background: linear-gradient(135deg, #10B981, #059669); }

.msg-b {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 14px;
    font-size: .86rem;
    line-height: 1.55;
    color: var(--text-primary);
}

.msg.ai .msg-b {
    background: var(--soft-blue);
    border: 1px solid rgba(59,130,246,.18);
    border-top-left-radius: 4px;
}

.msg.user .msg-b {
    background: rgba(16,185,129,.08);
    border: 1px solid rgba(16,185,129,.2);
    border-top-right-radius: 4px;
}

.cite-chip {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    margin-top: .5rem;
    font-size: .72rem;
    font-weight: 700;
    color: var(--accent-primary);
    background: rgba(59,130,246,.1);
    border: 1px solid rgba(59,130,246,.2);
    border-radius: 999px;
    padding: .15rem .7rem;
}

.tdot {
    width: 6px;
    height: 6px;
    background: var(--text-muted);
    border-radius: 50%;
    display: inline-block;
    animation: tb 1.4s infinite;
}

.tdot:nth-child(2) { animation-delay: .15s; }
.tdot:nth-child(3) { animation-delay: .3s; }

@keyframes tb {
    0%, 60%, 100% { transform: translateY(0); opacity: .4; }
    30% { transform: translateY(-7px); opacity: 1; }
}

.chip {
    padding: .45rem 1rem;
    background: var(--soft-blue);
    border: 1px solid rgba(59,130,246,.25);
    border-radius: 999px;
    color: var(--soft-blue-t);
    font-size: .8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
}

.chip:hover {
    border-color: var(--accent-primary);
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(59,130,246,.15);
}

/* ================================================================
   14. DASHBOARD: upload / diff / timeline / sidebar
   ================================================================ */
.upload-zone {
    border: 2px dashed rgba(59,130,246,.4);
    border-radius: 16px;
    cursor: pointer;
    transition: all .25s;
}

.upload-zone:hover, .upload-zone.dragover {
    border-color: var(--accent-primary);
    background: rgba(59,130,246,.05);
}

.diff-added {
    background: rgba(16,185,129,.08);
    border-left: 3px solid var(--success);
    padding: .6rem .9rem;
    margin: .5rem 0;
    border-radius: 0 10px 10px 0;
    font-size: .85rem;
}

.diff-removed {
    background: rgba(239,68,68,.08);
    border-left: 3px solid var(--error);
    padding: .6rem .9rem;
    margin: .5rem 0;
    border-radius: 0 10px 10px 0;
    font-size: .85rem;
    text-decoration: line-through;
    opacity: .75;
}

.diff-modified {
    background: rgba(245,158,11,.08);
    border-left: 3px solid var(--warning);
    padding: .6rem .9rem;
    margin: .5rem 0;
    border-radius: 0 10px 10px 0;
    font-size: .85rem;
}

.sidebar-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .65rem .9rem;
    border-radius: 12px;
    border: none;
    background: none;
    color: var(--text-secondary);
    font-size: .85rem;
    font-weight: 500;
    cursor: pointer;
    text-align: left;
    transition: all .2s;
    border-left: 3px solid transparent;
}

.sidebar-btn:hover {
    background: var(--soft-blue);
    color: var(--text-primary);
}

.sidebar-btn.active {
    background: var(--soft-blue);
    color: var(--accent-primary);
    border-left-color: var(--accent-primary);
}

.progress-fill {
    height: 100%;
    background: var(--accent-gradient);
    border-radius: 999px;
    transition: width .3s;
    width: 0%;
}

.risk-high { background: linear-gradient(135deg, #EF4444, #DC2626); }
.risk-medium { background: linear-gradient(135deg, #F59E0B, #D97706); }
.risk-low { background: linear-gradient(135deg, #10B981, #059669); }

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, var(--accent-primary), var(--card-border));
}

.timeline-item {
    position: relative;
    padding-bottom: 24px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--accent-primary);
    border: 2px solid var(--bg-primary);
}

.timeline-item.completed::before { background: var(--success); }
.timeline-item.warning::before { background: var(--warning); }

/* ================================================================
   15. FAQ / PRICING / TESTIMONIALS
   ================================================================ */
.faq-item {
    border: 1px solid var(--card-border);
    border-radius: 14px;
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: all .25s;
}

.faq-item:hover {
    border-color: var(--card-hover-border);
}

.faq-q {
    width: 100%;
    padding: 1.1rem 1.25rem;
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: .95rem;
    font-weight: 600;
    text-align: left;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.faq-q svg {
    width: 18px;
    height: 18px;
    color: var(--accent-primary);
    transition: transform .3s;
    flex-shrink: 0;
}

.faq-item.active .faq-q svg {
    transform: rotate(180deg);
}

.faq-a {
    max-height: 0;
    overflow: hidden;
    padding: 0 1.25rem;
    font-size: .875rem;
    color: var(--text-secondary);
    transition: max-height .35s, padding .35s;
}

.faq-item.active .faq-a {
    max-height: 400px;
    padding: 0 1.25rem 1.25rem;
}

.pricing-card {
    position: relative;
    transition: all var(--t-slow);
}

.pricing-card:hover {
    transform: translateY(-10px);
    border-color: var(--accent-primary);
    box-shadow: var(--shadow-hover);
}

.pricing-card.popular {
    border-color: var(--accent-primary);
    box-shadow: 0 0 40px var(--accent-glow);
}

.testimonial-card {
    transition: all var(--t-normal);
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

/* ================================================================
   16. THEME TOGGLE / FAB / TOAST / MODAL / PALETTE
   ================================================================ */
.theme-toggle {
    position: fixed;
    top: 96px;
    right: 28px;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 10px 30px var(--accent-glow);
    z-index: 9998;
    transition: all .3s;
    border: none;
}

.theme-toggle:hover {
    transform: scale(1.1) rotate(15deg);
}

.theme-toggle svg {
    width: 22px;
    height: 22px;
    color: #fff;
}

.theme-toggle .sun { display: block; }
.theme-toggle .moon { display: none; }
body.dark-theme .theme-toggle .sun { display: none; }
body.dark-theme .theme-toggle .moon { display: block; }

.fab {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: var(--accent-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 10px 30px var(--accent-glow);
    z-index: 9998;
    border: none;
    animation: float 4.5s ease-in-out infinite;
    transition: all .3s;
}

.fab:hover {
    transform: scale(1.1);
}

.fab svg {
    width: 24px;
    height: 24px;
    color: #fff;
}

.toast {
    position: fixed;
    bottom: 100px;
    right: 28px;
    padding: 1rem 1.4rem;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: .8rem;
    box-shadow: var(--shadow-hover);
    transform: translateX(130%);
    transition: transform .35s;
    z-index: 10002;
    color: var(--text-primary);
    font-size: .9rem;
    max-width: 90vw;
}

.toast.show { transform: none; }
.toast.success { border-color: var(--success); }
.toast.error { border-color: var(--error); }
.toast.warning { border-color: var(--warning); }
.toast.info { border-color: var(--accent-primary); }

.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 10000;
    background: rgba(15,23,42,.5);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-overlay.hidden { display: none; }

.modal-box {
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 20px;
    max-width: 760px;
    width: 100%;
    max-height: 88vh;
    overflow-y: auto;
    animation: scaleIn .3s ease;
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(.95); }
    to { opacity: 1; transform: scale(1); }
}

.palette {
    position: fixed;
    inset: 0;
    z-index: 10003;
    background: rgba(15,23,42,.5);
    backdrop-filter: blur(6px);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 12vh 20px 20px;
}

.palette.hidden { display: none; }

.palette-box {
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    max-width: 560px;
    width: 100%;
    overflow: hidden;
    box-shadow: var(--shadow-hover);
    animation: scaleIn .25s ease;
}

.command-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .8rem 1.1rem;
    cursor: pointer;
    color: var(--text-secondary);
    font-size: .9rem;
}

.command-item.focused, .command-item:hover {
    background: var(--soft-blue);
    color: var(--text-primary);
}

.command-item kbd {
    font-size: .7rem;
    border: 1px solid var(--card-border);
    border-radius: 6px;
    padding: .1rem .45rem;
    color: var(--text-muted);
}

/* ================================================================
   17. REVEAL / СЕКЦИИ / ПРОЧЕЕ
   ================================================================ */
.reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity .8s cubic-bezier(.4,0,.2,1), transform .8s cubic-bezier(.4,0,.2,1);
}

.reveal.active {
    opacity: 1;
    transform: none;
}

.rd1 { transition-delay: .1s; }
.rd2 { transition-delay: .2s; }
.rd3 { transition-delay: .3s; }
.rd4 { transition-delay: .4s; }

.hidden-section { display: none; }

.active-section {
    display: block;
    animation: fadeIn .4s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.hide-scrollbar::-webkit-scrollbar { display: none; }

footer {
    border-top: 1px solid var(--card-border);
    background: var(--bg-secondary);
}

.dash-tab { display: none; }
.dash-tab.active { display: block; animation: fadeIn .35s ease; }

@media (max-width: 768px) {
    .theme-toggle { top: 80px; right: 14px; width: 46px; height: 46px; }
    .fab { bottom: 18px; right: 18px; width: 52px; height: 52px; }
    #backToTop { bottom: 84px; right: 20px; }
    .toast { right: 14px; left: 14px; bottom: 84px; }
    .info-section { padding: 2rem 1.5rem; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
    html { scroll-behavior: auto; }
    .reveal { opacity: 1; transform: none; }
}
</style>
</head>

<body>
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[10004] focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">{{ app()->getLocale() === 'tg' ? 'Ба мундариҷа гузаштан' : (app()->getLocale() === 'en' ? 'Skip to content' : 'Перейти к контенту') }}</a>

<div id="scrollProgress" aria-hidden="true"></div>
<div class="bg-grid" aria-hidden="true"></div>

<div class="particles" aria-hidden="true">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<!-- Theme toggle -->
<button class="theme-toggle" onclick="toggleTheme()" role="switch" aria-label="{{ app()->getLocale() === 'en' ? 'Toggle theme' : (app()->getLocale() === 'tg' ? 'Иваз кардани мавзӯъ' : 'Переключить тему') }}">
    <svg class="sun" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['sun'] !!}</svg>
    <svg class="moon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['moon'] !!}</svg>
</button>

<!-- ==================== NAV ==================== -->
<nav role="navigation" x-data="{ lang: false, bell: false, mobile: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <button onclick="showSection('landing'); window.scrollTo({top:0,behavior:'smooth'})" class="flex items-center gap-2.5">
                <svg class="w-8 h-8" style="color:#3B82F6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">{!! $icons['shield'] !!}</svg>
                <span class="text-xl font-extrabold tracking-tight c1">LegalAI Pro</span>
            </button>

            <div class="hidden lg:flex items-center gap-8">
                <button onclick="showSection('landing')" class="nav-link">{{ $L['nav']['home'] }}</button>
                <button onclick="showSection('dashboard')" class="nav-link">{{ $L['nav']['dash'] }}</button>
                <button onclick="openTool('t5')" class="nav-link">{{ $L['nav']['calc'] }}</button>
                <button onclick="openTool('t4')" class="nav-link">{{ $L['nav']['chat'] }}</button>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Language -->
                <div class="relative hidden sm:block">
                    <button x-on:click="lang = !lang" class="lang-btn" aria-haspopup="true" :aria-expanded="lang ? 'true' : 'false'">
                        {{ strtoupper(app()->getLocale()) }}
                        <svg class="w-3.5 h-3.5 transition-transform" :class="lang && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['chevron'] !!}</svg>
                    </button>
                    <div x-show="lang" x-on:click.outside="lang = false" class="dropdown-panel !w-28 p-1.5">
                        @foreach(['ru' => 'RU', 'tg' => 'TJ', 'en' => 'EN'] as $code => $label)
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="block px-3 py-2 rounded-lg text-xs font-bold transition hover:bg-blue-500/10 {{ app()->getLocale() === $code ? 'text-blue-500' : 'c2' }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Notifications -->
                <div class="relative hidden sm:block">
                    <button x-on:click="bell = !bell" class="bell-btn" aria-label="{{ $L['nav']['notif'] }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['bell'] !!}</svg>
                        <span class="bell-badge">3</span>
                    </button>
                    <div x-show="bell" x-on:click.outside="bell = false" class="dropdown-panel !w-80">
                        <div class="p-4 border-b" style="border-color: var(--card-border);">
                            <h4 class="font-bold text-sm c1">{{ $L['nav']['notif'] }}</h4>
                        </div>
                        @foreach([['#3B82F6', 0], ['#F59E0B', 1], ['#10B981', 2]] as $n)
                            <div class="p-4 flex items-start gap-3 transition hover:bg-blue-500/5 {{ $n[1] < 2 ? 'border-b' : '' }}" style="border-color: var(--card-border);">
                                <span class="w-2 h-2 rounded-full mt-1.5" style="background: {{ $n[0] }}"></span>
                                <div>
                                    <p class="text-sm c1">{{ $L['js']['done'] }}</p>
                                    <p class="text-xs c3 mt-0.5">{{ ['2 ' . ($L['js']['months'][0] ?? '') , '1h', '24h'][$n[1]] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('login') }}" class="btn-primary !px-6 !py-2.5 text-sm hidden sm:inline-flex">{{ $L['nav']['login'] }}</a>

                <button x-on:click="mobile = !mobile" class="lg:hidden bell-btn" aria-label="{{ $L['nav']['menu'] }}" :aria-expanded="mobile ? 'true' : 'false'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['menu'] !!}</svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobile" class="lg:hidden border-t px-4 py-4 space-y-1" style="border-color: var(--card-border); background: var(--bg-secondary);">
        <button onclick="showSection('landing')" class="sidebar-btn">{{ $L['nav']['home'] }}</button>
        <button onclick="showSection('dashboard')" class="sidebar-btn">{{ $L['nav']['dash'] }}</button>
        <button onclick="openTool('t5')" class="sidebar-btn">{{ $L['nav']['calc'] }}</button>
        <button onclick="openTool('t4')" class="sidebar-btn">{{ $L['nav']['chat'] }}</button>
        <button onclick="openPalette()" class="sidebar-btn">⌘K {{ $L['nav']['search'] }}</button>
        <div class="pt-3 flex gap-2">
            @foreach(['ru' => 'RU', 'tg' => 'TJ', 'en' => 'EN'] as $code => $label)
                <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="lang-btn flex-1 justify-center">{{ $label }}</a>
            @endforeach
        </div>
        <a href="{{ route('login') }}" class="btn-primary w-full !py-2.5 text-sm">{{ $L['nav']['login'] }}</a>
    </div>
</nav>

<main id="main-content">
<!-- ==================== LANDING ==================== -->
<div id="landingSection" class="active-section">

    <!-- HERO -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-14 items-center">
            <div class="reveal active">
                <div class="badge-pill mb-7"><span class="dot" aria-hidden="true"></span>{{ $L['hero']['badge'] }}</div>
                <h1 class="text-4xl md:text-5xl xl:text-[3.5rem] font-extrabold leading-[1.12] mb-4">
                    {{ $L['hero']['title1'] }}<br>
                    <span class="text-gradient">{{ $L['hero']['title2'] }}</span>
                </h1>
                <p class="text-lg md:text-xl font-semibold mb-6 c2">
                    <span id="typeTarget" class="text-gradient"></span><span class="type-cursor" aria-hidden="true"></span>
                </p>
                <p class="text-base md:text-lg leading-relaxed mb-9 max-w-xl c2">{{ $L['hero']['sub'] }}</p>

                <div class="flex flex-wrap items-center gap-4 mb-9">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['upload'] !!}</svg>
                            {{ $L['hero']['cta1'] }}
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['upload'] !!}</svg>
                            {{ $L['hero']['cta1'] }}
                        </a>
                    @endauth
                    <button onclick="openModal('demoModal')" class="btn-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['play'] !!}</svg>
                        {{ $L['hero']['cta2'] }}
                    </button>
                </div>

                <div class="flex flex-wrap gap-x-8 gap-y-3">
                    @foreach($L['hero']['trust'] as $t)
                        <div class="flex items-center gap-2 text-sm c2">
                            <svg class="w-5 h-5" style="color:#10B981" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
                            <span>{{ $t }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hero visual -->
            <div class="relative reveal active rd2" aria-hidden="true">
                <div class="hero-glow"></div>
                <img id="heroImg" src="https://image.qwenlm.ai/public_source/48f99b9f-6493-4401-a338-927a8c873e1a/1269011f0-818d-4497-9ee0-8c8efb1a3788.png" alt="Legal AI" class="hero-img w-full object-cover">
                <div class="stat-card stat-badge -bottom-5 -left-3 md:-left-6">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#ECFDF5; color:#10B981;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['bolt'] !!}</svg>
                    </span>
                    <div>
                        <div class="text-lg font-extrabold c1"><span class="counter" data-count="60">0</span> {{ app()->getLocale() === 'en' ? 'sec' : (app()->getLocale() === 'tg' ? 'сония' : 'сек') }}</div>
                        <div class="text-xs c3">{{ $L['hero']['stat1'] }}</div>
                    </div>
                </div>
                <div class="stat-card stat-badge -top-5 -right-3 md:-right-6" style="animation-delay: 1.2s;">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#EFF4FF; color:#3B82F6;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
                    </span>
                    <div>
                        <div class="text-lg font-extrabold c1"><span class="counter" data-count="95">0</span>%</div>
                        <div class="text-xs c3">{{ $L['hero']['stat2'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TICKER -->
    <div class="ticker reveal" aria-hidden="true">
        <div class="ticker-track">
            @foreach([0,1] as $d)
                @foreach($L['ticker'] as $item)
                    <span class="ticker-item">
                        <svg class="w-4 h-4" style="color:#3B82F6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['scale'] !!}</svg>
                        {{ $item }}
                    </span>
                @endforeach
            @endforeach
        </div>
    </div>

    <!-- STATS BAND -->
    <section class="py-16 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($L['stats'] as $i => $s)
                <div class="card-gradient card-hover p-6 text-center reveal rd{{ $i + 1 }}">
                    <div class="text-3xl md:text-4xl font-extrabold text-gradient">
                        <span class="counter" data-count="{{ $s['v'] }}" data-decimals="{{ $s['d'] ?? 0 }}">0</span>{{ $s['s'] }}
                    </div>
                    <div class="text-sm mt-1 c2">{{ $s['l'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- PROBLEM / SOLUTION -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl mx-auto text-center mb-14">
                <div class="badge-pill mb-4" style="background:rgba(244,63,94,.08); color:#F43F5E; border-color:rgba(244,63,94,.2);"><span class="dot" style="background:#F43F5E"></span>{{ $L['prob']['badge'] }}</div>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['prob']['title'] }} <span class="text-gradient">{{ $L['prob']['accent'] }}</span></h2>
                <p class="text-lg c2">{{ $L['prob']['sub'] }}</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-6">
                <div class="card-gradient p-7 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(244,63,94,.1); color:#F43F5E;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['warning'] !!}</svg>
                        </span>
                        <div>
                            <div class="text-[11px] uppercase tracking-widest font-bold" style="color:#F43F5E;">{{ $L['prob']['w_label'] }}</div>
                            <div class="font-bold c1">{{ $L['prob']['w_title'] }}</div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($L['prob']['problems'] as $p)
                            <div class="pain">
                                <div class="flex gap-3">
                                    <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0" style="background:rgba(244,63,94,.12); color:#F43F5E;">✕</span>
                                    <div>
                                        <div class="font-bold text-sm c1">{{ $p['pain'] }}</div>
                                        <p class="text-[13px] mt-1 c2">{{ $p['detail'] }}</p>
                                        <span class="chip-cost mt-2" style="background:rgba(244,63,94,.1); color:#E11D48;">{{ $p['cost'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-gradient p-7 md:p-8 relative" style="border-color: rgba(16,185,129,.3);">
                    <span class="absolute -top-3 right-6 text-[11px] font-bold text-white px-3 py-1 rounded-full" style="background:linear-gradient(135deg,#10B981,#0D9488);">✦ {{ $L['prob']['a_label'] }}</span>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background:linear-gradient(135deg,#10B981,#059669);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
                        </span>
                        <div>
                            <div class="text-[11px] uppercase tracking-widest font-bold" style="color:#10B981;">{{ $L['prob']['a_label'] }}</div>
                            <div class="font-bold c1">{{ $L['prob']['a_title'] }}</div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($L['prob']['solutions'] as $s)
                            <div class="win">
                                <div class="flex gap-3">
                                    <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center text-white text-xs shrink-0" style="background:#10B981;">✓</span>
                                    <div>
                                        <div class="font-bold text-sm c1">{{ $s['win'] }}</div>
                                        <p class="text-[13px] mt-1 c2">{{ $s['detail'] }}</p>
                                        <span class="chip-cost mt-2" style="background:rgba(16,185,129,.12); color:#059669;">{{ $s['metric'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="#demo" class="btn-primary w-full mt-6 !py-3 text-sm" style="background:linear-gradient(135deg,#10B981,#0D9488);">{{ $L['prob']['cta'] }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 8 TOOLS -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="badge-pill mb-4"><span class="dot"></span>{{ $L['tools']['badge'] }}</div>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['tools']['title'] }} <span class="text-gradient">{{ $L['tools']['accent'] }}</span></h2>
                <p class="text-lg c2">{{ $L['tools']['sub'] }}</p>
            </div>
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5">
                @foreach($L['tools']['items'] as $i => $tool)
                    <div class="card-gradient card-hover feature-card p-6 flex flex-col reveal rd{{ ($i % 4) + 1 }}">
                        <div class="icon-tile mb-4 bg-gradient-to-br {{ ['from-blue-500 to-indigo-500','from-violet-500 to-purple-500','from-amber-500 to-orange-500','from-sky-500 to-cyan-500','from-emerald-500 to-teal-500','from-fuchsia-500 to-pink-500','from-rose-500 to-red-500','from-indigo-500 to-blue-500'][$i] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons[$toolIcons[$i]] !!}</svg>
                        </div>
                        <h3 class="text-[15px] font-bold mb-1.5">{{ $tool['t'] }}</h3>
                        <p class="text-[13px] mb-4 c2">{{ $tool['d'] }}</p>
                        <ul class="space-y-2 mb-4 flex-1">
                            @foreach($tool['b'] as $b)
                                <li class="bullet"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg><span>{{ $b }}</span></li>
                            @endforeach
                        </ul>
                        @isset($tool['note'])
                            <p class="text-[11px] mb-3 pt-2 border-t" style="color:var(--warning); border-color:var(--card-border);">⚠️ {{ $tool['note'] }}</p>
                        @endisset
                        <button onclick="openTool('t{{ $i + 1 }}')" class="tool-cta">
                            {{ $L['tools']['cta'] }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['arrow'] !!}</svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="py-24 px-4 sm:px-6 reveal">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['how']['title'] }} <span class="text-gradient">{{ $L['how']['accent'] }}</span></h2>
                <p class="text-lg c2">{{ $L['how']['sub'] }}</p>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute top-7 left-[18%] right-[18%] step-line"></div>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($L['how']['steps'] as $i => $step)
                        <div class="card-gradient card-hover p-8 text-center relative reveal rd{{ $i + 1 }}">
                            <div class="w-14 h-14 mx-auto mb-6 rounded-2xl flex items-center justify-center text-white text-lg font-extrabold relative z-10 bg-gradient-to-br {{ ['from-blue-500 to-indigo-500','from-violet-500 to-purple-500','from-emerald-500 to-teal-500'][$i] }}" style="box-shadow:0 8px 20px var(--accent-glow);">{{ $i + 1 }}</div>
                            <h3 class="text-base font-bold mb-2">{{ $step['title'] }}</h3>
                            <p class="text-[13px] mb-5 c2">{{ $step['desc'] }}</p>
                            <ul class="space-y-2">
                                @foreach($step['d'] as $d)
                                    <li class="bullet justify-center"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg><span>{{ $d }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE DEMO CHAT -->
    <section id="demo" class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="badge-pill mb-4"><span class="dot"></span>{{ $L['demo']['badge'] }}</div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['demo']['title'] }}<br><span class="text-gradient">{{ $L['demo']['accent'] }}</span></h2>
                <p class="text-base mb-8 c2">{{ $L['demo']['sub'] }}</p>
                <div x-data="chatDemo" class="space-y-2.5">
                    <template x-for="(item, idx) in quick" :key="idx">
                        <button x-on:click="ask(item)" class="chip block w-full sm:w-auto" x-text="item.q"></button>
                    </template>
                    <p class="text-xs pt-2 c3">{{ $L['demo']['hint'] }}</p>
                </div>
            </div>
            <div x-data="chatDemo" class="card-gradient overflow-hidden reveal rd2">
                <div class="flex items-center gap-3 px-5 py-4 border-b" style="border-color: var(--card-border);">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:var(--accent-gradient);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['scale'] !!}</svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold c1">{{ $L['tools']['items'][3]['t'] }}</p>
                        <p class="text-[11px]" style="color:#10B981;">● online</p>
                    </div>
                </div>
                <div class="chat-box">
                    <div class="chat-scroll" x-ref="demoScroll">
                        <template x-for="(m, idx) in messages" :key="idx">
                            <div class="msg" :class="m.who">
                                <div class="msg-ava" :class="m.who === 'ai' ? 'ai' : 'usr'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" x-show="m.who === 'ai'">{!! $icons['scale'] !!}</svg>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" x-show="m.who === 'user'">{!! $icons['briefcase'] !!}</svg>
                                </div>
                                <div class="msg-b">
                                    <template x-if="m.lines"><span>
                                        <template x-for="(line, li) in m.lines" :key="li"><span x-text="line" class="block mb-2 last:mb-0"></span></template>
                                        <span x-show="m.cite" class="cite-chip"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['book'] !!}</svg><span x-text="m.cite"></span></span>
                                    </span></template>
                                    <template x-if="m.text"><span x-text="m.text"></span></template>
                                </div>
                            </div>
                        </template>
                        <div x-show="typing" class="msg ai">
                            <div class="msg-ava ai"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['scale'] !!}</svg></div>
                            <div class="msg-b"><span class="tdot"></span> <span class="tdot"></span> <span class="tdot"></span><span class="block text-[11px] mt-1.5 c3" x-text="typingLabel"></span></div>
                        </div>
                    </div>
                </div>
                <p class="text-[11px] px-5 py-3 border-t c3" style="border-color: var(--card-border);">{{ $L['demo']['note'] }}</p>
            </div>
        </div>
    </section>

    <!-- DOCS SLIDER -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['docs']['title'] }}</h2>
                <p class="text-lg c2">{{ $L['docs']['sub'] }}</p>
            </div>
            <div class="relative">
                <div id="sliderTrack" class="flex gap-5 overflow-x-auto hide-scrollbar py-3 px-1 scroll-smooth">
                    @foreach($L['docs']['types'] as $i => $type)
                        <div class="card-gradient card-hover shrink-0 w-60 p-6 text-center">
                            <div class="w-16 h-16 {{ ['bg-blue-50 text-blue-500','bg-amber-50 text-amber-500','bg-emerald-50 text-emerald-500','bg-violet-50 text-violet-500','bg-rose-50 text-rose-500','bg-cyan-50 text-cyan-500'][$i] }} rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons[$docIcons[$i]] !!}</svg>
                            </div>
                            <h3 class="font-bold text-sm mb-1">{{ $type[0] }}</h3>
                            <p class="text-xs c3">{{ $type[1] }}</p>
                        </div>
                    @endforeach
                </div>
                <button onclick="scrollSlider(-1)" class="hidden md:flex absolute -left-4 top-1/2 -translate-y-1/2 btn-white !p-3 !rounded-full z-10" aria-label="←">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="scrollSlider(1)" class="hidden md:flex absolute -right-4 top-1/2 -translate-y-1/2 btn-white !p-3 !rounded-full z-10" aria-label="→">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </section>

    <!-- EXAMPLES -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4 text-gradient">{{ $L['examples']['title'] }}</h2>
                <p class="text-lg c2">{{ $L['examples']['sub'] }}</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($L['examples']['items'] as $i => $ex)
                    <div class="card-gradient card-hover feature-card p-6 reveal rd{{ ($i % 2) + 1 }}">
                        <div class="flex items-start gap-4">
                            <span class="w-12 h-12 shrink-0 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white shadow-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons[$docIcons[$i]] !!}</svg>
                            </span>
                            <div>
                                <h3 class="text-lg font-bold mb-2">{{ $ex['t'] }}</h3>
                                <p class="text-sm leading-relaxed c2">{{ $ex['x'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="py-16 px-4 sm:px-6 reveal">
        <div class="max-w-4xl mx-auto info-section">
            <h2 class="text-2xl md:text-4xl font-extrabold text-center text-gradient mb-8">{{ $L['about']['title'] }}</h2>
            <div class="space-y-5 text-[15px] leading-relaxed c2">
                <p><strong class="c1">LegalAI Pro</strong> — {{ $L['about']['p1'] }}</p>
                <p>{{ $L['about']['p2'] }}</p>
                <div class="flex gap-3 p-4 rounded-xl" style="background:rgba(245,158,11,.06); border:1px solid rgba(245,158,11,.3);">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:var(--warning);" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['warning'] !!}</svg>
                    <p class="text-sm c1">{{ $L['about']['p3'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['testimonials']['title'] }}</h2>
                <p class="text-lg c2">{{ $L['testimonials']['sub'] }}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($L['testimonials']['items'] as $i => $t)
                    <div class="card-gradient testimonial-card p-6 reveal rd{{ $i + 1 }}">
                        <div class="flex gap-1 mb-4" aria-hidden="true">
                            @for($s = 0; $s < 5; $s++)
                                <svg class="w-4 h-4" style="color:#F59E0B" fill="currentColor" viewBox="0 0 24 24">{!! $icons['star'] !!}</svg>
                            @endfor
                        </div>
                        <p class="text-sm leading-relaxed mb-5 c2">«{{ $t['x'] }}»</p>
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">{{ mb_substr($t['n'], 0, 1) }}</span>
                            <div>
                                <div class="text-sm font-bold c1">{{ $t['n'] }}</div>
                                <div class="text-xs c3">{{ $t['r'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['pricing']['title'] }}</h2>
                <p class="text-lg c2">{{ $L['pricing']['sub'] }}</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6 items-stretch">
                @foreach($L['pricing']['items'] as $i => $p)
                    <div class="card-gradient pricing-card p-7 flex flex-col {{ $i === 1 ? 'popular' : '' }} reveal rd{{ $i + 1 }}">
                        @if($i === 1)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[11px] font-bold text-white px-3 py-1 rounded-full" style="background:var(--accent-gradient);">{{ $L['pricing']['popular'] }}</span>
                        @endif
                        <h3 class="text-lg font-bold mb-2">{{ $p['n'] }}</h3>
                        <div class="mb-5">
                            <span class="text-4xl font-extrabold c1">{{ $p['p'] }}</span>
                            <span class="text-sm c3"> {{ $p['per'] }}</span>
                        </div>
                        <ul class="space-y-2.5 mb-7 flex-1">
                            @foreach($p['f'] as $f)
                                <li class="bullet"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg><span>{{ $f }}</span></li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="{{ $i === 1 ? 'btn-primary' : 'btn-white' }} w-full !py-2.5 text-sm">{{ $p['c'] }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 px-4 sm:px-6 reveal" x-data="{ open: 0 }">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <div class="badge-pill mb-4">{{ $L['faq']['badge'] }}</div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3">{{ $L['faq']['title'] }} <span class="text-gradient">{{ $L['faq']['accent'] }}</span></h2>
                <p class="c2">{{ $L['faq']['sub'] }}</p>
            </div>
            <div class="space-y-3">
                @foreach($L['faq']['items'] as $i => $item)
                    <div class="faq-item" :class="{ 'active': open === {{ $i }} }">
                        <button class="faq-q" x-on:click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }} ? 'true' : 'false'">
                            <span>{{ $item['q'] }}</span>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['chevron'] !!}</svg>
                        </button>
                        <div class="faq-a"><p>{{ $item['a'] }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- NOTICE -->
    <section class="py-16 px-4 sm:px-6 reveal">
        <div class="max-w-4xl mx-auto card-gradient p-8 md:p-10 text-center" style="border-color: rgba(245,158,11,.35);">
            <div class="flex items-center justify-center gap-3 mb-4">
                <svg class="w-8 h-8" style="color:var(--warning);" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['warning'] !!}</svg>
                <h3 class="text-xl font-extrabold" style="color:var(--warning);">{{ $L['notice']['title'] }}</h3>
            </div>
            <p class="leading-relaxed c2">{{ $L['notice']['text'] }}</p>
            <p class="leading-relaxed c2 mt-3">{{ $L['notice']['text2'] }}</p>
        </div>
    </section>

    <!-- CTA BANNER -->
    <section class="pb-24 px-4 sm:px-6 reveal">
        <div class="max-w-5xl mx-auto info-section text-center" style="border-color: rgba(59,130,246,.3);">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-3">{{ $L['cta']['title'] }}</h2>
            <p class="text-lg mb-8 c2">{{ $L['cta']['sub'] }}</p>
            <a href="{{ route('register') }}" class="btn-primary !px-10 !py-4 text-lg">{{ $L['cta']['btn'] }}</a>
        </div>
    </section>
</div>

<!-- ==================== DASHBOARD ==================== -->
<div id="dashboardSection" class="hidden-section pt-24 pb-12 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <aside class="w-full lg:w-64 shrink-0">
            <div class="card-gradient p-3 lg:sticky lg:top-24">
                <button onclick="switchDashboardTab('upload')" data-tab="upload" class="sidebar-btn active">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['upload'] !!}</svg>
                    <span>{{ $L['dash']['upload'] }}</span>
                </button>
                <button onclick="switchDashboardTab('analysis')" data-tab="analysis" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['calc'] !!}</svg>
                    <span>{{ $L['dash']['analytics'] }}</span>
                </button>
                <button onclick="switchDashboardTab('chat')" data-tab="chat" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['chat'] !!}</svg>
                    <span>{{ $L['dash']['assistant'] }}</span>
                </button>
                <button onclick="switchDashboardTab('compare')" data-tab="compare" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['compare'] !!}</svg>
                    <span>{{ $L['dash']['compare'] }}</span>
                </button>
                <button onclick="switchDashboardTab('history')" data-tab="history" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['clock'] !!}</svg>
                    <span>{{ $L['dash']['history'] }}</span>
                </button>
                <button onclick="switchDashboardTab('settings')" data-tab="settings" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['globe'] !!}</svg>
                    <span>{{ $L['dash']['settings'] }}</span>
                </button>
                <div class="my-2 h-px" style="background:var(--card-border);"></div>
                <button onclick="showSection('landing')" class="sidebar-btn">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['home'] !!}</svg>
                    <span>{{ $L['nav']['home'] }}</span>
                </button>
                <div class="pt-3">
                    <button onclick="openModal('tplModal')" class="btn-primary w-full !py-2.5 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        {{ $L['dash']['new_tpl'] }}
                    </button>
                </div>
            </div>
        </aside>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <!-- Upload -->
            <div id="tab-upload" class="dash-tab active">
                <div class="card-gradient p-6 md:p-8">
                    <h2 class="text-xl font-extrabold mb-6">{{ $L['dash']['up_title'] }}</h2>
                    <div id="dropZone" class="upload-zone p-10 text-center">
                        <input type="file" id="fileInput" class="hidden" accept=".pdf,.docx,.txt">
                        <svg class="w-12 h-12 mx-auto mb-3 c3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $icons['upload'] !!}</svg>
                        <p class="font-bold text-sm mb-1 c1">{{ $L['dash']['drop'] }}</p>
                        <p class="text-xs mb-4 c2">{{ $L['dash']['or'] }}</p>
                        <button class="btn-primary !py-2.5 text-sm">{{ $L['dash']['choose'] }}</button>
                    </div>
                    <div id="fileInfo" class="hidden mt-4 card-gradient p-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" style="background:#EFF4FF; color:#3B82F6;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['doc'] !!}</svg></span>
                            <div class="min-w-0"><p id="fileName" class="text-sm font-bold truncate c1">document.docx</p><p id="fileSize" class="text-xs c3">—</p></div>
                        </div>
                        <button onclick="removeFile()" class="bell-btn" aria-label="✕"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['x'] !!}</svg></button>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button id="analyzeBtn" onclick="startAnalysis()" disabled class="btn-primary text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['search'] !!}</svg>
                            {{ $L['dash']['analyze'] }}
                        </button>
                    </div>
                    <div id="analysisProgress" class="hidden mt-8">
                        <p class="text-sm font-bold mb-3 c1">{{ $L['dash']['progress'] }}</p>
                        <div class="h-1.5 rounded-full overflow-hidden mb-3" style="background:var(--card-border);"><div id="progressBar" class="progress-fill"></div></div>
                        <div class="grid grid-cols-3 gap-2 text-center text-[11px] c3">
                            <div>{{ $L['dash']['st1'] }}</div>
                            <div>{{ $L['dash']['st2'] }}</div>
                            <div>{{ $L['dash']['st3'] }}</div>
                        </div>
                    </div>
                    <div id="analysisResults" class="hidden mt-8 space-y-5">
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div class="card-gradient p-5 text-center"><p class="text-xs mb-2 c3">{{ $L['dash']['overall'] }}</p><span id="overallRisk" class="text-xs font-bold px-3 py-1.5 rounded-full risk-medium" style="color:#fff;">—</span></div>
                            <div class="card-gradient p-5 text-center"><p class="text-xs mb-1 c3">{{ $L['dash']['found'] }}</p><p class="text-2xl font-extrabold" style="color:var(--error);">4</p></div>
                            <div class="card-gradient p-5 text-center"><p class="text-xs mb-1 c3">{{ $L['dash']['time'] }}</p><p class="text-2xl font-extrabold" style="color:var(--success);">58 {{ app()->getLocale() === 'en' ? 'sec' : (app()->getLocale() === 'tg' ? 'сония' : 'сек') }}</p></div>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div class="card-gradient p-5"><h3 class="text-sm font-bold mb-3">{{ $L['dash']['chart'] }}</h3><div style="height:220px;"><canvas id="riskChart" role="img" aria-label="{{ $L['dash']['chart'] }}"></canvas></div></div>
                            <div class="card-gradient p-5"><h3 class="text-sm font-bold mb-3">{{ $L['dash']['problems'] }}</h3><div id="riskList" class="space-y-2.5 max-h-[220px] overflow-y-auto pr-1"></div></div>
                        </div>
                        <div class="card-gradient p-5">
                            <h3 class="text-sm font-bold mb-3">{{ $L['dash']['recs'] }}</h3>
                            <ul id="recommendations" class="space-y-2"></ul>
                            <div class="flex flex-wrap gap-2 mt-5">
                                <button onclick="showToast('success', T.sent)" class="btn-white !py-2 text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['download'] !!}</svg>{{ $L['dash']['dl'] }}</button>
                                <button onclick="showToast('success', T.sent)" class="btn-primary !py-2 text-sm">{{ $L['dash']['mail'] }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div id="tab-analysis" class="dash-tab">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="card-gradient p-6"><h3 class="text-sm font-bold mb-4">{{ $L['dash']['act'] }}</h3><div style="height:260px;"><canvas id="analyticsChart1"></canvas></div></div>
                    <div class="card-gradient p-6"><h3 class="text-sm font-bold mb-4">{{ $L['dash']['types'] }}</h3><div style="height:260px;"><canvas id="analyticsChart2"></canvas></div></div>
                </div>
            </div>

            <!-- Chat -->
            <div id="tab-chat" class="dash-tab">
                <div class="card-gradient p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-xl font-extrabold">{{ $L['dash']['assistant'] }}</h2>
                        <button onclick="clearChat()" class="text-xs font-semibold c2 hover:opacity-70">{{ $L['dash']['clear'] }}</button>
                    </div>
                    <div class="card-gradient overflow-hidden">
                        <div class="chat-box">
                            <div class="chat-scroll" id="chatMessages">
                                <div class="msg ai">
                                    <div class="msg-ava ai"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['scale'] !!}</svg></div>
                                    <div class="msg-b">{{ $L['dash']['hello'] }}</div>
                                </div>
                            </div>
                            <div class="flex gap-2 p-3 border-t" style="border-color:var(--card-border);">
                                <input type="text" id="chatInput" placeholder="{{ $L['dash']['ph'] }}" onkeypress="if(event.key==='Enter') sendMessage()" aria-label="{{ $L['dash']['ph'] }}">
                                <button onclick="sendMessage()" class="btn-primary !px-4" aria-label="→"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['send'] !!}</svg></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compare -->
            <div id="tab-compare" class="dash-tab">
                <div class="card-gradient p-6 md:p-8">
                    <h2 class="text-xl font-extrabold mb-2">{{ $L['dash']['compare'] }}</h2>
                    <p class="text-sm mb-6 c2">{{ $L['dash']['cmp_run'] }}</p>
                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        @foreach(['A' => 'fileInputA', 'B' => 'fileInputB'] as $label => $inputId)
                            <div class="card-gradient p-4">
                                <h3 class="text-sm font-bold mb-3">{{ $label === 'A' ? $L['dash']['docA'] : $L['dash']['docB'] }}</h3>
                                <label class="upload-zone block p-6 text-center cursor-pointer">
                                    <input type="file" id="{{ $inputId }}" class="hidden" accept=".pdf,.docx,.txt" onchange="this.nextElementSibling.nextElementSibling.textContent = this.files[0]?.name || ''">
                                    <svg class="w-8 h-8 mx-auto mb-2 c3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $icons['upload'] !!}</svg>
                                    <span class="text-xs block truncate c3"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-center mb-6">
                        <button onclick="compareDocuments()" class="btn-primary text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['compare'] !!}</svg>{{ $L['dash']['cmp_run'] }}</button>
                    </div>
                    <div id="comparisonResults" class="hidden">
                        <div class="card-gradient p-5 mb-4">
                            <h3 class="text-sm font-bold mb-3">{{ $L['dash']['cmp_res'] }}</h3>
                            <div class="diff-added c1">+ {{ app()->getLocale() === 'en' ? 'Added: confidentiality extended to 5 years' : (app()->getLocale() === 'tg' ? 'Илова шуд: махфият то 5 сол' : 'Добавлено: пункт о конфиденциальности расширен до 5 лет') }}</div>
                            <div class="diff-removed c1">− {{ app()->getLocale() === 'en' ? 'Removed: liability cap 100%' : (app()->getLocale() === 'tg' ? 'Ҳазф шуд: маҳдудияти масъулият 100%' : 'Удалено: ограничение ответственности в 100% суммы договора') }}</div>
                            <div class="diff-modified c1">~ {{ app()->getLocale() === 'en' ? 'Modified: payment term 30 → 14 days' : (app()->getLocale() === 'tg' ? 'Тағйир ёфт: мӯҳлати пардохт 30 → 14 рӯз' : 'Изменено: срок оплаты сокращён с 30 до 14 дней') }}</div>
                        </div>
                        <div class="flex justify-end"><button onclick="showToast('success', T.cmp_done)" class="btn-white !py-2 text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['download'] !!}</svg>{{ $L['dash']['cmp_dl'] }}</button></div>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div id="tab-history" class="dash-tab">
                <div class="card-gradient p-6">
                    <h2 class="text-xl font-extrabold mb-6">{{ $L['dash']['hist'] }}</h2>
                    <div class="timeline">
                        @foreach([['completed', '#10B981', '1234', 'medium'], ['warning', '#F59E0B', '1233', 'high'], ['completed', '#10B981', '1232', 'low']] as $h)
                            <div class="timeline-item {{ $h[0] }}">
                                <div class="card-gradient p-4 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <span class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:{{ $h[1] }}22; color:{{ $h[1] }};"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['doc'] !!}</svg></span>
                                        <div>
                                            <div class="font-bold text-sm c1">{{ app()->getLocale() === 'en' ? 'Contract' : (app()->getLocale() === 'tg' ? 'Шартнома' : 'Договор') }} №{{ $h[2] }}</div>
                                            <div class="text-xs c3">{{ $L['js'][$h[3]] ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-xs c3">{{ $h[2] === '1234' ? '2 min' : ($h[2] === '1233' ? '1h' : '24h') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div id="tab-settings" class="dash-tab">
                <div class="card-gradient p-6">
                    <h3 class="text-lg font-extrabold mb-6">{{ $L['dash']['set_title'] }}</h3>
                    <div class="space-y-4 max-w-lg">
                        <div><label class="block text-xs mb-1.5 c2">{{ $L['dash']['email_l'] }}</label><input type="email" value="user@company.com"></div>
                        <div><label class="block text-xs mb-1.5 c2">{{ $L['dash']['lang_l'] }}</label><select><option>Русский</option><option>Тоҷикӣ</option><option>English</option></select></div>
                        <div class="pt-4"><button onclick="showToast('success', T.saved)" class="btn-primary px-6 py-2.5 text-sm">{{ $L['dash']['save'] }}</button></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- ==================== FOOTER ==================== -->
<footer class="py-14 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <svg class="w-8 h-8" style="color:#3B82F6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['shield'] !!}</svg>
                    <span class="text-lg font-extrabold c1">LegalAI Pro</span>
                </div>
                <p class="text-[13px] leading-relaxed c2">{{ $L['footer']['about'] }}</p>
            </div>
            <div>
                <h4 class="text-sm font-extrabold mb-4">{{ $L['footer']['product'] }}</h4>
                <ul class="space-y-2.5">
                    @foreach($L['tools']['items'] as $i => $tool)
                        <li><button onclick="openTool('t{{ $i + 1 }}')" class="text-[13px] c2 hover:opacity-70">{{ $tool['t'] }}</button></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-extrabold mb-4">{{ $L['footer']['resources'] }}</h4>
                <ul class="space-y-2.5">@foreach($L['footer']['res'] as $r)<li><a href="#" class="text-[13px] c2 hover:opacity-70">{{ $r }}</a></li>@endforeach</ul>
            </div>
            <div>
                <h4 class="text-sm font-extrabold mb-4">{{ $L['footer']['company'] }}</h4>
                <ul class="space-y-2.5">@foreach($L['footer']['comp'] as $c)<li><a href="#" class="text-[13px] c2 hover:opacity-70">{{ $c }}</a></li>@endforeach</ul>
            </div>
        </div>
        <div class="h-px mb-6" style="background:var(--card-border);"></div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs c3">{{ str_replace(':year', date('Y'), $L['footer']['copyright']) }}</p>
            <div class="flex flex-wrap gap-x-6 gap-y-2">
                <a href="#" class="text-xs c3 hover:opacity-70">{{ $L['footer']['privacy'] }}</a>
                <a href="#" class="text-xs c3 hover:opacity-70">{{ $L['footer']['terms'] }}</a>
                <a href="#" class="text-xs c3 hover:opacity-70">{{ $L['footer']['support'] }}</a>
            </div>
        </div>
    </div>
</footer>

<!-- ==================== MODALS ==================== -->
<div id="demoModal" class="modal-overlay hidden" onclick="closeModal('demoModal')" role="dialog" aria-modal="true">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-5 border-b" style="border-color:var(--card-border);">
            <h3 class="text-base font-extrabold">{{ $L['hero']['cta2'] }}</h3>
            <button onclick="closeModal('demoModal')" class="bell-btn" aria-label="✕"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['x'] !!}</svg></button>
        </div>
        <div class="p-5">
            <div class="aspect-video rounded-xl flex flex-col items-center justify-center gap-3" style="background:var(--bg-primary); border:1px solid var(--card-border);">
                <span class="w-14 h-14 rounded-full flex items-center justify-center text-white" style="background:var(--accent-gradient); box-shadow:0 10px 30px var(--accent-glow);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['play'] !!}</svg>
                </span>
                <p class="text-xs c3">LegalAI Pro · demo · 02:40</p>
            </div>
        </div>
    </div>
</div>

<div id="tplModal" class="modal-overlay hidden" onclick="closeModal('tplModal')" role="dialog" aria-modal="true">
    <div class="modal-box !max-w-2xl" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-5 border-b" style="border-color:var(--card-border);">
            <h3 class="text-base font-extrabold">{{ $L['dash']['new_tpl'] }}</h3>
            <button onclick="closeModal('tplModal')" class="bell-btn" aria-label="✕"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['x'] !!}</svg></button>
        </div>
        <div class="p-5 space-y-4">
            <div><label class="block text-xs mb-1.5 c2">{{ $L['dash']['new_tpl'] }}</label><input type="text"></div>
            <div><label class="block text-xs mb-1.5 c2">{{ $L['docs']['title'] }}</label><select>@foreach($L['docs']['types'] as $t)<option>{{ $t[0] }}</option>@endforeach</select></div>
        </div>
        <div class="p-5 border-t flex justify-end gap-3" style="border-color:var(--card-border);">
            <button onclick="closeModal('tplModal')" class="btn-white !py-2 text-sm">✕</button>
            <button onclick="closeModal('tplModal'); showToast('success', T.tpl)" class="btn-primary !py-2 text-sm">{{ $L['dash']['new_tpl'] }}</button>
        </div>
    </div>
</div>

<!-- Command palette -->
<div id="commandPalette" class="palette hidden" onclick="if(event.target===this) closePalette()">
    <div class="palette-box">
        <input id="commandInput" type="text" placeholder="{{ $L['nav']['search'] }}… (Ctrl+K)" class="!border-0 !rounded-none !p-4" aria-label="{{ $L['nav']['search'] }}">
        <div id="commandResults" class="max-h-72 overflow-y-auto border-t" style="border-color:var(--card-border);"></div>
    </div>
</div>

<div id="toast" class="toast" role="status" aria-live="polite">
    <svg id="toastIcon" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
    <span id="toastMessage"></span>
</div>

<button id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="↑">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['up'] !!}</svg>
</button>

<div class="fab" onclick="showToast('info', T.help)" role="button" aria-label="{{ $L['js']['help'] }}">
    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['chat'] !!}</svg>
</div>

<script>
/* ================================================================
   LOCALIZED JS STRINGS
   ================================================================ */
const T = @json($L['js']);
const TYPE_WORDS = @json($L['hero']['typed']);
const DEMO_Q0 = @json($L['demo']['user_q']);
const DEMO_A0 = @json($L['demo']['ans']);
const DEMO_CITE = @json($L['demo']['cite']);
const DEMO_QUICK = @json($L['demo']['quick']);
const DEMO_TYPING = @json($L['demo']['typing']);
const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ================================================================
   THEME
   ================================================================ */
function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    const dark = document.body.classList.contains('dark-theme');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
    showToast('success', dark ? T.theme_dark : T.theme_light);
    updateChartsTheme();
}

(function restoreTheme() {
    try {
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    } catch (e) {}
})();

/* ================================================================
   SCROLL: progress bar + back-to-top
   ================================================================ */
window.addEventListener('scroll', () => {
    const h = document.documentElement;
    const pct = h.scrollTop / (h.scrollHeight - h.clientHeight) * 100;
    document.getElementById('scrollProgress').style.width = pct + '%';
    document.getElementById('backToTop').classList.toggle('show', h.scrollTop > 600);
}, { passive: true });

/* ================================================================
   TYPEWRITER
   ================================================================ */
(function typewriter() {
    const el = document.getElementById('typeTarget');
    if (!el) return;
    if (REDUCED) {
        el.textContent = TYPE_WORDS[0];
        return;
    }
    let pi = 0, ci = 0, deleting = false;
    (function tick() {
        const word = TYPE_WORDS[pi];
        el.textContent = word.slice(0, ci);
        if (!deleting) {
            ci++;
            if (ci > word.length) {
                deleting = true;
                setTimeout(tick, 2200);
                return;
            }
            setTimeout(tick, 65);
        } else {
            ci--;
            if (ci === 0) {
                deleting = false;
                pi = (pi + 1) % TYPE_WORDS.length;
            }
            setTimeout(tick, 32);
        }
    })();
})();

/* ================================================================
   COUNTERS
   ================================================================ */
function initCounters() {
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            const target = parseFloat(el.dataset.count);
            const dec = parseInt(el.dataset.decimals || 0);
            const dur = 1400;
            const st = performance.now();
            (function step(now) {
                const p = Math.min((now - st) / dur, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                const val = (target * eased).toFixed(dec);
                el.textContent = val.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
                if (p < 1) requestAnimationFrame(step);
            })(st);
            io.unobserve(el);
        });
    }, { threshold: .5 });
    document.querySelectorAll('.counter').forEach(el => io.observe(el));
}

/* ================================================================
   TILT (hero image)
   ================================================================ */
(function tilt() {
    const img = document.getElementById('heroImg');
    if (!img || REDUCED || !window.matchMedia('(pointer: fine)').matches) return;
    const wrap = img.parentElement;
    wrap.addEventListener('mousemove', e => {
        const r = img.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width - .5;
        const y = (e.clientY - r.top) / r.height - .5;
        img.style.transform = 'perspective(900px) rotateY(' + (x * 5) + 'deg) rotateX(' + (-y * 5) + 'deg)';
    });
    wrap.addEventListener('mouseleave', () => {
        img.style.transform = 'none';
    });
})();

/* ================================================================
   REVEAL ON SCROLL
   ================================================================ */
function initReveal() {
    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('active');
                io.unobserve(e.target);
            }
        });
    }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
}

/* ================================================================
   TOAST
   ================================================================ */
function showToast(type, message) {
    const toast = document.getElementById('toast');
    document.getElementById('toastMessage').textContent = message;
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
    };
    document.getElementById('toastIcon').innerHTML = icons[type] || icons.info;
    toast.className = 'toast ' + type;
    requestAnimationFrame(() => toast.classList.add('show'));
    clearTimeout(toast._t);
    toast._t = setTimeout(() => toast.classList.remove('show'), 3200);
}

/* ================================================================
   SECTIONS / TABS / TOOLS
   ================================================================ */
function showSection(name) {
    const l = document.getElementById('landingSection');
    const d = document.getElementById('dashboardSection');
    l.classList.toggle('hidden-section', name !== 'landing');
    l.classList.toggle('active-section', name === 'landing');
    d.classList.toggle('hidden-section', name !== 'dashboard');
    d.classList.toggle('active-section', name === 'dashboard');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function switchDashboardTab(key) {
    document.querySelectorAll('.dash-tab').forEach(t => t.classList.remove('active'));
    const tab = document.getElementById('tab-' + key);
    if (tab) tab.classList.add('active');
    document.querySelectorAll('.sidebar-btn[data-tab]').forEach(b => b.classList.toggle('active', b.dataset.tab === key));
    if (key === 'analysis') initAnalyticsCharts();
}

function openTool(key) {
    showSection('dashboard');
    const map = { t1: 'upload', t2: 'upload', t3: 'upload', t4: 'chat', t5: 'upload', t6: 'analysis', t7: 'compare', t8: 'chat' };
    switchDashboardTab(map[key] || 'upload');
    showToast('info', T.help);
}

/* ================================================================
   SLIDER / MODALS
   ================================================================ */
function scrollSlider(dir) {
    document.getElementById('sliderTrack').scrollBy({ left: dir * 260, behavior: 'smooth' });
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

/* ================================================================
   FILE UPLOAD + ANALYSIS
   ================================================================ */
let uploadedFile = null;
let riskChart = null;
let analyticsChart1 = null;
let analyticsChart2 = null;

const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

if (dropZone) {
    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', e => {
        if (e.target.files.length) handleFile(e.target.files[0]);
    });
    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
    });
}

function handleFile(file) {
    const ext = '.' + file.name.split('.').pop().toLowerCase();
    if (!['.pdf', '.docx', '.txt'].includes(ext)) {
        showToast('error', T.file_type);
        return;
    }
    if (file.size > 50 * 1024 * 1024) {
        showToast('error', T.file_size);
        return;
    }
    uploadedFile = file;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatSize(file.size);
    document.getElementById('fileInfo').classList.remove('hidden');
    document.getElementById('analyzeBtn').disabled = false;
    showToast('success', T.file_ok);
}

function formatSize(b) {
    const k = 1024, s = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(b) / Math.log(k));
    return parseFloat((b / Math.pow(k, i)).toFixed(1)) + ' ' + s[i];
}

function removeFile() {
    uploadedFile = null;
    fileInput.value = '';
    document.getElementById('fileInfo').classList.add('hidden');
    document.getElementById('analyzeBtn').disabled = true;
    document.getElementById('analysisResults').classList.add('hidden');
    document.getElementById('analysisProgress').classList.add('hidden');
}

function startAnalysis() {
    if (!uploadedFile) return;
    document.getElementById('analysisResults').classList.add('hidden');
    document.getElementById('analysisProgress').classList.remove('hidden');
    let p = 0;
    const bar = document.getElementById('progressBar');
    const iv = setInterval(() => {
        p += 2;
        bar.style.width = p + '%';
        if (p >= 100) {
            clearInterval(iv);
            setTimeout(showResults, 350);
        }
    }, 45);
}

function chartColors() {
    const dark = document.body.classList.contains('dark-theme');
    return {
        tick: dark ? '#9CA3AF' : '#64748B',
        grid: dark ? 'rgba(148,163,184,.12)' : 'rgba(15,23,42,.06)'
    };
}

function showResults() {
    document.getElementById('analysisProgress').classList.add('hidden');
    document.getElementById('analysisResults').classList.remove('hidden');
    const c = chartColors();
    const ctx = document.getElementById('riskChart').getContext('2d');
    if (riskChart) riskChart.destroy();
    riskChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: T.labels,
            datasets: [{ data: [75, 58, 34, 21], backgroundColor: ['rgba(239,68,68,.8)', 'rgba(245,158,11,.8)', 'rgba(16,185,129,.75)', 'rgba(59,130,246,.8)'], borderRadius: 6 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: c.grid }, ticks: { color: c.tick } },
                x: { grid: { display: false }, ticks: { color: c.tick } }
            },
            plugins: { legend: { display: false } }
        }
    });
    const lvl = {
        high: { cls: 'risk-high', label: T.high },
        medium: { cls: 'risk-medium', label: T.medium },
        low: { cls: 'risk-low', label: T.low }
    };
    document.getElementById('riskList').innerHTML = T.risk_items.map(r =>
        '<div class="flex items-start gap-2.5 p-2.5 rounded-lg" style="background:var(--bg-primary); border:1px solid var(--card-border);">' +
        '<span class="text-[10px] font-bold px-2 py-1 rounded-full whitespace-nowrap ' + lvl[r.l].cls + '" style="color:#fff;">' + lvl[r.l].label + '</span>' +
        '<span class="text-xs leading-relaxed c2">' + r.t + '</span></div>'
    ).join('');
    document.getElementById('recommendations').innerHTML = T.recs.map(r =>
        '<li class="flex items-start gap-2 text-[13px] c2"><svg class="w-3.5 h-3.5 shrink-0 mt-1" style="color:#3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg><span>' + r + '</span></li>'
    ).join('');
    document.getElementById('overallRisk').textContent = T.overall_v;
    showToast('success', T.done);
}

function initAnalyticsCharts() {
    const c = chartColors();
    const ctx1 = document.getElementById('analyticsChart1');
    const ctx2 = document.getElementById('analyticsChart2');
    if (!ctx1 || !ctx2) return;
    if (analyticsChart1) analyticsChart1.destroy();
    if (analyticsChart2) analyticsChart2.destroy();
    analyticsChart1 = new Chart(ctx1.getContext('2d'), {
        type: 'line',
        data: {
            labels: T.months,
            datasets: [{ data: [120, 150, 180, 220, 280, 340], borderColor: '#3B82F6', backgroundColor: 'rgba(59,130,246,.12)', fill: true, tension: .4 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { grid: { color: c.grid }, ticks: { color: c.tick } },
                x: { grid: { display: false }, ticks: { color: c.tick } }
            },
            plugins: { legend: { display: false } }
        }
    });
    analyticsChart2 = new Chart(ctx2.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: T.types_l,
            datasets: [{ data: [35, 25, 20, 15, 5], backgroundColor: ['rgba(59,130,246,.8)', 'rgba(168,85,247,.8)', 'rgba(34,197,94,.8)', 'rgba(245,158,11,.8)', 'rgba(239,68,68,.8)'], borderWidth: 0 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { color: c.tick } } }
        }
    });
}

function updateChartsTheme() {
    const c = chartColors();
    if (riskChart) {
        riskChart.options.scales.y.grid.color = c.grid;
        riskChart.options.scales.y.ticks.color = c.tick;
        riskChart.options.scales.x.ticks.color = c.tick;
        riskChart.update();
    }
    if (analyticsChart1) {
        analyticsChart1.options.scales.y.grid.color = c.grid;
        analyticsChart1.options.scales.y.ticks.color = c.tick;
        analyticsChart1.options.scales.x.ticks.color = c.tick;
        analyticsChart1.update();
    }
    if (analyticsChart2) {
        analyticsChart2.options.plugins.legend.labels.color = c.tick;
        analyticsChart2.update();
    }
}

/* ================================================================
   DASHBOARD CHAT
   ================================================================ */
let chatIdx = 0;

function sendMessage() {
    const input = document.getElementById('chatInput');
    const text = input.value.trim();
    if (!text) return;
    addMsg('user', text);
    input.value = '';
    const box = document.getElementById('chatMessages');
    const typing = document.createElement('div');
    typing.className = 'msg ai';
    typing.innerHTML = '<div class="msg-ava ai"></div><div class="msg-b"><span class="tdot"></span> <span class="tdot"></span> <span class="tdot"></span></div>';
    box.appendChild(typing);
    box.scrollTop = box.scrollHeight;
    setTimeout(() => {
        typing.remove();
        addMsg('ai', T.responses[chatIdx % T.responses.length]);
        chatIdx++;
    }, 1200);
}

function addMsg(who, text) {
    const box = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.className = 'msg ' + who;
    div.innerHTML = '<div class="msg-ava ' + (who === 'ai' ? 'ai' : 'usr') + '"></div><div class="msg-b"></div>';
    div.querySelector('.msg-b').textContent = text;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function clearChat() {
    document.getElementById('chatMessages').innerHTML = '<div class="msg ai"><div class="msg-ava ai"></div><div class="msg-b">' + T.responses[0] + '</div></div>';
    showToast('success', T.chat_clear);
}

/* ================================================================
   COMPARE
   ================================================================ */
function compareDocuments() {
    const a = document.getElementById('fileInputA').files[0];
    const b = document.getElementById('fileInputB').files[0];
    if (!a || !b) {
        showToast('warning', T.cmp_need);
        return;
    }
    showToast('info', T.cmp_run);
    setTimeout(() => {
        document.getElementById('comparisonResults').classList.remove('hidden');
        showToast('success', T.cmp_done);
    }, 1500);
}

/* ================================================================
   COMMAND PALETTE (Ctrl+K)
   ================================================================ */
const commands = [
    { t: '📤 ' + (commands => 'Upload')(), k: 'U', run: () => { showSection('dashboard'); switchDashboardTab('upload'); } },
];

/* simpler explicit list */
const CMD = [];

function buildCommands() {
    CMD.length = 0;
    CMD.push({ t: '📤 Upload', run: () => { showSection('dashboard'); switchDashboardTab('upload'); } });
    CMD.push({ t: '💬 Chat', run: () => { showSection('dashboard'); switchDashboardTab('chat'); } });
    CMD.push({ t: '🔀 Compare', run: () => { showSection('dashboard'); switchDashboardTab('compare'); } });
    CMD.push({ t: '📊 Analytics', run: () => { showSection('dashboard'); switchDashboardTab('analysis'); } });
    CMD.push({ t: '🕘 History', run: () => { showSection('dashboard'); switchDashboardTab('history'); } });
    CMD.push({ t: '⚙️ Settings', run: () => { showSection('dashboard'); switchDashboardTab('settings'); } });
    CMD.push({ t: ' Theme', run: toggleTheme });
    CMD.push({ t: '🏠 Home', run: () => showSection('landing') });
}

let cmdFocus = 0;

function openPalette() {
    buildCommands();
    document.getElementById('commandPalette').classList.remove('hidden');
    const input = document.getElementById('commandInput');
    input.value = '';
    input.focus();
    cmdFocus = 0;
    renderCommands('');
}

function closePalette() {
    document.getElementById('commandPalette').classList.add('hidden');
}

function renderCommands(q) {
    const box = document.getElementById('commandResults');
    const list = CMD.filter(c => c.t.toLowerCase().includes(q.toLowerCase()));
    cmdFocus = Math.min(cmdFocus, Math.max(list.length - 1, 0));
    box.innerHTML = list.map((c, i) =>
        '<div class="command-item ' + (i === cmdFocus ? 'focused' : '') + '" data-i="' + CMD.indexOf(c) + '">' +
        '<span>' + c.t + '</span><kbd>↵</kbd></div>'
    ).join('') || '<div class="p-6 text-center text-sm c3">—</div>';
    box.querySelectorAll('.command-item').forEach(el => {
        el.addEventListener('click', () => {
            CMD[parseInt(el.dataset.i)].run();
            closePalette();
        });
    });
}

document.getElementById('commandInput').addEventListener('input', e => {
    cmdFocus = 0;
    renderCommands(e.target.value);
});

document.getElementById('commandInput').addEventListener('keydown', e => {
    const q = e.target.value;
    const list = CMD.filter(c => c.t.toLowerCase().includes(q.toLowerCase()));
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        cmdFocus = Math.min(cmdFocus + 1, list.length - 1);
        renderCommands(q);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        cmdFocus = Math.max(cmdFocus - 1, 0);
        renderCommands(q);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (list.length) {
            list[cmdFocus].run();
            closePalette();
        }
    }
});

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const p = document.getElementById('commandPalette');
        p.classList.contains('hidden') ? openPalette() : closePalette();
    }
    if (e.key === 'Escape') {
        closePalette();
        document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => m.classList.add('hidden'));
    }
});

/* ================================================================
   HERO DEMO CHAT (Alpine)
   ================================================================ */
document.addEventListener('alpine:init', () => {
    Alpine.data('chatDemo', () => ({
        messages: [],
        typing: false,
        quick: DEMO_QUICK,
        typingLabel: DEMO_TYPING,
        init() {
            if (this.$refs.demoScroll) {
                this.messages = [
                    { who: 'user', text: DEMO_Q0 },
                    { who: 'ai', lines: DEMO_A0, cite: DEMO_CITE }
                ];
            }
        },
        ask(item) {
            if (this.typing) return;
            this.messages.push({ who: 'user', text: item.q });
            this.typing = true;
            this.scroll();
            setTimeout(() => {
                this.typing = false;
                this.messages.push({ who: 'ai', lines: item.a });
                this.scroll();
            }, 1000);
        },
        scroll() {
            setTimeout(() => {
                if (this.$refs.demoScroll) this.$refs.demoScroll.scrollTop = this.$refs.demoScroll.scrollHeight;
            }, 60);
        }
    }));
});

/* ================================================================
   INIT
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initReveal();
    initCounters();
});
</script>
</body>
</html>