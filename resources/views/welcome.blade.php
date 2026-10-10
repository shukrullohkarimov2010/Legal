<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&display=swap" rel="stylesheet">

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
        'title1' => 'Платформаи ҳуқуқӣ',
        'title2' => 'бо истифодаи зеҳни сунъӣ',
        'typed' => ['аз рӯи қонунгузории Тоҷикистон', 'бо ишора ба моддаҳои кодексҳо', 'барои бизнес ва ҳуқуқшиносон'],
        'sub' => 'Санҷиши худкори шартномаҳо дар 60 сония. Ошкор кардани хавфҳои пинҳон, номувофиқатӣ ба қонунгузории ҶТ ва шартҳои номусоид. То 90% вақти ҳуқуқшиносон сарфа мешавад.',
        'cta1' => 'Бақайдгирӣ барои боркунӣ', 'cta2' => 'Видео-намоиш',
        'trust' => ['Рамзгузории AES-256', 'Мувофиқ ба Қонуни ҶТ «Дар бораи маълумоти шахсӣ»', 'Дастгирии 24/7'],
        'stat1' => 'Вақти миёнаи таҳлил', 'stat2' => 'Дақиқии муайянкунӣ',
    ],
    'ticker' => ['Кодекси гражданӣ', 'Кодекси меҳнат', 'Кодекси андоз', 'Кодекси ҷиноят', 'КҲМ ҶТ', 'Қонун дар бораи ҶДММ', 'Иҷора', 'Мерос', 'NDA', 'Таҳвил'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'санад дар база'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'дақиқӣ'], ['v' => 60, 's' => '', 'l' => 'сония таҳлил'], ['v' => 8, 's' => '', 'l' => 'абзори AI']],
    'showcase' => [
        'eyebrow' => 'Дар амал',
        'title' => 'Се абзор, ки ҳуқуқшиносии шуморо',
        'accent' => 'ба сатҳи дигар мебарорад',
        'items' => [
            ['t' => 'Хавфҳоро дар матн фавран мебинед', 'd' => 'AI ҳар банди шартномаро тафтиш карда, дараҷаи хавфро бо ишора ба моддаи мушаххас нишон медиҳад.'],
            ['t' => 'Ҳуҷҷатро дар якчанд дақиқа месозед', 'd' => 'Шаблонҳои санҷидашуда аз рӯи ГК ҶТ — танҳо тағйирёбандаҳоро пур кунед.'],
            ['t' => 'Ҳар хулоса ба модда пайваст аст', 'd' => 'Граф аз банди шартнома то моддаи дахлдори кодекс — шаффоф ва санҷидашаванда.'],
        ],
    ],
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
        'title1' => 'Legal platform',
        'title2' => 'powered by artificial intelligence',
        'typed' => ['built on Tajikistan law', 'with article-level citations', 'for business and lawyers'],
        'sub' => 'Automated contract review in 60 seconds. Detection of hidden risks, non-compliance with the legislation of Tajikistan and unfavorable terms. Saves up to 90% of lawyers’ time.',
        'cta1' => 'Register to upload', 'cta2' => 'Demo video',
        'trust' => ['AES-256 encryption', 'Compliant with the RT Law "On Personal Data"', '24/7 support'],
        'stat1' => 'Average analysis time', 'stat2' => 'Detection accuracy',
    ],
    'ticker' => ['Civil Code', 'Labor Code', 'Tax Code', 'Criminal Code', 'CAO of RT', 'LLC Law', 'Lease', 'Inheritance', 'NDA', 'Supply'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'acts in the base'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'accuracy'], ['v' => 60, 's' => '', 'l' => 'sec per analysis'], ['v' => 8, 's' => '', 'l' => 'AI tools']],
    'showcase' => [
        'eyebrow' => 'In practice',
        'title' => 'Three tools that take your legal work',
        'accent' => 'to another level',
        'items' => [
            ['t' => 'See risks in the text instantly', 'd' => 'AI checks every clause of the contract and flags the risk level with a reference to the exact article.'],
            ['t' => 'Assemble a document in minutes', 'd' => 'Templates verified against the Civil Code of RT — just fill in the variables.'],
            ['t' => 'Every conclusion is linked to a law', 'd' => 'A graph from the contract clause to the relevant code article — transparent and verifiable.'],
        ],
    ],
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
        'title1' => 'Юридическая платформа',
        'title2' => 'с использованием искусственного интеллекта',
        'typed' => ['по законодательству Таджикистана', 'со ссылками на статьи кодексов', 'для бизнеса и юристов'],
        'sub' => 'Автоматическая проверка контрактов за 60 секунд. Выявление скрытых рисков, несоответствий законодательству Республики Таджикистан и невыгодных условий. Экономия до 90% времени юристов.',
        'cta1' => 'Зарегистрироваться для загрузки', 'cta2' => 'Демо-видео',
        'trust' => ['Шифрование AES-256', 'Соответствие Закону РТ «О персональных данных»', '24/7 поддержка'],
        'stat1' => 'Среднее время анализа', 'stat2' => 'Точность выявления',
    ],
    'ticker' => ['Гражданский кодекс', 'Трудовой кодекс', 'Налоговый кодекс', 'Уголовный кодекс', 'КоАП РТ', 'Закон об ООО', 'Аренда', 'Наследство', 'NDA', 'Поставка'],
    'stats' => [['v' => 20000, 's' => '+', 'l' => 'актов в базе'], ['v' => 98.7, 'd' => 1, 's' => '%', 'l' => 'точность'], ['v' => 60, 's' => '', 'l' => 'секунд анализ'], ['v' => 8, 's' => '', 'l' => 'AI-инструментов']],
    'showcase' => [
        'eyebrow' => 'На практике',
        'title' => 'Три инструмента, которые выводят вашу юридическую работу',
        'accent' => 'на другой уровень',
        'items' => [
            ['t' => 'Видите риски прямо в тексте', 'd' => 'AI проверяет каждый пункт договора и подсвечивает уровень риска со ссылкой на конкретную статью.'],
            ['t' => 'Собираете документ за минуты', 'd' => 'Шаблоны, выверенные по ГК РТ, — просто заполните переменные.'],
            ['t' => 'Каждый вывод привязан к норме закона', 'd' => 'Граф от пункта договора до статьи кодекса — прозрачно и проверяемо.'],
        ],
    ],
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

<meta name="description" content="{{ $L['hero']['sub'] }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:site_name" content="LegalAI Pro">
<meta property="og:title" content="LegalAI Pro — {{ $L['hero']['title1'] }} {{ $L['hero']['title2'] }}">
<meta property="og:description" content="{{ $L['hero']['sub'] }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : (app()->getLocale() === 'tg' ? 'tg_TJ' : 'ru_RU') }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="LegalAI Pro — {{ $L['hero']['title1'] }} {{ $L['hero']['title2'] }}">
<meta name="twitter:description" content="{{ $L['hero']['sub'] }}">

<meta name="theme-color" content="#F8FAFC" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0A0F1A" media="(prefers-color-scheme: dark)">

<link rel="stylesheet" href="{{ asset('css/tw-default.css') }}?v={{ @filemtime(public_path('css/tw-default.css')) }}">
<script src="{{ asset('vendor/chart.min.js') }}"></script>
<script defer src="{{ asset('vendor/alpine.min.js') }}"></script>

<style>
/* ================================================================
   1. ДИЗАЙН-ТОКЕНЫ (светлая база — как на макете; тёмная — опция)
   ================================================================ */
:root {
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    --font-display: var(--font-sans);

    --bg-primary: #F8FAFC;
    --bg-secondary: #FFFFFF;
    --card-bg: rgba(255, 255, 255, 0.88);
    --card-solid: #FFFFFF;
    --card-border: rgba(15, 23, 42, 0.07);
    --card-hover-border: rgba(37, 99, 235, 0.28);
    --text-primary: #0B1220;
    --text-secondary: #475569;
    --text-muted: #8A99AD;
    --accent-primary: #2563EB;
    --accent-strong: #1D4ED8;
    --accent-gradient: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
    --accent-glow: rgba(37, 99, 235, 0.18);
    --success: #059669;
    --warning: #D97706;
    --error: #DC2626;
    --glass-blur: blur(16px) saturate(140%);
    /* Многослойные мягкие тени вместо одной «жирной» */
    --shadow-card: 0 1px 1px rgba(11,18,32,.04), 0 3px 8px -2px rgba(11,18,32,.05), 0 12px 24px -12px rgba(11,18,32,.10);
    --shadow-hover: 0 1px 2px rgba(11,18,32,.05), 0 8px 20px -6px rgba(11,18,32,.10), 0 24px 48px -20px rgba(11,18,32,.18);
    --shadow-lift: 0 20px 60px -24px rgba(11,18,32,.28);
    --radius-sm: 8px;
    --radius-md: 10px;
    --radius-lg: 14px;
    --radius-xl: 20px;
    --radius-full: 9999px;
    --t-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --t-normal: 0.24s cubic-bezier(0.4, 0, 0.2, 1);
    --t-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    --nav-bg: rgba(255, 255, 255, 0.72);
    --grid-line: rgba(15, 23, 42, 0.035);
    --soft-blue: rgba(37, 99, 235, 0.07);
    --soft-blue-t: #1D4ED8;
    --hairline: rgba(15, 23, 42, 0.06);
}

/* ---------- Тёмная тема ---------- */
body.dark-theme {
    --bg-primary: #0A0F1A;
    --bg-secondary: #0F1626;
    --card-bg: rgba(19, 27, 44, 0.72);
    --card-solid: #111a2b;
    --card-border: rgba(148, 163, 184, 0.13);
    --card-hover-border: rgba(96, 165, 250, 0.40);
    --text-primary: #F1F5F9;
    --text-secondary: #9FB0C6;
    --text-muted: #6B7C93;
    --accent-primary: #60A5FA;
    --accent-strong: #93C5FD;
    --accent-gradient: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
    --accent-glow: rgba(59, 130, 246, 0.28);
    --success: #34D399;
    --warning: #FBBF24;
    --error: #F87171;
    --shadow-card: 0 1px 1px rgba(0,0,0,.3), 0 6px 16px -6px rgba(0,0,0,.5);
    --shadow-hover: 0 2px 4px rgba(0,0,0,.35), 0 20px 44px -18px rgba(0,0,0,.7);
    --shadow-lift: 0 24px 64px -28px rgba(0,0,0,.85);
    --nav-bg: rgba(10, 15, 26, 0.72);
    --grid-line: rgba(148, 163, 184, 0.045);
    --soft-blue: rgba(96, 165, 250, 0.10);
    --soft-blue-t: #93C5FD;
    --hairline: rgba(148, 163, 184, 0.10);
}

/* ================================================================
   2. БАЗА
   ================================================================ */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
    -webkit-text-size-adjust: 100%;
}

body {
    font-family: var(--font-sans);
    background: var(--bg-primary);
    color: var(--text-primary);
    overflow-x: hidden;
    line-height: 1.6;
    position: relative;
    font-feature-settings: 'cv02' 1, 'cv03' 1, 'cv04' 1, 'cv11' 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
    transition: background var(--t-normal), color var(--t-normal);
}

button, input, select, textarea {
    font-family: inherit;
}

/* Оптический трекинг: чем крупнее кегль, тем плотнее межбуквенное */
h1, h2, h3, h4 {
    color: var(--text-primary);
    text-wrap: balance;
}

h1 { letter-spacing: -0.038em; line-height: 1.06; }
h2 { letter-spacing: -0.032em; line-height: 1.14; }
h3 { letter-spacing: -0.018em; }
h4 { letter-spacing: -0.012em; }

/* Цифры одинаковой ширины — счётчики не «прыгают» */
.counter,
.tabular {
    font-variant-numeric: tabular-nums;
    font-feature-settings: 'tnum' 1;
}

/* Ненавязчивая засечная акцентная гарнитура для ключевых слов в заголовках */
.h-accent {
    font-family: var(--font-display);
    font-style: normal;
    font-weight: inherit;
    letter-spacing: -0.005em;
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    padding-right: .06em;
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
   3. ФОН: тонкая сетка + спокойный градиентный подсвет
   ================================================================ */
.bg-grid {
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background-image:
        linear-gradient(var(--grid-line) 1px, transparent 1px),
        linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
    background-size: 64px 64px;
    mask-image: radial-gradient(ellipse 90% 70% at 50% 0%, black 25%, transparent 80%);
    -webkit-mask-image: radial-gradient(ellipse 90% 70% at 50% 0%, black 25%, transparent 80%);
}

/* Один спокойный тональный подсвет вместо трёх разноцветных пятен */
body::before {
    content: '';
    position: fixed;
    inset: 0;
    z-index: -1;
    pointer-events: none;
    background:
        radial-gradient(900px circle at 78% -8%, rgba(37,99,235,.09) 0%, transparent 60%),
        radial-gradient(700px circle at 8% 12%, rgba(79,70,229,.06) 0%, transparent 62%);
}

body.dark-theme::before {
    background:
        radial-gradient(900px circle at 78% -8%, rgba(59,130,246,.16) 0%, transparent 60%),
        radial-gradient(700px circle at 8% 12%, rgba(99,102,241,.10) 0%, transparent 62%);
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
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid transparent;
    transition: border-color var(--t-normal), box-shadow var(--t-normal), background var(--t-normal);
}

/* Граница и тень появляются только после начала скролла */
nav.scrolled {
    border-bottom-color: var(--hairline);
    box-shadow: 0 1px 0 var(--hairline), 0 8px 24px -18px rgba(11,18,32,.5);
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
    left: 50%;
    width: 0;
    height: 2px;
    border-radius: 2px;
    background: var(--accent-primary);
    transform: translateX(-50%);
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

.lang-option {
    display: block;
    padding: .5rem .75rem;
    border-radius: var(--radius-sm);
    font-size: .75rem;
    font-weight: 700;
    color: var(--text-secondary);
    text-decoration: none;
    transition: background var(--t-fast), color var(--t-fast);
}

.lang-option:hover { background: var(--soft-blue); color: var(--text-primary); }

.lang-option.is-active,
.lang-btn.is-active {
    background: var(--soft-blue);
    color: var(--accent-primary);
    border-color: color-mix(in srgb, var(--accent-primary) 24%, transparent);
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
    background: var(--error);
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
    padding: .78rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: .9375rem;
    letter-spacing: -0.01em;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .55rem;
    position: relative;
    /* Внутренний блик сверху + мягкая цветная тень — «объём» без анимаций */
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.18),
        0 1px 2px rgba(11,18,32,.12),
        0 8px 20px -8px var(--accent-glow);
    transition: transform var(--t-fast), box-shadow var(--t-normal), filter var(--t-fast);
    text-decoration: none;
}

.btn-primary:hover {
    transform: translateY(-1px);
    filter: saturate(1.06);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.22),
        0 2px 4px rgba(11,18,32,.12),
        0 14px 28px -10px var(--accent-glow);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: inset 0 1px 3px rgba(11,18,32,.22);
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
    justify-content: center;
    gap: .55rem;
    font-weight: 600;
    font-size: .9375rem;
    letter-spacing: -0.01em;
    border-radius: var(--radius-md);
    padding: .78rem 1.5rem;
    transition: transform var(--t-fast), border-color var(--t-normal), box-shadow var(--t-normal), background var(--t-normal);
    box-shadow: var(--shadow-card);
    text-decoration: none;
}

.btn-white:hover {
    transform: translateY(-1px);
    border-color: var(--card-hover-border);
    background: var(--bg-secondary);
    box-shadow: var(--shadow-hover);
}

.btn-white:active {
    transform: translateY(0);
}

.badge-pill {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: var(--soft-blue);
    color: var(--soft-blue-t);
    border: 1px solid rgba(37,99,235,.16);
    border-radius: 999px;
    padding: .4rem .9rem;
    font-size: .78125rem;
    font-weight: 600;
    letter-spacing: -0.005em;
}

.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--success);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--success) 18%, transparent);
    animation: pulse 2.4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .55; transform: scale(1.25); }
}

input, select, textarea {
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    color: var(--text-primary);
    padding: .7rem .95rem;
    border-radius: var(--radius-md);
    outline: none;
    width: 100%;
    font-size: .9rem;
    box-shadow: inset 0 1px 2px rgba(11,18,32,.03);
    transition: border-color var(--t-fast), box-shadow var(--t-fast);
}

input::placeholder, textarea::placeholder { color: var(--text-muted); }

input:focus, select:focus, textarea:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent-primary) 16%, transparent);
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
    transform: translateY(-3px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}

.feature-card {
    position: relative;
    overflow: hidden;
}

/* Волосяная линия-подсветка сверху при наведении вместо «блика» */
.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 12%;
    right: 12%;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
    opacity: 0;
    transition: opacity var(--t-normal);
    pointer-events: none;
}

.feature-card:hover::before {
    opacity: .55;
}

.info-section {
    background: var(--card-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
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
    left: 15%;
    right: 15%;
    height: 1px;
    background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--accent-primary) 55%, transparent), transparent);
}

/* Единая «плитка» иконки: мягкая подложка + акцентный штрих.
   Спокойнее и профессиональнее, чем восемь разноцветных градиентов. */
.icon-tile {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--soft-blue);
    color: var(--accent-primary);
    border: 1px solid color-mix(in srgb, var(--accent-primary) 14%, transparent);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.5);
    transition: background var(--t-normal), color var(--t-normal), border-color var(--t-normal);
}

body.dark-theme .icon-tile {
    box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
}

.card-hover:hover .icon-tile {
    background: var(--accent-primary);
    border-color: var(--accent-primary);
    color: #fff;
}

/* Круглая плитка для слайдера документов и карточек примеров */
.icon-tile-round {
    border-radius: var(--radius-full);
    width: 56px;
    height: 56px;
}

.bullet {
    display: flex;
    gap: .5rem;
    align-items: flex-start;
    font-size: .8125rem;
    line-height: 1.55;
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
    color: var(--accent-strong);
}

/* ================================================================
   8. ГРАДИЕНТНЫЙ ТЕКСТ / TYPEWRITER
   ================================================================ */
.text-gradient {
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
}

.type-cursor {
    display: inline-block;
    width: 2px;
    height: 1.05em;
    background: var(--accent-primary);
    margin-left: 4px;
    vertical-align: -0.14em;
    border-radius: 1px;
    animation: blink 1.1s step-end infinite;
}

@keyframes blink {
    50% { opacity: 0; }
}

/* ================================================================
   9. HERO: свечение, tilt, плавающие карточки
   ================================================================ */
.hero-glow {
    position: absolute;
    inset: -10%;
    z-index: 0;
    background: radial-gradient(55% 55% at 62% 38%, var(--accent-glow), transparent 72%);
    filter: blur(38px);
}

/* Оправа-«окно приложения» вокруг визуала — читается как продуктовый скриншот */
.hero-frame {
    position: relative;
    z-index: 1;
    border-radius: var(--radius-xl);
    border: 1px solid var(--card-border);
    background: var(--card-solid);
    padding: 8px;
    box-shadow: var(--shadow-lift);
    overflow: hidden;
}

.hero-frame-bar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 4px 6px 10px;
}

.hero-frame-bar i {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--card-border);
}

.hero-frame-bar span {
    margin-left: auto;
    font-size: .6875rem;
    font-weight: 600;
    letter-spacing: .04em;
    color: var(--text-muted);
}

.hero-img {
    display: block;
    border-radius: calc(var(--radius-xl) - 10px);
    position: relative;
    z-index: 1;
}

.stat-badge {
    position: absolute;
    z-index: 2;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-lg);
    padding: .8rem 1rem;
    box-shadow: var(--shadow-hover);
    display: flex;
    align-items: center;
    gap: .75rem;
    animation: float 7s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* ================================================================
   9b. STARBURST — фирменный лучевой визуал (hero / stats)
   ================================================================ */
.starburst-wrap {
    position: relative;
    width: 100%;
    overflow: hidden;
    border-radius: var(--radius-xl);
    border: 1px solid var(--card-border);
    background: var(--card-bg);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    box-shadow: var(--shadow-card);
    min-height: 340px;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 3.25rem;
}

.starburst-parallax {
    position: absolute;
    inset: -6%;
    z-index: 0;
    /* Плавное растворение к краям, чтобы луч не «обрубался» о рамку */
    mask-image: radial-gradient(120% 95% at 50% 105%, black 45%, transparent 100%);
    -webkit-mask-image: radial-gradient(120% 95% at 50% 105%, black 45%, transparent 100%);
    animation: parallaxDrift 14s ease-in-out infinite;
}

@keyframes parallaxDrift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(0, -10px) scale(1.015); }
}

.starburst-svg {
    display: block;
    width: 100%;
    height: 100%;
}

.starburst-svg line,
.starburst-svg circle {
    transition: stroke-opacity var(--t-slow), fill-opacity var(--t-slow);
}

.starburst-content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0 1.5rem 2.75rem;
}

@keyframes starburstDrift {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-4px) scale(1.008); }
}

.starburst-svg {
    animation: starburstDrift 10s ease-in-out infinite;
}

@keyframes twinkle {
    0%, 100% { opacity: 1; }
    50% { opacity: .32; }
}

.starburst-svg circle {
    animation: twinkle 3.2s ease-in-out infinite;
    transform-origin: center;
}

.starburst-svg circle:nth-of-type(3n) { animation-delay: .4s; animation-duration: 3.8s; }
.starburst-svg circle:nth-of-type(5n) { animation-delay: 1.1s; animation-duration: 2.6s; }
.starburst-svg circle:nth-of-type(7n) { animation-delay: 1.8s; animation-duration: 4.4s; }

/* Мягкая дышащая подсветка в основании луча — фон живёт сам по себе, без наведения */
.starburst-wrap::before {
    content: '';
    position: absolute;
    left: 50%;
    bottom: -10%;
    width: 60%;
    aspect-ratio: 1;
    transform: translateX(-50%);
    background: radial-gradient(circle, var(--accent-glow), transparent 70%);
    filter: blur(30px);
    z-index: 0;
    pointer-events: none;
    animation: burstPulse 4.5s ease-in-out infinite;
}

@keyframes burstPulse {
    0%, 100% { opacity: .55; transform: translateX(-50%) scale(1); }
    50% { opacity: 1; transform: translateX(-50%) scale(1.12); }
}

@media (max-width: 640px) {
    .starburst-wrap { min-height: 280px; padding-top: 2.25rem; }
}

@media (prefers-reduced-motion: reduce) {
    .starburst-svg { animation: none; }
    .starburst-svg circle { animation: none; }
    .starburst-parallax { animation: none; }
    .starburst-wrap::before { animation: none; }
}

/* ================================================================
   9c. CARD GLOW — лёгкий отклик на наведение (без наклона и поворота)
   Карточка чуть приподнимается (1-2px) и по ней скользит мягкий блик
   в точке курсора — «живой» отклик без физического движения/тряски.
   ================================================================ */
.tilt-card {
    --mx: 50%;
    --my: 50%;
    transition: transform .35s cubic-bezier(.22, 1, .36, 1), box-shadow .35s ease, border-color .35s ease;
}

.tilt-card.has-glare {
    position: relative;
}

.tilt-card.has-glare::after {
    content: '';
    position: absolute;
    inset: 0;
    z-index: 6;
    pointer-events: none;
    border-radius: inherit;
    background: radial-gradient(380px circle at var(--mx) var(--my), rgba(255, 255, 255, .45), transparent 55%);
    opacity: 0;
    transition: opacity .4s ease;
    mix-blend-mode: overlay;
}

body.dark-theme .tilt-card.has-glare::after {
    background: radial-gradient(380px circle at var(--mx) var(--my), rgba(255, 255, 255, .18), transparent 55%);
}

.tilt-card.has-glare.tilting::after {
    opacity: 1;
}

@media (prefers-reduced-motion: reduce) {
    .tilt-card.has-glare::after { display: none; }
}

/* Карточка-документ мягко «парит», как настоящая карта в воздухе */
@keyframes docFloat {
    0%, 100% { transform: translateY(0) rotate(-1.4deg); }
    50% { transform: translateY(-9px) rotate(1.4deg); }
}

.mock-doc-card {
    animation: docFloat 6s ease-in-out infinite;
}

/* Узлы графа норм права мягко пульсируют */
@keyframes pinPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .55; }
}

/* Мягкий блик пробегает по карточкам отчёта —живой фон без наведения */
.mock-report {
    position: relative;
    overflow: hidden;
}

.mock-report::after {
    content: '';
    position: absolute;
    inset: 0;
    left: -160%;
    width: 55%;
    background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .55), transparent);
    animation: shimmerSweep 5.5s ease-in-out infinite;
}

body.dark-theme .mock-report::after {
    background: linear-gradient(100deg, transparent, rgba(255, 255, 255, .12), transparent);
}

.mock-report:nth-child(2)::after { animation-delay: .5s; }
.mock-report:nth-child(3)::after { animation-delay: 1s; }

@keyframes shimmerSweep {
    0% { left: -160%; }
    45%, 100% { left: 160%; }
}

/* Иконки 8 инструментов — объёмная подача: градиент, лёгкий разворот,
   спокойное «дыхание» в фоне и уверенный pop при наведении на карточку */
.tool-card-3d .icon-tile-pop {
    background: var(--accent-gradient);
    color: #fff;
    border: none;
    box-shadow: 0 10px 22px -9px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, .28);
    transform: rotate(-6deg);
    transition: transform .4s cubic-bezier(.22, 1, .36, 1), box-shadow .4s ease;
    animation: iconBob 4.2s ease-in-out infinite;
}

.tool-card-3d:nth-child(4n+2) .icon-tile-pop { animation-delay: .6s; }
.tool-card-3d:nth-child(4n+3) .icon-tile-pop { animation-delay: 1.3s; }
.tool-card-3d:nth-child(4n+4) .icon-tile-pop { animation-delay: 2s; }

@keyframes iconBob {
    0%, 100% { transform: rotate(-6deg) translateY(0); }
    50% { transform: rotate(-1deg) translateY(-4px); }
}

.card-hover.tilt-card.tilting .icon-tile-pop {
    transform: rotate(0deg) scale(1.14) translateY(-2px);
    box-shadow: 0 16px 28px -8px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, .34);
    animation-play-state: paused;
}

@media (prefers-reduced-motion: reduce) {
    .mock-report::after { display: none; }
    .tool-card-3d .icon-tile-pop { animation: none; transform: rotate(-6deg); }
}

/* ================================================================
   9d. REVEAL-3D — кинематографичное появление при скролле
   («всплытие» из глубины с лёгким наклоном, вместо плоского слайда)
   ================================================================ */
.reveal-3d {
    opacity: 0;
    transform: perspective(1400px) rotateX(14deg) translateY(48px) scale(.96);
    transform-origin: 50% 100%;
    transition: opacity 1s cubic-bezier(.16, 1, .3, 1), transform 1s cubic-bezier(.16, 1, .3, 1);
}

.reveal-3d.active {
    opacity: 1;
    transform: perspective(1400px) rotateX(0deg) translateY(0) scale(1);
}

@media (prefers-reduced-motion: reduce) {
    .reveal-3d { opacity: 1; transform: none; }
}

/* ================================================================
   10. TICKER (бегущая строка кодексов)
   ================================================================ */
.ticker {
    overflow: hidden;
    border-top: 1px solid var(--hairline);
    border-bottom: 1px solid var(--hairline);
    background: var(--bg-secondary);
    padding: 15px 0;
    /* Края растворяются — строка не «обрубается» о край экрана */
    mask-image: linear-gradient(90deg, transparent, black 8%, black 92%, transparent);
    -webkit-mask-image: linear-gradient(90deg, transparent, black 8%, black 92%, transparent);
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
    font-size: .8125rem;
    font-weight: 500;
    letter-spacing: -0.005em;
    color: var(--text-muted);
}

/* ================================================================
   10b. SHOWCASE — 3-карточная витрина флагманских инструментов
   (в духе крупных продуктовых карточек с собственной иллюстрацией)
   ================================================================ */
.showcase-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

@media (max-width: 900px) {
    .showcase-grid { grid-template-columns: 1fr; }
}

.showcase-card {
    position: relative;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-xl);
    padding: 1.75rem 1.75rem 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 460px;
    box-shadow: var(--shadow-card);
    transition: transform var(--t-normal), box-shadow var(--t-normal), border-color var(--t-normal);
}

.showcase-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
    border-color: var(--card-hover-border);
}

.showcase-expand {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: var(--soft-blue);
    color: var(--accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
}

.showcase-heading {
    font-size: 1.25rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.25;
    max-width: 78%;
    margin-bottom: .5rem;
    position: relative;
    z-index: 3;
}

.showcase-desc {
    font-size: .8125rem;
    color: var(--text-secondary);
    max-width: 86%;
    margin-bottom: 1.25rem;
    position: relative;
    z-index: 3;
}

.showcase-visual {
    flex: 1;
    position: relative;
    margin: 0 -1.75rem;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    overflow: hidden;
    background: var(--soft-blue);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    padding-bottom: 0;
}

/* --- Card 1: анализ рисков — мини-мокап чата с отчётом --- */
.mock-report {
    width: 92%;
    margin-bottom: 1.1rem;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-hover);
    padding: .85rem .9rem;
    font-size: .72rem;
}

.mock-report + .mock-report { margin-top: -.6rem; }

.mock-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .5rem;
}

.mock-row:last-child { margin-bottom: 0; }

.mock-dot {
    width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
}

.mock-bar {
    height: 6px;
    border-radius: 4px;
    background: var(--card-border);
    flex: 1;
}

.mock-badge {
    font-size: .625rem;
    font-weight: 700;
    padding: .15rem .5rem;
    border-radius: 999px;
    white-space: nowrap;
}

/* Круговой риск-балл: дорисовывается при появлении карточки на экране */
.mock-gauge-card {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.mock-gauge-wrap {
    position: relative;
    width: 62px;
    height: 62px;
    flex-shrink: 0;
}

.mock-gauge {
    width: 100%;
    height: 100%;
    animation: gaugeGlow 3s ease-in-out infinite;
}

@keyframes gaugeGlow {
    0%, 100% { filter: drop-shadow(0 0 3px var(--accent-glow)); }
    50% { filter: drop-shadow(0 0 9px var(--accent-glow)); }
}

.mock-gauge-progress {
    stroke-dasharray: 264;
    stroke-dashoffset: 264;
    transition: stroke-dashoffset 1.5s cubic-bezier(.16, 1, .3, 1) .3s;
}

.showcase-card.active .mock-gauge-progress {
    stroke-dashoffset: 74;
}

.mock-gauge-num {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
}

@media (prefers-reduced-motion: reduce) {
    .mock-gauge { animation: none; }
    .mock-gauge-progress { transition: none; stroke-dashoffset: 74; }
}

/* --- Card 2: генератор — стилизованный документ, как «карта» --- */
.mock-doc-card {
    width: 62%;
    aspect-ratio: 5 / 7;
    margin-bottom: -6%;
    border-radius: 16px;
    background: var(--accent-gradient);
    box-shadow: var(--shadow-hover);
    position: relative;
    overflow: hidden;
    padding: 1.1rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.mock-doc-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(120% 60% at 15% 0%, rgba(255,255,255,.35), transparent 55%),
        radial-gradient(90% 50% at 100% 100%, rgba(0,0,0,.18), transparent 60%);
}

.mock-doc-lines span {
    display: block;
    height: 5px;
    border-radius: 4px;
    background: rgba(255,255,255,.55);
    margin-bottom: 6px;
    position: relative;
    z-index: 1;
}

.mock-doc-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.mock-doc-seal {
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(255,255,255,.22);
    border: 1px solid rgba(255,255,255,.5);
    display: flex; align-items: center; justify-content: center;
}

.mock-doc-word {
    font-size: .78rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -0.01em;
}

/* Печать организации периодически «опускается» на документ */
.mock-stamp {
    position: absolute;
    width: 112px;
    height: 112px;
    left: 50%;
    top: 42%;
    margin: -56px 0 0 -56px;
    z-index: 4;
    pointer-events: none;
    filter: drop-shadow(0 8px 14px rgba(37, 99, 235, .22));
    animation: stampDrop 5.5s cubic-bezier(.34, 1.4, .4, 1) infinite;
}

.mock-stamp svg {
    width: 100%;
    height: 100%;
}

@keyframes stampDrop {
    0% { transform: translateY(-95px) scale(1.35) rotate(-20deg); opacity: 0; }
    26% { transform: translateY(0) scale(1) rotate(-8deg); opacity: 1; }
    32% { transform: translateY(2px) scale(.93) rotate(-8deg); opacity: 1; }
    40%, 76% { transform: translateY(0) scale(1) rotate(-8deg); opacity: 1; }
    92%, 100% { transform: translateY(-95px) scale(1.35) rotate(-20deg); opacity: 0; }
}

@media (prefers-reduced-motion: reduce) {
    .mock-stamp {
        animation: none;
        transform: translateY(0) scale(1) rotate(-8deg);
        opacity: 1;
    }
}

/* --- Card 3: вращающийся шар из точек (в стиле Stripe) --- */
.mock-globe-scene {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    perspective: 700px;
    position: relative;
}

.mock-globe-sphere {
    position: relative;
    width: 1px;
    height: 1px;
    transform-style: preserve-3d;
    animation: globeSpin 18s linear infinite;
}

@keyframes globeSpin {
    from { transform: rotateY(0deg) rotateX(-8deg); }
    to { transform: rotateY(360deg) rotateX(-8deg); }
}

.globe-dot {
    position: absolute;
    top: 0;
    left: 0;
    margin: -1px;
    border-radius: 50%;
    background: var(--accent-primary);
    pointer-events: none;
}

.globe-pin {
    position: absolute;
    top: 0;
    left: 0;
    width: 7px;
    height: 7px;
    margin: -3.5px;
    border-radius: 50%;
    background: var(--accent-primary);
    box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent-primary) 22%, transparent), 0 0 14px 2px var(--accent-primary);
    animation: pinPulse 2.4s ease-in-out infinite;
}

.globe-pin:nth-of-type(2n) { animation-delay: .5s; }
.globe-pin:nth-of-type(3n) { animation-delay: 1s; }

/* Декоративные орбитальные кольца — вращаются независимо, для глубины */
.mock-globe-orbit {
    position: absolute;
    top: 50%;
    left: 50%;
    border: 1px solid color-mix(in srgb, var(--accent-primary) 30%, transparent);
    border-radius: 50%;
    pointer-events: none;
}

.mock-globe-orbit-a {
    width: 230px;
    height: 230px;
    margin: -115px 0 0 -115px;
    transform: rotateX(78deg) rotate(0deg);
    animation: orbitSpin 12s linear infinite;
}

.mock-globe-orbit-b {
    width: 260px;
    height: 260px;
    margin: -130px 0 0 -130px;
    border-color: color-mix(in srgb, var(--accent-primary) 16%, transparent);
    transform: rotateX(78deg) rotate(90deg);
    animation: orbitSpin 18s linear infinite reverse;
}

@keyframes orbitSpin {
    from { transform: rotateX(78deg) rotate(0deg); }
    to { transform: rotateX(78deg) rotate(360deg); }
}

.mock-globe-glow {
    position: absolute;
    inset: 0;
    background: radial-gradient(140px circle at 50% 50%, var(--accent-glow), transparent 70%);
    pointer-events: none;
    animation: burstPulse 4.5s ease-in-out infinite;
}

@media (prefers-reduced-motion: reduce) {
    .mock-globe-sphere { animation: none; }
    .mock-globe-orbit { animation: none; }
    .globe-pin { animation: none; }
    .mock-globe-glow { animation: none; }
}

/* Увеличенная версия шара для «featured»-карточки (крупнее соседних) */
.showcase-card-lg {
    min-height: 560px;
}

@media (min-width: 901px) {
    .showcase-card-lg {
        margin-top: -18px;
        margin-bottom: -18px;
    }
}

.mock-globe-scene-lg {
    perspective: 900px;
}

.mock-globe-scene-lg .mock-globe-orbit-a {
    width: 320px;
    height: 320px;
    margin: -160px 0 0 -160px;
}

.mock-globe-scene-lg .mock-globe-orbit-b {
    width: 362px;
    height: 362px;
    margin: -181px 0 0 -181px;
}

.mock-globe-scene-lg .globe-pin {
    width: 8px;
    height: 8px;
    margin: -4px;
}

.mock-globe-scene-lg .mock-globe-glow {
    background: radial-gradient(220px circle at 50% 50%, var(--accent-glow), transparent 70%);
}

/* Дуга «перелёта» пролетает над шаром по кругу */
.mock-globe-arc {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 3;
    pointer-events: none;
    overflow: visible;
}

.mock-globe-arc-path {
    stroke-dasharray: 460;
    stroke-dashoffset: 460;
    animation: arcFly 4.5s ease-in-out infinite;
}

@keyframes arcFly {
    0% { stroke-dashoffset: 460; opacity: 0; }
    10% { opacity: 1; }
    48%, 58% { stroke-dashoffset: 0; opacity: 1; }
    88%, 100% { stroke-dashoffset: -460; opacity: 0; }
}

/* Всплывающие бейджи со статистикой поверх шара */
.mock-globe-badge {
    position: absolute;
    display: flex;
    align-items: center;
    gap: .5rem;
    background: var(--card-solid);
    border: 1px solid var(--card-border);
    border-radius: 999px;
    padding: .35rem .75rem .35rem .35rem;
    box-shadow: var(--shadow-hover);
    z-index: 5;
    animation: badgeFloat 5s ease-in-out infinite;
}

.mock-globe-badge-a {
    left: 4%;
    bottom: 20%;
    animation-delay: .3s;
}

.mock-globe-badge-b {
    right: 6%;
    top: 12%;
    animation-delay: 1.7s;
}

@keyframes badgeFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-7px); }
}

@media (max-width: 900px) {
    .showcase-card-lg { min-height: 500px; }
    .mock-globe-badge { display: none; }
}

@media (prefers-reduced-motion: reduce) {
    .mock-globe-arc-path { animation: none; stroke-dashoffset: 0; opacity: .8; }
    .mock-globe-badge { animation: none; }
}

.showcase-section-head {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .75rem;
    margin-bottom: 2.25rem;
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
    border-radius: var(--radius-lg);
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    overflow: hidden;
    transition: border-color var(--t-normal), box-shadow var(--t-normal);
}

.faq-item:hover {
    border-color: var(--card-hover-border);
}

.faq-item.active {
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-card);
}

.faq-q {
    width: 100%;
    padding: 1.05rem 1.25rem;
    background: none;
    border: none;
    color: var(--text-primary);
    font-size: .9375rem;
    font-weight: 600;
    letter-spacing: -0.01em;
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
    line-height: 1.65;
    color: var(--text-secondary);
    transition: max-height .35s cubic-bezier(0.4, 0, 0.2, 1), padding .35s;
}

.faq-item.active .faq-a {
    max-height: 400px;
    padding: 0 1.25rem 1.15rem;
}

.pricing-card {
    position: relative;
    transition: transform var(--t-normal), border-color var(--t-normal), box-shadow var(--t-normal);
}

.pricing-card:hover {
    transform: translateY(-4px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}

/* Рекомендуемый тариф выделяется рамкой и высотой, а не свечением */
.pricing-card.popular {
    border-color: var(--accent-primary);
    box-shadow:
        0 0 0 1px var(--accent-primary),
        var(--shadow-hover);
}

@media (min-width: 768px) {
    .pricing-card.popular { transform: translateY(-10px); }
    .pricing-card.popular:hover { transform: translateY(-14px); }
}

.testimonial-card {
    position: relative;
    transition: transform var(--t-normal), box-shadow var(--t-normal), border-color var(--t-normal);
}

.testimonial-card:hover {
    transform: translateY(-3px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}

/* Аватар автора отзыва — спокойная тональная заливка */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .875rem;
    background: var(--soft-blue);
    color: var(--accent-primary);
    border: 1px solid color-mix(in srgb, var(--accent-primary) 16%, transparent);
    flex-shrink: 0;
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


/* ================================================================
   19. ПОДВАЛ И ТОНАЛЬНЫЕ ПЛАШКИ
   ================================================================ */
.site-footer {
    background: var(--bg-secondary);
    border-top: 1px solid var(--hairline);
    margin-top: 2rem;
}

.foot-h {
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.foot-link {
    font-size: .8125rem;
    color: var(--text-secondary);
    text-decoration: none;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    text-align: left;
    transition: color var(--t-fast);
}

.foot-link:hover { color: var(--accent-primary); }

/* Квадратная тональная плашка под иконку (герой, статистика) */
.tint {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tint-success {
    background: color-mix(in srgb, var(--success) 12%, transparent);
    color: var(--success);
}

.tint-accent {
    background: var(--soft-blue);
    color: var(--accent-primary);
}

/* ================================================================
   18. ЕДИНЫЕ МЕЛОЧИ: маркеры, ленты, номера шагов, оправа героя
   ================================================================ */
.mark {
    width: 22px;
    height: 22px;
    margin-top: 1px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.mark svg { width: 12px; height: 12px; }

.mark-bad {
    background: color-mix(in srgb, var(--error) 12%, transparent);
    color: var(--error);
}

.mark-good {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
}

/* Лента-метка на карточке */
.ribbon {
    position: absolute;
    top: -11px;
    right: 1.5rem;
    font-size: .6875rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: #fff;
    padding: .3rem .75rem;
    border-radius: var(--radius-full);
    box-shadow: 0 4px 12px -4px rgba(11,18,32,.4);
}

.ribbon-good { background: var(--success); }
.ribbon-accent { background: var(--accent-gradient); }

/* Номер шага в разделе «как это работает» */
.step-num {
    width: 52px;
    height: 52px;
    margin: 0 auto 1.5rem;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 10;
    font-size: 1.0625rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #fff;
    background: var(--accent-gradient);
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.2),
        0 8px 20px -8px var(--accent-glow);
}

/* Предупреждение внутри карточки инструмента */
.tool-note {
    display: flex;
    align-items: flex-start;
    gap: .4rem;
    font-size: .6875rem;
    line-height: 1.5;
    margin-bottom: .75rem;
    padding-top: .5rem;
    border-top: 1px solid var(--hairline);
    color: var(--warning);
}

.tool-note svg { width: 13px; height: 13px; flex-shrink: 0; margin-top: 2px; }

/* Заглушка, если внешняя картинка героя не загрузилась */
.hero-frame-empty::after {
    content: '';
    display: block;
    aspect-ratio: 4 / 3;
    border-radius: calc(var(--radius-xl) - 10px);
    background:
        linear-gradient(135deg, var(--soft-blue), transparent 60%),
        var(--bg-primary);
    border: 1px dashed var(--card-border);
}

@media (max-width: 768px) {
    .info-section { border-radius: var(--radius-lg); }
    .hero-frame { padding: 6px; }
}

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

/* ================================================================
   20. CARD HOVER OVERRIDES — объявлены в конце файла намеренно,
   чтобы гарантированно перебивать более ранние правила :hover
   (card-hover, showcase-card) с равной специфичностью.
   Только лёгкий подъём (1-2px) и свечение — без наклона/поворота.
   ================================================================ */
.showcase-card.tilting {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lift);
    border-color: var(--card-hover-border);
}

.card-hover.tilt-card.tilting {
    transform: translateY(-2px);
    border-color: var(--card-hover-border);
    box-shadow: var(--shadow-hover);
}
</style>
</head>

<body>
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[10004] focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg">{{ app()->getLocale() === 'tg' ? 'Ба мундариҷа гузаштан' : (app()->getLocale() === 'en' ? 'Skip to content' : 'Перейти к контенту') }}</a>

<div id="scrollProgress" aria-hidden="true"></div>
<div class="bg-grid" aria-hidden="true"></div>

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
                <svg class="w-8 h-8" style="color:var(--accent-primary)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">{!! $icons['shield'] !!}</svg>
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
                            <a href="{{ route('locale.switch', $code) }}" class="lang-option {{ app()->getLocale() === $code ? 'is-active' : '' }}">{{ $label }}</a>
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
                <a href="{{ route('locale.switch', $code) }}" class="lang-btn flex-1 justify-center {{ app()->getLocale() === $code ? 'is-active' : '' }}">{{ $label }}</a>
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
                    <span class="h-accent">{{ $L['hero']['title2'] }}</span>
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
                <div class="hero-frame">
                    <div class="hero-frame-bar">
                        <i></i><i></i><i></i>
                        <span>LEGALAI PRO</span>
                    </div>
                    <img id="heroImg" src="https://image.qwenlm.ai/public_source/48f99b9f-6493-4401-a338-927a8c873e1a/1269011f0-818d-4497-9ee0-8c8efb1a3788.png" alt="Legal AI" class="hero-img w-full object-cover" loading="eager" onerror="this.closest('.hero-frame').classList.add('hero-frame-empty'); this.remove();">
                </div>
                <div class="stat-card stat-badge -bottom-5 -left-3 md:-left-6">
                    <span class="tint tint-success">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['bolt'] !!}</svg>
                    </span>
                    <div>
                        <div class="text-lg font-extrabold c1"><span class="counter" data-count="60">0</span> {{ app()->getLocale() === 'en' ? 'sec' : (app()->getLocale() === 'tg' ? 'сония' : 'сек') }}</div>
                        <div class="text-xs c3">{{ $L['hero']['stat1'] }}</div>
                    </div>
                </div>
                <div class="stat-card stat-badge -top-5 -right-3 md:-right-6" style="animation-delay: 1.2s;">
                    <span class="tint tint-accent">
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

       <!-- STATS BAND — фирменный «звёздный взрыв» под цифрами -->
    <section class="pt-16 pb-4 px-4 sm:px-6 reveal reveal-3d">
        <div class="max-w-7xl mx-auto">
            <div class="starburst-wrap tilt-card" data-tilt-max="4">
                <div class="starburst-parallax">
                <svg class="starburst-svg" viewBox="0 0 1000 500" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
<line x1="500" y1="470" x2="362.4" y2="464.5" stroke="var(--accent-primary)" stroke-opacity="0.32" stroke-width="0.87" stroke-linecap="round"/>
<line x1="500" y1="470" x2="160.9" y2="448.5" stroke="var(--accent-primary)" stroke-opacity="0.77" stroke-width="0.7" stroke-linecap="round"/>
<line x1="500" y1="470" x2="361.1" y2="460.2" stroke="var(--accent-primary)" stroke-opacity="0.28" stroke-width="1.21" stroke-linecap="round"/>
<line x1="500" y1="470" x2="308.9" y2="455.7" stroke="var(--accent-primary)" stroke-opacity="0.59" stroke-width="1.25" stroke-linecap="round"/>
<line x1="500" y1="470" x2="188.9" y2="438.3" stroke="var(--accent-primary)" stroke-opacity="0.71" stroke-width="0.61" stroke-linecap="round"/>
<line x1="500" y1="470" x2="157.1" y2="420.5" stroke="var(--accent-primary)" stroke-opacity="0.37" stroke-width="0.79" stroke-linecap="round"/>
<line x1="500" y1="470" x2="269.0" y2="430.7" stroke="var(--accent-primary)" stroke-opacity="0.19" stroke-width="0.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="188.2" y2="412.1" stroke="var(--accent-primary)" stroke-opacity="0.71" stroke-width="1.48" stroke-linecap="round"/>
<line x1="500" y1="470" x2="76.2" y2="388.1" stroke="var(--accent-primary)" stroke-opacity="0.4" stroke-width="1.26" stroke-linecap="round"/>
<line x1="500" y1="470" x2="186.1" y2="399.3" stroke="var(--accent-primary)" stroke-opacity="0.75" stroke-width="1.29" stroke-linecap="round"/>
<line x1="500" y1="470" x2="359.8" y2="436.3" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="0.95" stroke-linecap="round"/>
<line x1="500" y1="470" x2="303.2" y2="423.7" stroke="var(--accent-primary)" stroke-opacity="0.19" stroke-width="0.93" stroke-linecap="round"/>
<line x1="500" y1="470" x2="265.8" y2="404.7" stroke="var(--accent-primary)" stroke-opacity="0.39" stroke-width="0.85" stroke-linecap="round"/>
<line x1="500" y1="470" x2="95.7" y2="355.0" stroke="var(--accent-primary)" stroke-opacity="0.59" stroke-width="1.33" stroke-linecap="round"/>
<line x1="500" y1="470" x2="159.1" y2="367.3" stroke="var(--accent-primary)" stroke-opacity="0.24" stroke-width="1.06" stroke-linecap="round"/>
<line x1="500" y1="470" x2="190.8" y2="359.4" stroke="var(--accent-primary)" stroke-opacity="0.53" stroke-width="1.42" stroke-linecap="round"/>
<line x1="500" y1="470" x2="152.8" y2="340.4" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="0.64" stroke-linecap="round"/>
<line x1="500" y1="470" x2="300.4" y2="395.7" stroke="var(--accent-primary)" stroke-opacity="0.27" stroke-width="1.73" stroke-linecap="round"/>
<line x1="500" y1="470" x2="290.2" y2="382.0" stroke="var(--accent-primary)" stroke-opacity="0.6" stroke-width="1.07" stroke-linecap="round"/>
<line x1="500" y1="470" x2="251.2" y2="359.5" stroke="var(--accent-primary)" stroke-opacity="0.31" stroke-width="0.9" stroke-linecap="round"/>
<line x1="500" y1="470" x2="307.2" y2="383.1" stroke="var(--accent-primary)" stroke-opacity="0.55" stroke-width="1.68" stroke-linecap="round"/>
<line x1="500" y1="470" x2="320.6" y2="386.3" stroke="var(--accent-primary)" stroke-opacity="0.85" stroke-width="1.21" stroke-linecap="round"/>
<line x1="500" y1="470" x2="369.4" y2="407.9" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="1.35" stroke-linecap="round"/>
<line x1="500" y1="470" x2="269.9" y2="347.1" stroke="var(--accent-primary)" stroke-opacity="0.17" stroke-width="1.06" stroke-linecap="round"/>
<line x1="500" y1="470" x2="244.5" y2="324.5" stroke="var(--accent-primary)" stroke-opacity="0.83" stroke-width="1.63" stroke-linecap="round"/>
<line x1="500" y1="470" x2="189.7" y2="300.8" stroke="var(--accent-primary)" stroke-opacity="0.62" stroke-width="1.24" stroke-linecap="round"/>
<line x1="500" y1="470" x2="216.1" y2="304.3" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="1.12" stroke-linecap="round"/>
<line x1="500" y1="470" x2="138.2" y2="245.8" stroke="var(--accent-primary)" stroke-opacity="0.76" stroke-width="0.92" stroke-linecap="round"/>
<line x1="500" y1="470" x2="344.5" y2="369.0" stroke="var(--accent-primary)" stroke-opacity="0.79" stroke-width="1.64" stroke-linecap="round"/>
<line x1="500" y1="470" x2="226.9" y2="288.2" stroke="var(--accent-primary)" stroke-opacity="0.56" stroke-width="0.78" stroke-linecap="round"/>
<line x1="500" y1="470" x2="258.9" y2="296.2" stroke="var(--accent-primary)" stroke-opacity="0.69" stroke-width="1.24" stroke-linecap="round"/>
<line x1="500" y1="470" x2="311.7" y2="337.1" stroke="var(--accent-primary)" stroke-opacity="0.13" stroke-width="1.71" stroke-linecap="round"/>
<line x1="500" y1="470" x2="195.5" y2="229.9" stroke="var(--accent-primary)" stroke-opacity="0.34" stroke-width="0.67" stroke-linecap="round"/>
<line x1="500" y1="470" x2="172.5" y2="201.4" stroke="var(--accent-primary)" stroke-opacity="0.18" stroke-width="1.18" stroke-linecap="round"/>
<line x1="500" y1="470" x2="214.5" y2="241.4" stroke="var(--accent-primary)" stroke-opacity="0.68" stroke-width="0.75" stroke-linecap="round"/>
<line x1="500" y1="470" x2="272.2" y2="274.1" stroke="var(--accent-primary)" stroke-opacity="0.31" stroke-width="1.65" stroke-linecap="round"/>
<line x1="500" y1="470" x2="353.9" y2="339.9" stroke="var(--accent-primary)" stroke-opacity="0.51" stroke-width="1.48" stroke-linecap="round"/>
<line x1="500" y1="470" x2="332.4" y2="317.5" stroke="var(--accent-primary)" stroke-opacity="0.85" stroke-width="1.38" stroke-linecap="round"/>
<line x1="500" y1="470" x2="290.8" y2="268.5" stroke="var(--accent-primary)" stroke-opacity="0.21" stroke-width="0.87" stroke-linecap="round"/>
<line x1="500" y1="470" x2="278.4" y2="249.8" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="0.86" stroke-linecap="round"/>
<line x1="500" y1="470" x2="271.1" y2="238.4" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="1.69" stroke-linecap="round"/>
<line x1="500" y1="470" x2="398.7" y2="356.8" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="1.4" stroke-linecap="round"/>
<line x1="500" y1="470" x2="385.3" y2="343.2" stroke="var(--accent-primary)" stroke-opacity="0.8" stroke-width="1.29" stroke-linecap="round"/>
<line x1="500" y1="470" x2="257.8" y2="186.0" stroke="var(--accent-primary)" stroke-opacity="0.71" stroke-width="0.83" stroke-linecap="round"/>
<line x1="500" y1="470" x2="329.9" y2="268.6" stroke="var(--accent-primary)" stroke-opacity="0.43" stroke-width="1.16" stroke-linecap="round"/>
<line x1="500" y1="470" x2="292.9" y2="201.9" stroke="var(--accent-primary)" stroke-opacity="0.84" stroke-width="0.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="357.5" y2="282.9" stroke="var(--accent-primary)" stroke-opacity="0.75" stroke-width="0.9" stroke-linecap="round"/>
<line x1="500" y1="470" x2="339.4" y2="254.1" stroke="var(--accent-primary)" stroke-opacity="0.43" stroke-width="0.93" stroke-linecap="round"/>
<line x1="500" y1="470" x2="258.8" y2="130.8" stroke="var(--accent-primary)" stroke-opacity="0.44" stroke-width="1.63" stroke-linecap="round"/>
<line x1="500" y1="470" x2="419.3" y2="348.7" stroke="var(--accent-primary)" stroke-opacity="0.85" stroke-width="1.6" stroke-linecap="round"/>
<line x1="500" y1="470" x2="281.3" y2="114.8" stroke="var(--accent-primary)" stroke-opacity="0.74" stroke-width="0.8" stroke-linecap="round"/>
<line x1="500" y1="470" x2="397.2" y2="302.8" stroke="var(--accent-primary)" stroke-opacity="0.41" stroke-width="0.67" stroke-linecap="round"/>
<line x1="500" y1="470" x2="277.6" y2="95.6" stroke="var(--accent-primary)" stroke-opacity="0.31" stroke-width="1.54" stroke-linecap="round"/>
<line x1="500" y1="470" x2="371.7" y2="242.6" stroke="var(--accent-primary)" stroke-opacity="0.82" stroke-width="1.79" stroke-linecap="round"/>
<line x1="500" y1="470" x2="333.9" y2="158.9" stroke="var(--accent-primary)" stroke-opacity="0.23" stroke-width="0.96" stroke-linecap="round"/>
<line x1="500" y1="470" x2="363.9" y2="192.0" stroke="var(--accent-primary)" stroke-opacity="0.52" stroke-width="1.5" stroke-linecap="round"/>
<line x1="500" y1="470" x2="358.8" y2="192.8" stroke="var(--accent-primary)" stroke-opacity="0.49" stroke-width="1.62" stroke-linecap="round"/>
<line x1="500" y1="470" x2="314.7" y2="84.4" stroke="var(--accent-primary)" stroke-opacity="0.18" stroke-width="0.82" stroke-linecap="round"/>
<line x1="500" y1="470" x2="364.2" y2="159.0" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="0.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="423.3" y2="278.5" stroke="var(--accent-primary)" stroke-opacity="0.55" stroke-width="1.34" stroke-linecap="round"/>
<line x1="500" y1="470" x2="384.7" y2="181.2" stroke="var(--accent-primary)" stroke-opacity="0.5" stroke-width="1.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="373.1" y2="141.6" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="1.07" stroke-linecap="round"/>
<line x1="500" y1="470" x2="427.4" y2="259.1" stroke="var(--accent-primary)" stroke-opacity="0.35" stroke-width="1.5" stroke-linecap="round"/>
<line x1="500" y1="470" x2="410.5" y2="213.1" stroke="var(--accent-primary)" stroke-opacity="0.85" stroke-width="1.8" stroke-linecap="round"/>
<line x1="500" y1="470" x2="439.1" y2="283.6" stroke="var(--accent-primary)" stroke-opacity="0.31" stroke-width="1.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="394.4" y2="81.5" stroke="var(--accent-primary)" stroke-opacity="0.39" stroke-width="0.79" stroke-linecap="round"/>
<line x1="500" y1="470" x2="414.6" y2="132.6" stroke="var(--accent-primary)" stroke-opacity="0.57" stroke-width="1.78" stroke-linecap="round"/>
<line x1="500" y1="470" x2="469.1" y2="341.2" stroke="var(--accent-primary)" stroke-opacity="0.72" stroke-width="0.96" stroke-linecap="round"/>
<line x1="500" y1="470" x2="409.8" y2="58.7" stroke="var(--accent-primary)" stroke-opacity="0.22" stroke-width="0.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="434.8" y2="175.6" stroke="var(--accent-primary)" stroke-opacity="0.32" stroke-width="1.33" stroke-linecap="round"/>
<line x1="500" y1="470" x2="466.3" y2="279.8" stroke="var(--accent-primary)" stroke-opacity="0.58" stroke-width="0.92" stroke-linecap="round"/>
<line x1="500" y1="470" x2="432.7" y2="64.9" stroke="var(--accent-primary)" stroke-opacity="0.74" stroke-width="0.71" stroke-linecap="round"/>
<line x1="500" y1="470" x2="468.2" y2="256.6" stroke="var(--accent-primary)" stroke-opacity="0.12" stroke-width="1.53" stroke-linecap="round"/>
<line x1="500" y1="470" x2="474.7" y2="260.3" stroke="var(--accent-primary)" stroke-opacity="0.66" stroke-width="1.26" stroke-linecap="round"/>
<line x1="500" y1="470" x2="485.5" y2="337.8" stroke="var(--accent-primary)" stroke-opacity="0.17" stroke-width="1.66" stroke-linecap="round"/>
<line x1="500" y1="470" x2="478.7" y2="171.6" stroke="var(--accent-primary)" stroke-opacity="0.73" stroke-width="1.3" stroke-linecap="round"/>
<line x1="500" y1="470" x2="486.3" y2="301.0" stroke="var(--accent-primary)" stroke-opacity="0.35" stroke-width="1.68" stroke-linecap="round"/>
<line x1="500" y1="470" x2="485.3" y2="73.5" stroke="var(--accent-primary)" stroke-opacity="0.78" stroke-width="0.85" stroke-linecap="round"/>
<line x1="500" y1="470" x2="493.8" y2="308.3" stroke="var(--accent-primary)" stroke-opacity="0.69" stroke-width="1.66" stroke-linecap="round"/>
<line x1="500" y1="470" x2="495.7" y2="147.6" stroke="var(--accent-primary)" stroke-opacity="0.23" stroke-width="1.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="510.2" y2="37.5" stroke="var(--accent-primary)" stroke-opacity="0.71" stroke-width="1.66" stroke-linecap="round"/>
<line x1="500" y1="470" x2="503.8" y2="111.7" stroke="var(--accent-primary)" stroke-opacity="0.36" stroke-width="1.72" stroke-linecap="round"/>
<line x1="500" y1="470" x2="523.8" y2="72.9" stroke="var(--accent-primary)" stroke-opacity="0.71" stroke-width="0.92" stroke-linecap="round"/>
<line x1="500" y1="470" x2="512.8" y2="307.0" stroke="var(--accent-primary)" stroke-opacity="0.76" stroke-width="1.63" stroke-linecap="round"/>
<line x1="500" y1="470" x2="529.2" y2="88.0" stroke="var(--accent-primary)" stroke-opacity="0.46" stroke-width="0.97" stroke-linecap="round"/>
<line x1="500" y1="470" x2="523.5" y2="270.8" stroke="var(--accent-primary)" stroke-opacity="0.14" stroke-width="0.83" stroke-linecap="round"/>
<line x1="500" y1="470" x2="547.2" y2="74.9" stroke="var(--accent-primary)" stroke-opacity="0.83" stroke-width="0.93" stroke-linecap="round"/>
<line x1="500" y1="470" x2="538.0" y2="219.0" stroke="var(--accent-primary)" stroke-opacity="0.84" stroke-width="1.24" stroke-linecap="round"/>
<line x1="500" y1="470" x2="529.9" y2="307.0" stroke="var(--accent-primary)" stroke-opacity="0.83" stroke-width="0.81" stroke-linecap="round"/>
<line x1="500" y1="470" x2="542.4" y2="262.0" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="1.12" stroke-linecap="round"/>
<line x1="500" y1="470" x2="547.7" y2="247.8" stroke="var(--accent-primary)" stroke-opacity="0.56" stroke-width="1.21" stroke-linecap="round"/>
<line x1="500" y1="470" x2="566.7" y2="168.5" stroke="var(--accent-primary)" stroke-opacity="0.31" stroke-width="1.45" stroke-linecap="round"/>
<line x1="500" y1="470" x2="591.9" y2="63.3" stroke="var(--accent-primary)" stroke-opacity="0.51" stroke-width="1.46" stroke-linecap="round"/>
<line x1="500" y1="470" x2="590.2" y2="144.4" stroke="var(--accent-primary)" stroke-opacity="0.39" stroke-width="0.68" stroke-linecap="round"/>
<line x1="500" y1="470" x2="565.7" y2="247.1" stroke="var(--accent-primary)" stroke-opacity="0.35" stroke-width="1.62" stroke-linecap="round"/>
<line x1="500" y1="470" x2="567.6" y2="257.4" stroke="var(--accent-primary)" stroke-opacity="0.35" stroke-width="1.09" stroke-linecap="round"/>
<line x1="500" y1="470" x2="568.7" y2="259.3" stroke="var(--accent-primary)" stroke-opacity="0.21" stroke-width="1.1" stroke-linecap="round"/>
<line x1="500" y1="470" x2="618.2" y2="151.2" stroke="var(--accent-primary)" stroke-opacity="0.78" stroke-width="1.34" stroke-linecap="round"/>
<line x1="500" y1="470" x2="602.8" y2="188.3" stroke="var(--accent-primary)" stroke-opacity="0.12" stroke-width="0.94" stroke-linecap="round"/>
<line x1="500" y1="470" x2="613.2" y2="181.6" stroke="var(--accent-primary)" stroke-opacity="0.6" stroke-width="1.16" stroke-linecap="round"/>
<line x1="500" y1="470" x2="575.3" y2="288.8" stroke="var(--accent-primary)" stroke-opacity="0.47" stroke-width="1.68" stroke-linecap="round"/>
<line x1="500" y1="470" x2="575.6" y2="303.8" stroke="var(--accent-primary)" stroke-opacity="0.18" stroke-width="1.22" stroke-linecap="round"/>
<line x1="500" y1="470" x2="599.6" y2="258.4" stroke="var(--accent-primary)" stroke-opacity="0.72" stroke-width="1.5" stroke-linecap="round"/>
<line x1="500" y1="470" x2="588.7" y2="291.2" stroke="var(--accent-primary)" stroke-opacity="0.27" stroke-width="0.63" stroke-linecap="round"/>
<line x1="500" y1="470" x2="624.0" y2="222.0" stroke="var(--accent-primary)" stroke-opacity="0.74" stroke-width="0.69" stroke-linecap="round"/>
<line x1="500" y1="470" x2="652.9" y2="182.9" stroke="var(--accent-primary)" stroke-opacity="0.26" stroke-width="1.44" stroke-linecap="round"/>
<line x1="500" y1="470" x2="600.7" y2="290.7" stroke="var(--accent-primary)" stroke-opacity="0.6" stroke-width="0.61" stroke-linecap="round"/>
<line x1="500" y1="470" x2="689.9" y2="153.9" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="1.11" stroke-linecap="round"/>
<line x1="500" y1="470" x2="718.8" y2="103.4" stroke="var(--accent-primary)" stroke-opacity="0.5" stroke-width="0.66" stroke-linecap="round"/>
<line x1="500" y1="470" x2="708.8" y2="137.1" stroke="var(--accent-primary)" stroke-opacity="0.45" stroke-width="1.56" stroke-linecap="round"/>
<line x1="500" y1="470" x2="744.8" y2="108.9" stroke="var(--accent-primary)" stroke-opacity="0.55" stroke-width="1.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="686.8" y2="210.3" stroke="var(--accent-primary)" stroke-opacity="0.65" stroke-width="1.21" stroke-linecap="round"/>
<line x1="500" y1="470" x2="679.2" y2="229.6" stroke="var(--accent-primary)" stroke-opacity="0.77" stroke-width="1.49" stroke-linecap="round"/>
<line x1="500" y1="470" x2="626.7" y2="302.1" stroke="var(--accent-primary)" stroke-opacity="0.3" stroke-width="1.37" stroke-linecap="round"/>
<line x1="500" y1="470" x2="682.6" y2="242.7" stroke="var(--accent-primary)" stroke-opacity="0.58" stroke-width="0.93" stroke-linecap="round"/>
<line x1="500" y1="470" x2="635.7" y2="298.6" stroke="var(--accent-primary)" stroke-opacity="0.32" stroke-width="0.98" stroke-linecap="round"/>
<line x1="500" y1="470" x2="612.3" y2="338.5" stroke="var(--accent-primary)" stroke-opacity="0.29" stroke-width="1.43" stroke-linecap="round"/>
<line x1="500" y1="470" x2="600.2" y2="358.5" stroke="var(--accent-primary)" stroke-opacity="0.42" stroke-width="1.25" stroke-linecap="round"/>
<line x1="500" y1="470" x2="631.0" y2="326.7" stroke="var(--accent-primary)" stroke-opacity="0.43" stroke-width="1.69" stroke-linecap="round"/>
<line x1="500" y1="470" x2="739.7" y2="221.0" stroke="var(--accent-primary)" stroke-opacity="0.75" stroke-width="1.52" stroke-linecap="round"/>
<line x1="500" y1="470" x2="592.5" y2="376.1" stroke="var(--accent-primary)" stroke-opacity="0.38" stroke-width="1.5" stroke-linecap="round"/>
<line x1="500" y1="470" x2="809.8" y2="178.2" stroke="var(--accent-primary)" stroke-opacity="0.43" stroke-width="1.5" stroke-linecap="round"/>
<line x1="500" y1="470" x2="732.4" y2="254.4" stroke="var(--accent-primary)" stroke-opacity="0.28" stroke-width="0.86" stroke-linecap="round"/>
<line x1="500" y1="470" x2="603.3" y2="377.0" stroke="var(--accent-primary)" stroke-opacity="0.37" stroke-width="1.41" stroke-linecap="round"/>
<line x1="500" y1="470" x2="636.8" y2="351.3" stroke="var(--accent-primary)" stroke-opacity="0.46" stroke-width="0.75" stroke-linecap="round"/>
<line x1="500" y1="470" x2="607.0" y2="382.2" stroke="var(--accent-primary)" stroke-opacity="0.41" stroke-width="1.28" stroke-linecap="round"/>
<line x1="500" y1="470" x2="753.8" y2="260.3" stroke="var(--accent-primary)" stroke-opacity="0.22" stroke-width="1.15" stroke-linecap="round"/>
<line x1="500" y1="470" x2="694.0" y2="316.2" stroke="var(--accent-primary)" stroke-opacity="0.27" stroke-width="0.99" stroke-linecap="round"/>
<line x1="500" y1="470" x2="700.9" y2="325.4" stroke="var(--accent-primary)" stroke-opacity="0.67" stroke-width="1.6" stroke-linecap="round"/>
<line x1="500" y1="470" x2="626.1" y2="379.2" stroke="var(--accent-primary)" stroke-opacity="0.13" stroke-width="1.25" stroke-linecap="round"/>
<line x1="500" y1="470" x2="700.0" y2="340.1" stroke="var(--accent-primary)" stroke-opacity="0.59" stroke-width="1.54" stroke-linecap="round"/>
<line x1="500" y1="470" x2="806.3" y2="273.7" stroke="var(--accent-primary)" stroke-opacity="0.81" stroke-width="0.84" stroke-linecap="round"/>
<line x1="500" y1="470" x2="648.7" y2="373.6" stroke="var(--accent-primary)" stroke-opacity="0.21" stroke-width="1.4" stroke-linecap="round"/>
<line x1="500" y1="470" x2="670.0" y2="369.3" stroke="var(--accent-primary)" stroke-opacity="0.63" stroke-width="1.52" stroke-linecap="round"/>
<line x1="500" y1="470" x2="774.5" y2="308.9" stroke="var(--accent-primary)" stroke-opacity="0.67" stroke-width="0.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="879.3" y2="269.5" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="0.63" stroke-linecap="round"/>
<line x1="500" y1="470" x2="800.5" y2="311.1" stroke="var(--accent-primary)" stroke-opacity="0.82" stroke-width="1.08" stroke-linecap="round"/>
<line x1="500" y1="470" x2="638.2" y2="403.0" stroke="var(--accent-primary)" stroke-opacity="0.62" stroke-width="1.35" stroke-linecap="round"/>
<line x1="500" y1="470" x2="831.7" y2="307.3" stroke="var(--accent-primary)" stroke-opacity="0.74" stroke-width="1.32" stroke-linecap="round"/>
<line x1="500" y1="470" x2="894.3" y2="286.4" stroke="var(--accent-primary)" stroke-opacity="0.69" stroke-width="1.02" stroke-linecap="round"/>
<line x1="500" y1="470" x2="725.1" y2="373.6" stroke="var(--accent-primary)" stroke-opacity="0.49" stroke-width="1.01" stroke-linecap="round"/>
<line x1="500" y1="470" x2="859.0" y2="331.1" stroke="var(--accent-primary)" stroke-opacity="0.2" stroke-width="1.75" stroke-linecap="round"/>
<line x1="500" y1="470" x2="862.4" y2="334.4" stroke="var(--accent-primary)" stroke-opacity="0.64" stroke-width="1.12" stroke-linecap="round"/>
<line x1="500" y1="470" x2="905.4" y2="328.8" stroke="var(--accent-primary)" stroke-opacity="0.32" stroke-width="1.57" stroke-linecap="round"/>
<line x1="500" y1="470" x2="765.4" y2="381.1" stroke="var(--accent-primary)" stroke-opacity="0.44" stroke-width="1.48" stroke-linecap="round"/>
<line x1="500" y1="470" x2="874.7" y2="348.2" stroke="var(--accent-primary)" stroke-opacity="0.73" stroke-width="0.7" stroke-linecap="round"/>
<line x1="500" y1="470" x2="698.1" y2="414.9" stroke="var(--accent-primary)" stroke-opacity="0.46" stroke-width="1.33" stroke-linecap="round"/>
<line x1="500" y1="470" x2="633.8" y2="432.8" stroke="var(--accent-primary)" stroke-opacity="0.74" stroke-width="0.82" stroke-linecap="round"/>
<line x1="500" y1="470" x2="864.8" y2="373.6" stroke="var(--accent-primary)" stroke-opacity="0.37" stroke-width="1.66" stroke-linecap="round"/>
<line x1="500" y1="470" x2="710.4" y2="422.9" stroke="var(--accent-primary)" stroke-opacity="0.13" stroke-width="1.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="844.3" y2="391.3" stroke="var(--accent-primary)" stroke-opacity="0.48" stroke-width="1.51" stroke-linecap="round"/>
<line x1="500" y1="470" x2="824.8" y2="410.2" stroke="var(--accent-primary)" stroke-opacity="0.48" stroke-width="1.55" stroke-linecap="round"/>
<line x1="500" y1="470" x2="695.3" y2="433.3" stroke="var(--accent-primary)" stroke-opacity="0.63" stroke-width="0.97" stroke-linecap="round"/>
<line x1="500" y1="470" x2="773.7" y2="429.3" stroke="var(--accent-primary)" stroke-opacity="0.51" stroke-width="1.11" stroke-linecap="round"/>
<line x1="500" y1="470" x2="730.8" y2="441.7" stroke="var(--accent-primary)" stroke-opacity="0.63" stroke-width="0.93" stroke-linecap="round"/>
<line x1="500" y1="470" x2="666.2" y2="449.7" stroke="var(--accent-primary)" stroke-opacity="0.26" stroke-width="0.74" stroke-linecap="round"/>
<line x1="500" y1="470" x2="864.7" y2="436.5" stroke="var(--accent-primary)" stroke-opacity="0.26" stroke-width="0.86" stroke-linecap="round"/>
<line x1="500" y1="470" x2="853.6" y2="443.7" stroke="var(--accent-primary)" stroke-opacity="0.83" stroke-width="1.23" stroke-linecap="round"/>
<line x1="500" y1="470" x2="660.8" y2="459.9" stroke="var(--accent-primary)" stroke-opacity="0.26" stroke-width="0.87" stroke-linecap="round"/>
<line x1="500" y1="470" x2="634.2" y2="463.7" stroke="var(--accent-primary)" stroke-opacity="0.51" stroke-width="0.93" stroke-linecap="round"/>
<circle cx="308.9" cy="455.7" r="3.2" fill="var(--accent-primary)" fill-opacity="0.74"/>
<circle cx="186.1" cy="399.3" r="1.7" fill="var(--accent-primary)" fill-opacity="0.9"/>
<circle cx="159.1" cy="367.3" r="2.7" fill="var(--accent-primary)" fill-opacity="0.39"/>
<circle cx="190.8" cy="359.4" r="1.9" fill="var(--accent-primary)" fill-opacity="0.68"/>
<circle cx="300.4" cy="395.7" r="3.4" fill="var(--accent-primary)" fill-opacity="0.42000000000000004"/>
<circle cx="290.2" cy="382.0" r="3.0" fill="var(--accent-primary)" fill-opacity="0.75"/>
<circle cx="320.6" cy="386.3" r="2.5" fill="var(--accent-primary)" fill-opacity="1"/>
<circle cx="216.1" cy="304.3" r="2.7" fill="var(--accent-primary)" fill-opacity="0.35"/>
<circle cx="258.9" cy="296.2" r="2.0" fill="var(--accent-primary)" fill-opacity="0.84"/>
<circle cx="195.5" cy="229.9" r="1.9" fill="var(--accent-primary)" fill-opacity="0.49"/>
<circle cx="172.5" cy="201.4" r="1.6" fill="var(--accent-primary)" fill-opacity="0.32999999999999996"/>
<circle cx="353.9" cy="339.9" r="2.0" fill="var(--accent-primary)" fill-opacity="0.66"/>
<circle cx="397.2" cy="302.8" r="1.9" fill="var(--accent-primary)" fill-opacity="0.5599999999999999"/>
<circle cx="277.6" cy="95.6" r="2.9" fill="var(--accent-primary)" fill-opacity="0.45999999999999996"/>
<circle cx="333.9" cy="158.9" r="1.8" fill="var(--accent-primary)" fill-opacity="0.38"/>
<circle cx="384.7" cy="181.2" r="1.7" fill="var(--accent-primary)" fill-opacity="0.65"/>
<circle cx="427.4" cy="259.1" r="2.1" fill="var(--accent-primary)" fill-opacity="0.5"/>
<circle cx="410.5" cy="213.1" r="3.3" fill="var(--accent-primary)" fill-opacity="1"/>
<circle cx="434.8" cy="175.6" r="1.7" fill="var(--accent-primary)" fill-opacity="0.47"/>
<circle cx="485.5" cy="337.8" r="2.7" fill="var(--accent-primary)" fill-opacity="0.32"/>
<circle cx="485.3" cy="73.5" r="2.2" fill="var(--accent-primary)" fill-opacity="0.93"/>
<circle cx="493.8" cy="308.3" r="2.8" fill="var(--accent-primary)" fill-opacity="0.84"/>
<circle cx="510.2" cy="37.5" r="2.8" fill="var(--accent-primary)" fill-opacity="0.86"/>
<circle cx="503.8" cy="111.7" r="3.3" fill="var(--accent-primary)" fill-opacity="0.51"/>
<circle cx="523.5" cy="270.8" r="2.2" fill="var(--accent-primary)" fill-opacity="0.29000000000000004"/>
<circle cx="590.2" cy="144.4" r="2.3" fill="var(--accent-primary)" fill-opacity="0.54"/>
<circle cx="618.2" cy="151.2" r="2.6" fill="var(--accent-primary)" fill-opacity="0.93"/>
<circle cx="575.6" cy="303.8" r="3.4" fill="var(--accent-primary)" fill-opacity="0.32999999999999996"/>
<circle cx="588.7" cy="291.2" r="1.9" fill="var(--accent-primary)" fill-opacity="0.42000000000000004"/>
<circle cx="600.7" cy="290.7" r="3.4" fill="var(--accent-primary)" fill-opacity="0.75"/>
<circle cx="708.8" cy="137.1" r="3.1" fill="var(--accent-primary)" fill-opacity="0.6"/>
<circle cx="626.7" cy="302.1" r="3.1" fill="var(--accent-primary)" fill-opacity="0.44999999999999996"/>
<circle cx="635.7" cy="298.6" r="2.6" fill="var(--accent-primary)" fill-opacity="0.47"/>
<circle cx="809.8" cy="178.2" r="3.0" fill="var(--accent-primary)" fill-opacity="0.58"/>
<circle cx="753.8" cy="260.3" r="2.1" fill="var(--accent-primary)" fill-opacity="0.37"/>
<circle cx="700.0" cy="340.1" r="2.6" fill="var(--accent-primary)" fill-opacity="0.74"/>
<circle cx="806.3" cy="273.7" r="2.6" fill="var(--accent-primary)" fill-opacity="0.9600000000000001"/>
<circle cx="774.5" cy="308.9" r="1.9" fill="var(--accent-primary)" fill-opacity="0.8200000000000001"/>
<circle cx="638.2" cy="403.0" r="3.0" fill="var(--accent-primary)" fill-opacity="0.77"/>
<circle cx="831.7" cy="307.3" r="2.9" fill="var(--accent-primary)" fill-opacity="0.89"/>
<circle cx="698.1" cy="414.9" r="2.5" fill="var(--accent-primary)" fill-opacity="0.61"/>
<circle cx="864.7" cy="436.5" r="2.4" fill="var(--accent-primary)" fill-opacity="0.41000000000000003"/>
<circle cx="660.8" cy="459.9" r="2.8" fill="var(--accent-primary)" fill-opacity="0.41000000000000003"/>
                </svg>
                </div>
                <div class="starburst-content">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($L['stats'] as $i => $s)
                            <div class="text-center reveal rd{{ $i + 1 }}">
                                <div class="text-4xl md:text-5xl font-extrabold text-gradient tabular">
                                    <span class="counter" data-count="{{ $s['v'] }}" data-decimals="{{ $s['d'] ?? 0 }}">0</span>{{ $s['s'] }}
                                </div>
                                <div class="text-sm mt-2 c2">{{ $s['l'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- SHOWCASE — 3 флагманских инструмента с фирменными иллюстрациями -->
    <section class="pt-8 pb-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="showcase-section-head text-center max-w-2xl mx-auto reveal">
                <div class="badge-pill mx-auto"><span class="dot"></span>{{ $L['showcase']['eyebrow'] }}</div>
                <h2 class="text-3xl md:text-5xl font-extrabold">{{ $L['showcase']['title'] }} <span class="h-accent">{{ $L['showcase']['accent'] }}</span></h2>
            </div>
            <div class="showcase-grid">
                <!-- Card 1: анализ рисков — мокап отчёта -->
                <div class="showcase-card tilt-card has-glare reveal reveal-3d rd1" data-tilt-max="9">
                    <span class="showcase-expand" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M16 4h4v4M20 16v4h-4M8 20H4v-4"/></svg>
                    </span>
                    <h3 class="showcase-heading">{{ $L['showcase']['items'][0]['t'] }}</h3>
                    <p class="showcase-desc">{{ $L['showcase']['items'][0]['d'] }}</p>
                    <div class="showcase-visual">
                        <div class="mock-report" style="text-align:left;">
                            <div class="mock-row" style="margin-bottom:0; gap:.6rem;">
                                <span class="tint tint-accent" style="width:26px; height:26px; border-radius:8px; flex-shrink:0;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['doc'] !!}</svg>
                                </span>
                                <div style="min-width:0;">
                                    <div class="c1" style="font-size:.7rem; font-weight:700;">{{ app()->getLocale() === 'en' ? 'Supply_agreement.docx' : (app()->getLocale() === 'tg' ? 'Шартномаи_таҳвил.docx' : 'Договор_поставки.docx') }}</div>
                                    <div class="c3" style="font-size:.62rem;">{{ app()->getLocale() === 'en' ? 'Analyzed in 58 sec' : (app()->getLocale() === 'tg' ? 'Дар 58 сония таҳлил шуд' : 'Проанализирован за 58 сек') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mock-report mock-gauge-card">
                            <div class="mock-row" style="margin-bottom:0; gap:.85rem; align-items:center;">
                                <span class="mock-gauge-wrap">
                                    <svg class="mock-gauge" viewBox="0 0 100 100">
                                        <defs>
                                            <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#F59E0B"/>
                                                <stop offset="100%" stop-color="#DC2626"/>
                                            </linearGradient>
                                        </defs>
                                        <circle cx="50" cy="50" r="42" fill="none" stroke="var(--card-border)" stroke-width="8"/>
                                        <circle class="mock-gauge-progress" cx="50" cy="50" r="42" fill="none" stroke="url(#gaugeGrad)" stroke-width="8" stroke-linecap="round" transform="rotate(-90 50 50)"/>
                                    </svg>
                                    <span class="mock-gauge-num c1">72</span>
                                </span>
                                <div style="min-width:0;">
                                    <div class="c1" style="font-size:.78rem; font-weight:800;">{{ app()->getLocale() === 'en' ? 'Risk score: 72/100' : (app()->getLocale() === 'tg' ? 'Балли хавф: 72/100' : 'Риск-балл: 72/100') }}</div>
                                    <span class="mock-badge" style="background:rgba(239,68,68,.12); color:#DC2626; margin-top:.3rem; display:inline-block;">{{ app()->getLocale() === 'en' ? 'High risk' : (app()->getLocale() === 'tg' ? 'Хавфи баланд' : 'Высокий риск') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mock-report">
                            <div class="mock-row" style="margin-bottom:0;">
                                <svg class="w-3.5 h-3.5 shrink-0" style="color:var(--accent-primary)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['book'] !!}</svg>
                                <span class="c2" style="font-size:.68rem;">{{ app()->getLocale() === 'en' ? 'Art. 350, Art. 421 — Civil Code RT' : (app()->getLocale() === 'tg' ? 'мод. 350, мод. 421 — КГ ҶТ' : 'ст. 350, ст. 421 — ГК РТ') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: генератор договоров — «карточка» документа -->
                <div class="showcase-card tilt-card has-glare reveal reveal-3d rd2" data-tilt-max="9">
                    <span class="showcase-expand" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M16 4h4v4M20 16v4h-4M8 20H4v-4"/></svg>
                    </span>
                    <h3 class="showcase-heading">{{ $L['showcase']['items'][1]['t'] }}</h3>
                    <p class="showcase-desc">{{ $L['showcase']['items'][1]['d'] }}</p>
                    <div class="showcase-visual">
                        <div class="mock-doc-card">
                            <div class="mock-doc-lines">
                                <span style="width:60%;"></span>
                                <span style="width:85%;"></span>
                                <span style="width:40%;"></span>
                            </div>
                            <div class="mock-doc-foot">
                                <span class="mock-doc-word">LegalAI Pro</span>
                                <span class="mock-doc-seal">
                                    <svg class="w-3.5 h-3.5" style="color:#fff" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
                                </span>
                            </div>
                        </div>
                        <div class="mock-stamp" aria-hidden="true">
                            <svg viewBox="0 0 120 120">
                                <defs>
                                    <path id="stampCirclePath" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"/>
                                </defs>
                                <circle cx="60" cy="60" r="50" fill="var(--card-solid)" opacity=".98"/>
                                <circle cx="60" cy="60" r="52" fill="none" stroke="var(--accent-primary)" stroke-width="2" stroke-dasharray="3 4" opacity=".85"/>
                                <circle cx="60" cy="60" r="44" fill="none" stroke="var(--accent-primary)" stroke-width="1.4" opacity=".6"/>
                                <text font-size="8" font-weight="700" fill="var(--accent-primary)" letter-spacing="2">
                                    <textPath href="#stampCirclePath" startOffset="0%">{{ app()->getLocale() === 'en' ? 'VERIFIED · LEGALAI PRO · VERIFIED ·' : (app()->getLocale() === 'tg' ? 'САНҶИДА ШУД · LEGALAI PRO · САНҶИДА ШУД ·' : 'ПРОВЕРЕНО · LEGALAI PRO · ПРОВЕРЕНО ·') }}</textPath>
                                </text>
                                <path d="M42 61l12 12 24-26" stroke="var(--accent-primary)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: сеть норм права — точечный граф -->
                <div class="showcase-card showcase-card-lg tilt-card has-glare reveal reveal-3d rd3" data-tilt-max="9">
                    <span class="showcase-expand" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4h4M16 4h4v4M20 16v4h-4M8 20H4v-4"/></svg>
                    </span>
                    <h3 class="showcase-heading">{{ $L['showcase']['items'][2]['t'] }}</h3>
                    <p class="showcase-desc">{{ $L['showcase']['items'][2]['d'] }}</p>
                    <div class="showcase-visual showcase-visual-lg">
                        <div class="mock-globe-scene mock-globe-scene-lg">
                            <div class="mock-globe-orbit mock-globe-orbit-a"></div>
                            <div class="mock-globe-orbit mock-globe-orbit-b"></div>
                            <div class="mock-globe-sphere mock-globe-sphere-lg">
<span class="globe-dot" style="transform:translate3d(0.0px,128.0px,0.0px);width:2.35px;height:2.35px;opacity:0.62"></span>
<span class="globe-dot" style="transform:translate3d(-10.2px,127.2px,9.4px);width:2.43px;height:2.43px;opacity:0.64"></span>
<span class="globe-dot" style="transform:translate3d(1.7px,126.5px,-19.5px);width:2.19px;height:2.19px;opacity:0.57"></span>
<span class="globe-dot" style="transform:translate3d(14.6px,125.7px,19.0px);width:2.51px;height:2.51px;opacity:0.66"></span>
<span class="globe-dot" style="transform:translate3d(-27.2px,125.0px,-4.8px);width:2.31px;height:2.31px;opacity:0.61"></span>
<span class="globe-dot" style="transform:translate3d(26.0px,124.2px,-16.6px);width:2.21px;height:2.21px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(-8.8px,123.5px,32.6px);width:2.62px;height:2.62px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-16.8px,122.7px,-32.3px);width:2.08px;height:2.08px;opacity:0.54"></span>
<span class="globe-dot" style="transform:translate3d(36.5px,122.0px,13.3px);width:2.46px;height:2.46px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(-38.0px,121.2px,15.7px);width:2.48px;height:2.48px;opacity:0.66"></span>
<span class="globe-dot" style="transform:translate3d(18.4px,120.4px,-39.2px);width:2.03px;height:2.03px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(13.6px,119.7px,43.3px);width:2.71px;height:2.71px;opacity:0.72"></span>
<span class="globe-dot" style="transform:translate3d(-40.9px,118.9px,-23.7px);width:2.16px;height:2.16px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(48.0px,118.2px,-10.6px);width:2.26px;height:2.26px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(-29.3px,117.4px,41.7px);width:2.69px;height:2.69px;opacity:0.72"></span>
<span class="globe-dot" style="transform:translate3d(-6.8px,116.7px,-52.2px);width:1.92px;height:1.92px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(41.5px,115.9px,35.0px);width:2.64px;height:2.64px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-55.8px,115.2px,2.3px);width:2.37px;height:2.37px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(40.7px,114.4px,-40.5px);width:2.02px;height:2.02px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(-2.7px,113.7px,58.8px);width:2.83px;height:2.83px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-38.6px,112.9px,-46.3px);width:1.97px;height:1.97px;opacity:0.51"></span>
<span class="globe-dot" style="transform:translate3d(61.2px,112.1px,8.2px);width:2.42px;height:2.42px;opacity:0.64"></span>
<span class="globe-dot" style="transform:translate3d(-51.8px,111.4px,36.0px);width:2.65px;height:2.65px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(14.1px,110.6px,-62.8px);width:1.83px;height:1.83px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(32.6px,109.9px,57.0px);width:2.82px;height:2.82px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(-63.7px,109.1px,-20.3px);width:2.18px;height:2.18px;opacity:0.57"></span>
<span class="globe-dot" style="transform:translate3d(61.8px,108.4px,-28.6px);width:2.12px;height:2.12px;opacity:0.55"></span>
<span class="globe-dot" style="transform:translate3d(-26.8px,107.6px,63.9px);width:2.87px;height:2.87px;opacity:0.77"></span>
<span class="globe-dot" style="transform:translate3d(-23.9px,106.9px,-66.3px);width:1.81px;height:1.81px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(63.4px,106.1px,33.3px);width:2.62px;height:2.62px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-70.3px,105.3px,18.5px);width:2.5px;height:2.5px;opacity:0.66"></span>
<span class="globe-dot" style="transform:translate3d(39.9px,104.6px,-62.1px);width:1.84px;height:1.84px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(12.7px,103.8px,73.8px);width:2.96px;height:2.96px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-60.0px,103.1px,-46.5px);width:1.97px;height:1.97px;opacity:0.51"></span>
<span class="globe-dot" style="transform:translate3d(76.6px,102.3px,-6.3px);width:2.3px;height:2.3px;opacity:0.61"></span>
<span class="globe-dot" style="transform:translate3d(-52.9px,101.6px,57.2px);width:2.82px;height:2.82px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(0.4px,100.8px,-78.9px);width:1.7px;height:1.7px;opacity:0.44"></span>
<span class="globe-dot" style="transform:translate3d(53.6px,100.1px,59.1px);width:2.83px;height:2.83px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-80.4px,99.3px,-7.5px);width:2.29px;height:2.29px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(65.1px,98.5px,-49.4px);width:1.94px;height:1.94px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(-14.8px,97.8px,81.3px);width:3.02px;height:3.02px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-44.5px,97.0px,-70.6px);width:1.77px;height:1.77px;opacity:0.45"></span>
<span class="globe-dot" style="transform:translate3d(81.3px,96.3px,22.3px);width:2.53px;height:2.53px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(-75.8px,95.5px,38.9px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(29.9px,94.8px,-80.7px);width:1.69px;height:1.69px;opacity:0.43"></span>
<span class="globe-dot" style="transform:translate3d(32.7px,94.0px,80.4px);width:3.01px;height:3.01px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-79.2px,93.3px,-37.5px);width:2.04px;height:2.04px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(84.5px,92.5px,-26.1px);width:2.14px;height:2.14px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(-45.1px,91.8px,77.0px);width:2.98px;height:2.98px;opacity:0.8"></span>
<span class="globe-dot" style="transform:translate3d(-18.9px,91.0px,-88.0px);width:1.63px;height:1.63px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(74.0px,90.2px,52.6px);width:2.78px;height:2.78px;opacity:0.74"></span>
<span class="globe-dot" style="transform:translate3d(-90.8px,89.5px,11.3px);width:2.44px;height:2.44px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(59.8px,88.7px,-70.3px);width:1.77px;height:1.77px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(3.4px,88.0px,92.9px);width:3.11px;height:3.11px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-65.8px,87.2px,-66.7px);width:1.8px;height:1.8px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(94.3px,86.5px,4.8px);width:2.39px;height:2.39px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(-73.3px,85.7px,60.6px);width:2.85px;height:2.85px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(13.2px,85.0px,-94.8px);width:1.57px;height:1.57px;opacity:0.4"></span>
<span class="globe-dot" style="transform:translate3d(54.7px,84.2px,79.4px);width:3.0px;height:3.0px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-94.6px,83.4px,-21.8px);width:2.17px;height:2.17px;opacity:0.57"></span>
<span class="globe-dot" style="transform:translate3d(85.0px,82.7px,-48.2px);width:1.95px;height:1.95px;opacity:0.51"></span>
<span class="globe-dot" style="transform:translate3d(-30.3px,81.9px,93.5px);width:3.12px;height:3.12px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-41.1px,81.2px,-90.0px);width:1.61px;height:1.61px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(91.7px,80.4px,38.9px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(-94.4px,79.7px,33.4px);width:2.62px;height:2.62px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(47.3px,78.9px,-89.0px);width:1.62px;height:1.62px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(25.4px,78.2px,98.1px);width:3.16px;height:3.16px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-85.5px,77.4px,-55.6px);width:1.89px;height:1.89px;opacity:0.49"></span>
<span class="globe-dot" style="transform:translate3d(101.1px,76.6px,-16.9px);width:2.21px;height:2.21px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(-63.5px,75.9px,81.2px);width:3.02px;height:3.02px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-8.1px,75.1px,-103.3px);width:1.5px;height:1.5px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(76.1px,74.4px,71.1px);width:2.93px;height:2.93px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-104.7px,73.6px,-1.0px);width:2.34px;height:2.34px;opacity:0.62"></span>
<span class="globe-dot" style="transform:translate3d(78.3px,72.9px,-70.3px);width:1.77px;height:1.77px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(-10.3px,72.1px,105.2px);width:3.21px;height:3.21px;opacity:0.87"></span>
<span class="globe-dot" style="transform:translate3d(-63.8px,71.4px,-85.0px);width:1.65px;height:1.65px;opacity:0.42"></span>
<span class="globe-dot" style="transform:translate3d(104.9px,70.6px,19.6px);width:2.51px;height:2.51px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(-91.1px,69.9px,56.7px);width:2.81px;height:2.81px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(29.0px,69.1px,-103.8px);width:1.5px;height:1.5px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(48.9px,68.3px,96.5px);width:3.14px;height:3.14px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-101.7px,67.6px,-38.3px);width:2.04px;height:2.04px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(101.3px,66.8px,-40.7px);width:2.02px;height:2.02px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(-47.4px,66.1px,98.8px);width:3.16px;height:3.16px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-31.9px,65.3px,-105.3px);width:1.49px;height:1.49px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(95.1px,64.6px,56.3px);width:2.81px;height:2.81px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(-108.6px,63.8px,22.8px);width:2.54px;height:2.54px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(65.0px,63.1px,-90.5px);width:1.61px;height:1.61px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(13.3px,62.3px,111.0px);width:3.26px;height:3.26px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-85.1px,61.5px,-73.2px);width:1.75px;height:1.75px;opacity:0.45"></span>
<span class="globe-dot" style="transform:translate3d(112.6px,60.8px,-3.6px);width:2.32px;height:2.32px;opacity:0.61"></span>
<span class="globe-dot" style="transform:translate3d(-80.9px,60.0px,79.0px);width:3.0px;height:3.0px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(6.3px,59.3px,-113.3px);width:1.42px;height:1.42px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(72.1px,58.5px,88.1px);width:3.07px;height:3.07px;opacity:0.83"></span>
<span class="globe-dot" style="transform:translate3d(-113.0px,57.8px,-16.3px);width:2.22px;height:2.22px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(94.7px,57.0px,-64.5px);width:1.82px;height:1.82px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(-26.3px,56.3px,111.9px);width:3.27px;height:3.27px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-56.4px,55.5px,-100.6px);width:1.52px;height:1.52px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(109.9px,54.7px,36.2px);width:2.65px;height:2.65px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-105.8px,54.0px,47.6px);width:2.74px;height:2.74px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(46.0px,53.2px,-106.9px);width:1.47px;height:1.47px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(38.4px,52.5px,110.2px);width:3.25px;height:3.25px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-103.1px,51.7px,-55.5px);width:1.89px;height:1.89px;opacity:0.49"></span>
<span class="globe-dot" style="transform:translate3d(113.8px,51.0px,-28.8px);width:2.11px;height:2.11px;opacity:0.55"></span>
<span class="globe-dot" style="transform:translate3d(-64.6px,50.2px,98.4px);width:3.16px;height:3.16px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-18.9px,49.5px,-116.5px);width:1.39px;height:1.39px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(92.9px,48.7px,73.4px);width:2.95px;height:2.95px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-118.4px,48.0px,8.6px);width:2.42px;height:2.42px;opacity:0.64"></span>
<span class="globe-dot" style="transform:translate3d(81.6px,47.2px,-86.5px);width:1.64px;height:1.64px;opacity:0.42"></span>
<span class="globe-dot" style="transform:translate3d(-1.7px,46.4px,119.3px);width:3.33px;height:3.33px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-79.5px,45.7px,-89.3px);width:1.62px;height:1.62px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(119.2px,44.9px,12.2px);width:2.45px;height:2.45px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(-96.4px,44.2px,71.7px);width:2.94px;height:2.94px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(22.7px,43.4px,-118.2px);width:1.38px;height:1.38px;opacity:0.34"></span>
<span class="globe-dot" style="transform:translate3d(63.3px,42.7px,102.8px);width:3.19px;height:3.19px;opacity:0.86"></span>
<span class="globe-dot" style="transform:translate3d(-116.3px,41.9px,-33.1px);width:2.08px;height:2.08px;opacity:0.54"></span>
<span class="globe-dot" style="transform:translate3d(108.4px,41.2px,-54.3px);width:1.9px;height:1.9px;opacity:0.49"></span>
<span class="globe-dot" style="transform:translate3d(-43.3px,40.4px,113.5px);width:3.28px;height:3.28px;opacity:0.89"></span>
<span class="globe-dot" style="transform:translate3d(-44.8px,39.6px,-113.2px);width:1.42px;height:1.42px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(109.7px,38.9px,53.3px);width:2.79px;height:2.79px;opacity:0.74"></span>
<span class="globe-dot" style="transform:translate3d(-117.1px,38.1px,34.9px);width:2.64px;height:2.64px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(62.9px,37.4px,-105.0px);width:1.49px;height:1.49px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(24.6px,36.6px,120.2px);width:3.34px;height:3.34px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-99.5px,35.9px,-72.1px);width:1.76px;height:1.76px;opacity:0.45"></span>
<span class="globe-dot" style="transform:translate3d(122.3px,35.1px,-14.0px);width:2.23px;height:2.23px;opacity:0.59"></span>
<span class="globe-dot" style="transform:translate3d(-80.8px,34.4px,93.1px);width:3.11px;height:3.11px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-3.3px,33.6px,-123.5px);width:1.34px;height:1.34px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(86.0px,32.8px,89.0px);width:3.08px;height:3.08px;opacity:0.83"></span>
<span class="globe-dot" style="transform:translate3d(-123.7px,32.1px,-7.5px);width:2.29px;height:2.29px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(96.4px,31.3px,-78.1px);width:1.71px;height:1.71px;opacity:0.44"></span>
<span class="globe-dot" style="transform:translate3d(-18.4px,30.6px,122.9px);width:3.36px;height:3.36px;opacity:0.91"></span>
<span class="globe-dot" style="transform:translate3d(-69.6px,29.8px,-103.2px);width:1.5px;height:1.5px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(121.2px,29.1px,29.1px);width:2.59px;height:2.59px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(-109.2px,28.3px,60.5px);width:2.85px;height:2.85px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(39.7px,27.6px,-118.5px);width:1.38px;height:1.38px;opacity:0.34"></span>
<span class="globe-dot" style="transform:translate3d(50.8px,26.8px,114.4px);width:3.29px;height:3.29px;opacity:0.89"></span>
<span class="globe-dot" style="transform:translate3d(-114.9px,26.1px,-50.1px);width:1.94px;height:1.94px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(118.7px,25.3px,-40.7px);width:2.02px;height:2.02px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(-60.1px,24.5px,110.3px);width:3.26px;height:3.26px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-30.3px,23.8px,-122.1px);width:1.35px;height:1.35px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(104.9px,23.0px,69.6px);width:2.92px;height:2.92px;opacity:0.78"></span>
<span class="globe-dot" style="transform:translate3d(-124.5px,22.3px,19.5px);width:2.51px;height:2.51px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(78.7px,21.5px,-98.6px);width:1.54px;height:1.54px;opacity:0.39"></span>
<span class="globe-dot" style="transform:translate3d(8.6px,20.8px,126.0px);width:3.38px;height:3.38px;opacity:0.92"></span>
<span class="globe-dot" style="transform:translate3d(-91.5px,20.0px,-87.2px);width:1.63px;height:1.63px;opacity:0.42"></span>
<span class="globe-dot" style="transform:translate3d(126.5px,19.3px,2.5px);width:2.37px;height:2.37px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(-95.0px,18.5px,83.7px);width:3.04px;height:3.04px;opacity:0.82"></span>
<span class="globe-dot" style="transform:translate3d(13.5px,17.7px,-126.0px);width:1.32px;height:1.32px;opacity:0.32"></span>
<span class="globe-dot" style="transform:translate3d(75.2px,17.0px,102.2px);width:3.19px;height:3.19px;opacity:0.86"></span>
<span class="globe-dot" style="transform:translate3d(-124.6px,16.2px,-24.6px);width:2.15px;height:2.15px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(108.5px,15.5px,-66.1px);width:1.81px;height:1.81px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(-35.4px,14.7px,122.1px);width:3.35px;height:3.35px;opacity:0.91"></span>
<span class="globe-dot" style="transform:translate3d(-56.4px,14.0px,-114.0px);width:1.41px;height:1.41px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(118.7px,13.2px,46.0px);width:2.73px;height:2.73px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(-118.7px,12.5px,46.3px);width:2.73px;height:2.73px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(56.3px,11.7px,-114.4px);width:1.41px;height:1.41px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(35.8px,10.9px,122.4px);width:3.35px;height:3.35px;opacity:0.91"></span>
<span class="globe-dot" style="transform:translate3d(-109.1px,10.2px,-66.1px);width:1.81px;height:1.81px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(125.2px,9.4px,-25.0px);width:2.15px;height:2.15px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(-75.5px,8.7px,103.0px);width:3.2px;height:3.2px;opacity:0.86"></span>
<span class="globe-dot" style="transform:translate3d(-13.9px,7.9px,-127.0px);width:1.31px;height:1.31px;opacity:0.32"></span>
<span class="globe-dot" style="transform:translate3d(96.1px,7.2px,84.3px);width:3.04px;height:3.04px;opacity:0.82"></span>
<span class="globe-dot" style="transform:translate3d(-127.8px,6.4px,2.8px);width:2.37px;height:2.37px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(92.4px,5.7px,-88.4px);width:1.62px;height:1.62px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(-8.4px,4.9px,127.6px);width:3.4px;height:3.4px;opacity:0.92"></span>
<span class="globe-dot" style="transform:translate3d(-80.0px,4.2px,-99.8px);width:1.53px;height:1.53px;opacity:0.39"></span>
<span class="globe-dot" style="transform:translate3d(126.5px,3.4px,19.5px);width:2.51px;height:2.51px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(-106.5px,2.6px,71.0px);width:2.93px;height:2.93px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(30.5px,1.9px,-124.3px);width:1.33px;height:1.33px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(61.5px,1.1px,112.3px);width:3.27px;height:3.27px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-121.2px,0.4px,-41.3px);width:2.01px;height:2.01px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(117.2px,-0.4px,-51.4px);width:1.93px;height:1.93px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(-51.7px,-1.1px,117.1px);width:3.31px;height:3.31px;opacity:0.89"></span>
<span class="globe-dot" style="transform:translate3d(-41.0px,-1.9px,-121.3px);width:1.36px;height:1.36px;opacity:0.34"></span>
<span class="globe-dot" style="transform:translate3d(112.1px,-2.6px,61.7px);width:2.86px;height:2.86px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-124.3px,-3.4px,30.2px);width:2.6px;height:2.6px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(71.3px,-4.2px,-106.2px);width:1.48px;height:1.48px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(19.2px,-4.9px,126.5px);width:3.39px;height:3.39px;opacity:0.92"></span>
<span class="globe-dot" style="transform:translate3d(-99.6px,-5.7px,-80.3px);width:1.69px;height:1.69px;opacity:0.43"></span>
<span class="globe-dot" style="transform:translate3d(127.6px,-6.4px,-8.1px);width:2.28px;height:2.28px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(-88.6px,-7.2px,92.1px);width:3.11px;height:3.11px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(3.1px,-7.9px,-127.7px);width:1.3px;height:1.3px;opacity:0.32"></span>
<span class="globe-dot" style="transform:translate3d(83.9px,-8.7px,96.2px);width:3.14px;height:3.14px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-126.9px,-9.4px,-14.3px);width:2.23px;height:2.23px;opacity:0.59"></span>
<span class="globe-dot" style="transform:translate3d(103.1px,-10.2px,-75.1px);width:1.73px;height:1.73px;opacity:0.44"></span>
<span class="globe-dot" style="transform:translate3d(-25.3px,-10.9px,125.0px);width:3.38px;height:3.38px;opacity:0.91"></span>
<span class="globe-dot" style="transform:translate3d(-65.8px,-11.7px,-109.2px);width:1.45px;height:1.45px;opacity:0.36"></span>
<span class="globe-dot" style="transform:translate3d(122.2px,-12.5px,36.1px);width:2.65px;height:2.65px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-114.4px,-13.2px,55.9px);width:2.81px;height:2.81px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(46.5px,-14.0px,-118.4px);width:1.38px;height:1.38px;opacity:0.34"></span>
<span class="globe-dot" style="transform:translate3d(45.6px,-14.7px,118.7px);width:3.32px;height:3.32px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-113.7px,-15.5px,-56.6px);width:1.89px;height:1.89px;opacity:0.49"></span>
<span class="globe-dot" style="transform:translate3d(122.0px,-16.2px,-35.0px);width:2.06px;height:2.06px;opacity:0.54"></span>
<span class="globe-dot" style="transform:translate3d(-66.3px,-17.0px,108.2px);width:3.24px;height:3.24px;opacity:0.87"></span>
<span class="globe-dot" style="transform:translate3d(-24.2px,-17.7px,-124.4px);width:1.33px;height:1.33px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(101.8px,-18.5px,75.3px);width:2.97px;height:2.97px;opacity:0.8"></span>
<span class="globe-dot" style="transform:translate3d(-125.9px,-19.3px,13.2px);width:2.46px;height:2.46px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(83.8px,-20.0px,-94.7px);width:1.57px;height:1.57px;opacity:0.4"></span>
<span class="globe-dot" style="transform:translate3d(2.1px,-20.8px,126.3px);width:3.39px;height:3.39px;opacity:0.92"></span>
<span class="globe-dot" style="transform:translate3d(-86.8px,-21.5px,-91.6px);width:1.6px;height:1.6px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(125.7px,-22.3px,8.9px);width:2.42px;height:2.42px;opacity:0.64"></span>
<span class="globe-dot" style="transform:translate3d(-98.6px,-23.0px,78.3px);width:2.99px;height:2.99px;opacity:0.8"></span>
<span class="globe-dot" style="transform:translate3d(19.8px,-23.8px,-124.2px);width:1.33px;height:1.33px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(69.2px,-24.5px,104.8px);width:3.21px;height:3.21px;opacity:0.87"></span>
<span class="globe-dot" style="transform:translate3d(-121.7px,-25.3px,-30.5px);width:2.1px;height:2.1px;opacity:0.55"></span>
<span class="globe-dot" style="transform:translate3d(110.2px,-26.1px,-59.6px);width:1.86px;height:1.86px;opacity:0.48"></span>
<span class="globe-dot" style="transform:translate3d(-40.9px,-26.8px,118.3px);width:3.32px;height:3.32px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-49.6px,-27.6px,-114.7px);width:1.41px;height:1.41px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(113.9px,-28.3px,51.0px);width:2.77px;height:2.77px;opacity:0.74"></span>
<span class="globe-dot" style="transform:translate3d(-118.3px,-29.1px,39.3px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(60.6px,-29.8px,-108.7px);width:1.46px;height:1.46px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(28.7px,-30.6px,120.9px);width:3.34px;height:3.34px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-102.7px,-31.3px,-69.6px);width:1.78px;height:1.78px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(122.6px,-32.1px,-18.0px);width:2.2px;height:2.2px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(-78.1px,-32.8px,95.9px);width:3.14px;height:3.14px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-7.2px,-33.6px,-123.3px);width:1.34px;height:1.34px;opacity:0.33"></span>
<span class="globe-dot" style="transform:translate3d(88.4px,-34.4px,85.9px);width:3.05px;height:3.05px;opacity:0.82"></span>
<span class="globe-dot" style="transform:translate3d(-123.0px,-35.1px,-3.6px);width:2.32px;height:2.32px;opacity:0.61"></span>
<span class="globe-dot" style="transform:translate3d(93.0px,-35.9px,-80.3px);width:1.69px;height:1.69px;opacity:0.43"></span>
<span class="globe-dot" style="transform:translate3d(-14.3px,-36.6px,121.8px);width:3.35px;height:3.35px;opacity:0.91"></span>
<span class="globe-dot" style="transform:translate3d(-71.6px,-37.4px,-99.3px);width:1.54px;height:1.54px;opacity:0.39"></span>
<span class="globe-dot" style="transform:translate3d(119.6px,-38.1px,24.8px);width:2.55px;height:2.55px;opacity:0.68"></span>
<span class="globe-dot" style="transform:translate3d(-104.8px,-38.9px,62.4px);width:2.86px;height:2.86px;opacity:0.77"></span>
<span class="globe-dot" style="transform:translate3d(35.0px,-39.6px,-116.6px);width:1.39px;height:1.39px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(52.8px,-40.4px,109.4px);width:3.25px;height:3.25px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-112.6px,-41.2px,-44.9px);width:1.98px;height:1.98px;opacity:0.51"></span>
<span class="globe-dot" style="transform:translate3d(113.1px,-41.9px,-42.9px);width:2.0px;height:2.0px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(-54.3px,-42.7px,107.8px);width:3.23px;height:3.23px;opacity:0.87"></span>
<span class="globe-dot" style="transform:translate3d(-32.7px,-43.4px,-115.9px);width:1.4px;height:1.4px;opacity:0.35"></span>
<span class="globe-dot" style="transform:translate3d(102.1px,-44.2px,63.3px);width:2.87px;height:2.87px;opacity:0.77"></span>
<span class="globe-dot" style="transform:translate3d(-117.8px,-44.9px,22.3px);width:2.53px;height:2.53px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(71.6px,-45.7px,-95.8px);width:1.56px;height:1.56px;opacity:0.4"></span>
<span class="globe-dot" style="transform:translate3d(11.9px,-46.4px,118.7px);width:3.32px;height:3.32px;opacity:0.9"></span>
<span class="globe-dot" style="transform:translate3d(-88.7px,-47.2px,-79.3px);width:1.7px;height:1.7px;opacity:0.43"></span>
<span class="globe-dot" style="transform:translate3d(118.7px,-48.0px,-1.4px);width:2.34px;height:2.34px;opacity:0.62"></span>
<span class="globe-dot" style="transform:translate3d(-86.3px,-48.7px,81.0px);width:3.01px;height:3.01px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(8.9px,-49.5px,-117.7px);width:1.38px;height:1.38px;opacity:0.34"></span>
<span class="globe-dot" style="transform:translate3d(72.8px,-50.2px,92.6px);width:3.11px;height:3.11px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-115.9px,-51.0px,-19.1px);width:2.19px;height:2.19px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(98.0px,-51.7px,-64.0px);width:1.82px;height:1.82px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(-28.9px,-52.5px,113.1px);width:3.28px;height:3.28px;opacity:0.89"></span>
<span class="globe-dot" style="transform:translate3d(-54.9px,-53.2px,-102.6px);width:1.51px;height:1.51px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(109.5px,-54.0px,38.5px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(-106.4px,-54.7px,45.4px);width:2.72px;height:2.72px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(47.6px,-55.5px,-105.0px);width:1.49px;height:1.49px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(35.7px,-56.3px,109.3px);width:3.25px;height:3.25px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-99.8px,-57.0px,-56.3px);width:1.89px;height:1.89px;opacity:0.49"></span>
<span class="globe-dot" style="transform:translate3d(111.3px,-57.8px,-25.9px);width:2.14px;height:2.14px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(-64.3px,-58.5px,93.9px);width:3.12px;height:3.12px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-15.9px,-59.3px,-112.3px);width:1.43px;height:1.43px;opacity:0.36"></span>
<span class="globe-dot" style="transform:translate3d(87.3px,-60.0px,71.8px);width:2.94px;height:2.94px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-112.5px,-60.8px,6.0px);width:2.4px;height:2.4px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(78.6px,-61.5px,-80.1px);width:1.69px;height:1.69px;opacity:0.43"></span>
<span class="globe-dot" style="transform:translate3d(-3.8px,-62.3px,111.7px);width:3.27px;height:3.27px;opacity:0.88"></span>
<span class="globe-dot" style="transform:translate3d(-72.4px,-63.1px,-84.7px);width:1.66px;height:1.66px;opacity:0.42"></span>
<span class="globe-dot" style="transform:translate3d(110.1px,-63.8px,13.5px);width:2.46px;height:2.46px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(-90.0px,-64.6px,64.2px);width:2.88px;height:2.88px;opacity:0.77"></span>
<span class="globe-dot" style="transform:translate3d(22.9px,-65.3px,-107.7px);width:1.47px;height:1.47px;opacity:0.37"></span>
<span class="globe-dot" style="transform:translate3d(55.6px,-66.1px,94.5px);width:3.12px;height:3.12px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-104.4px,-66.8px,-31.9px);width:2.09px;height:2.09px;opacity:0.55"></span>
<span class="globe-dot" style="transform:translate3d(98.1px,-67.6px,-46.8px);width:1.97px;height:1.97px;opacity:0.51"></span>
<span class="globe-dot" style="transform:translate3d(-40.6px,-68.3px,100.3px);width:3.17px;height:3.17px;opacity:0.86"></span>
<span class="globe-dot" style="transform:translate3d(-37.7px,-69.1px,-100.9px);width:1.52px;height:1.52px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(95.5px,-69.9px,48.8px);width:2.75px;height:2.75px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(-102.9px,-70.6px,28.5px);width:2.58px;height:2.58px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(56.4px,-71.4px,-90.1px);width:1.61px;height:1.61px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(19.2px,-72.1px,104.0px);width:3.2px;height:3.2px;opacity:0.86"></span>
<span class="globe-dot" style="transform:translate3d(-84.0px,-72.9px,-63.4px);width:1.83px;height:1.83px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(104.2px,-73.6px,-9.9px);width:2.27px;height:2.27px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(-69.8px,-74.4px,77.3px);width:2.98px;height:2.98px;opacity:0.8"></span>
<span class="globe-dot" style="transform:translate3d(-0.7px,-75.1px,-103.6px);width:1.5px;height:1.5px;opacity:0.38"></span>
<span class="globe-dot" style="transform:translate3d(70.2px,-75.9px,75.5px);width:2.97px;height:2.97px;opacity:0.8"></span>
<span class="globe-dot" style="transform:translate3d(-102.2px,-76.6px,-8.2px);width:2.28px;height:2.28px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(80.5px,-77.4px,-62.6px);width:1.84px;height:1.84px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(-16.9px,-78.2px,99.9px);width:3.17px;height:3.17px;opacity:0.85"></span>
<span class="globe-dot" style="transform:translate3d(-54.7px,-78.9px,-84.6px);width:1.66px;height:1.66px;opacity:0.42"></span>
<span class="globe-dot" style="transform:translate3d(96.9px,-79.7px,25.3px);width:2.56px;height:2.56px;opacity:0.68"></span>
<span class="globe-dot" style="transform:translate3d(-88.0px,-80.4px,46.5px);width:2.73px;height:2.73px;opacity:0.73"></span>
<span class="globe-dot" style="transform:translate3d(33.3px,-81.2px,-93.2px);width:1.59px;height:1.59px;opacity:0.4"></span>
<span class="globe-dot" style="transform:translate3d(38.2px,-81.9px,90.6px);width:3.09px;height:3.09px;opacity:0.83"></span>
<span class="globe-dot" style="transform:translate3d(-88.8px,-82.7px,-40.8px);width:2.02px;height:2.02px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(92.4px,-83.4px,-29.7px);width:2.11px;height:2.11px;opacity:0.55"></span>
<span class="globe-dot" style="transform:translate3d(-47.7px,-84.2px,83.8px);width:3.04px;height:3.04px;opacity:0.82"></span>
<span class="globe-dot" style="transform:translate3d(-21.2px,-85.0px,-93.4px);width:1.58px;height:1.58px;opacity:0.4"></span>
<span class="globe-dot" style="transform:translate3d(78.2px,-85.7px,54.1px);width:2.79px;height:2.79px;opacity:0.75"></span>
<span class="globe-dot" style="transform:translate3d(-93.5px,-86.5px,12.8px);width:2.46px;height:2.46px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(59.9px,-87.2px,-72.1px);width:1.76px;height:1.76px;opacity:0.45"></span>
<span class="globe-dot" style="transform:translate3d(4.5px,-88.0px,92.9px);width:3.11px;height:3.11px;opacity:0.84"></span>
<span class="globe-dot" style="transform:translate3d(-65.5px,-88.7px,-64.9px);width:1.82px;height:1.82px;opacity:0.47"></span>
<span class="globe-dot" style="transform:translate3d(91.5px,-89.5px,3.6px);width:2.38px;height:2.38px;opacity:0.63"></span>
<span class="globe-dot" style="transform:translate3d(-69.3px,-90.2px,58.7px);width:2.83px;height:2.83px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(11.4px,-91.0px,-89.3px);width:1.62px;height:1.62px;opacity:0.41"></span>
<span class="globe-dot" style="transform:translate3d(51.5px,-91.8px,72.9px);width:2.95px;height:2.95px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-86.4px,-92.5px,-18.8px);width:2.2px;height:2.2px;opacity:0.58"></span>
<span class="globe-dot" style="transform:translate3d(75.8px,-93.3px,-44.1px);width:1.99px;height:1.99px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(-25.8px,-94.0px,82.9px);width:3.03px;height:3.03px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-36.6px,-94.8px,-77.8px);width:1.71px;height:1.71px;opacity:0.44"></span>
<span class="globe-dot" style="transform:translate3d(78.8px,-95.5px,32.3px);width:2.62px;height:2.62px;opacity:0.7"></span>
<span class="globe-dot" style="transform:translate3d(-79.2px,-96.3px,29.1px);width:2.59px;height:2.59px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(38.3px,-97.0px,-74.2px);width:1.74px;height:1.74px;opacity:0.45"></span>
<span class="globe-dot" style="transform:translate3d(21.6px,-97.8px,79.7px);width:3.0px;height:3.0px;opacity:0.81"></span>
<span class="globe-dot" style="transform:translate3d(-69.0px,-98.5px,-43.7px);width:1.99px;height:1.99px;opacity:0.52"></span>
<span class="globe-dot" style="transform:translate3d(79.5px,-99.3px,-14.3px);width:2.23px;height:2.23px;opacity:0.59"></span>
<span class="globe-dot" style="transform:translate3d(-48.4px,-100.1px,63.5px);width:2.87px;height:2.87px;opacity:0.77"></span>
<span class="globe-dot" style="transform:translate3d(-7.1px,-100.8px,-78.6px);width:1.71px;height:1.71px;opacity:0.44"></span>
<span class="globe-dot" style="transform:translate3d(57.6px,-101.6px,52.5px);width:2.78px;height:2.78px;opacity:0.74"></span>
<span class="globe-dot" style="transform:translate3d(-76.9px,-102.3px,0.2px);width:2.35px;height:2.35px;opacity:0.62"></span>
<span class="globe-dot" style="transform:translate3d(55.8px,-103.1px,-51.4px);width:1.93px;height:1.93px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(-6.4px,-103.8px,74.6px);width:2.96px;height:2.96px;opacity:0.79"></span>
<span class="globe-dot" style="transform:translate3d(-45.0px,-104.6px,-58.5px);width:1.87px;height:1.87px;opacity:0.48"></span>
<span class="globe-dot" style="transform:translate3d(71.6px,-105.3px,12.5px);width:2.45px;height:2.45px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(-60.3px,-106.1px,38.6px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(18.1px,-106.9px,-68.1px);width:1.79px;height:1.79px;opacity:0.46"></span>
<span class="globe-dot" style="transform:translate3d(32.1px,-107.6px,61.4px);width:2.85px;height:2.85px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-64.0px,-108.4px,-23.2px);width:2.16px;height:2.16px;opacity:0.57"></span>
<span class="globe-dot" style="transform:translate3d(61.8px,-109.1px,-25.7px);width:2.14px;height:2.14px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(-27.7px,-109.9px,59.5px);width:2.84px;height:2.84px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-19.4px,-110.6px,-61.4px);width:1.85px;height:1.85px;opacity:0.48"></span>
<span class="globe-dot" style="transform:translate3d(54.6px,-111.4px,31.5px);width:2.61px;height:2.61px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(-60.2px,-112.1px,13.4px);width:2.46px;height:2.46px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(34.6px,-112.9px,-49.4px);width:1.94px;height:1.94px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(7.7px,-113.7px,58.4px);width:2.83px;height:2.83px;opacity:0.76"></span>
<span class="globe-dot" style="transform:translate3d(-44.0px,-114.4px,-36.9px);width:2.05px;height:2.05px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(55.8px,-115.2px,-2.4px);width:2.33px;height:2.33px;opacity:0.61"></span>
<span class="globe-dot" style="transform:translate3d(-38.4px,-115.9px,38.4px);width:2.66px;height:2.66px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(2.3px,-116.7px,-52.6px);width:1.92px;height:1.92px;opacity:0.5"></span>
<span class="globe-dot" style="transform:translate3d(32.7px,-117.4px,39.0px);width:2.67px;height:2.67px;opacity:0.71"></span>
<span class="globe-dot" style="transform:translate3d(-48.7px,-118.2px,-6.4px);width:2.3px;height:2.3px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(38.8px,-118.9px,-27.1px);width:2.13px;height:2.13px;opacity:0.56"></span>
<span class="globe-dot" style="transform:translate3d(-9.9px,-119.7px,44.3px);width:2.71px;height:2.71px;opacity:0.72"></span>
<span class="globe-dot" style="transform:translate3d(-21.6px,-120.4px,-37.5px);width:2.04px;height:2.04px;opacity:0.53"></span>
<span class="globe-dot" style="transform:translate3d(39.2px,-121.2px,12.4px);width:2.45px;height:2.45px;opacity:0.65"></span>
<span class="globe-dot" style="transform:translate3d(-35.2px,-122.0px,16.4px);width:2.48px;height:2.48px;opacity:0.66"></span>
<span class="globe-dot" style="transform:translate3d(14.0px,-122.7px,-33.6px);width:2.07px;height:2.07px;opacity:0.54"></span>
<span class="globe-dot" style="transform:translate3d(11.5px,-123.5px,31.7px);width:2.61px;height:2.61px;opacity:0.69"></span>
<span class="globe-dot" style="transform:translate3d(-27.4px,-124.2px,-14.3px);width:2.23px;height:2.23px;opacity:0.59"></span>
<span class="globe-dot" style="transform:translate3d(26.7px,-125.0px,-7.1px);width:2.29px;height:2.29px;opacity:0.6"></span>
<span class="globe-dot" style="transform:translate3d(-12.9px,-125.7px,20.2px);width:2.52px;height:2.52px;opacity:0.67"></span>
<span class="globe-dot" style="transform:translate3d(-3.4px,-126.5px,-19.3px);width:2.19px;height:2.19px;opacity:0.57"></span>
<span class="globe-dot" style="transform:translate3d(11.0px,-127.2px,8.5px);width:2.42px;height:2.42px;opacity:0.64"></span>
<span class="globe-dot" style="transform:translate3d(-0.0px,-128.0px,0.0px);width:2.35px;height:2.35px;opacity:0.62"></span>
<span class="globe-pin" style="transform:translate3d(60.1px,-43.8px,104.2px);"></span>
<span class="globe-pin" style="transform:translate3d(116.2px,33.1px,-42.3px);"></span>
<span class="globe-pin" style="transform:translate3d(-78.4px,-90.5px,45.3px);"></span>
<span class="globe-pin" style="transform:translate3d(-67.4px,73.4px,-80.3px);"></span>
<span class="globe-pin" style="transform:translate3d(-43.1px,-22.2px,-118.5px);"></span>
<span class="globe-pin" style="transform:translate3d(52.9px,98.1px,63.0px);"></span>
                            </div>
                            <svg class="mock-globe-arc" viewBox="0 0 320 340" aria-hidden="true">
                                <path class="mock-globe-arc-path" d="M18,236 C 90,60 210,40 300,96" fill="none" stroke="url(#arcGrad)" stroke-width="2" stroke-linecap="round"/>
                                <defs>
                                    <linearGradient id="arcGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="var(--accent-primary)" stop-opacity="0"/>
                                        <stop offset="50%" stop-color="var(--accent-primary)" stop-opacity="1"/>
                                        <stop offset="100%" stop-color="#8B5CF6" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="mock-globe-badge mock-globe-badge-a">
                                <span class="tint tint-accent" style="width:22px;height:22px;border-radius:7px;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['book'] !!}</svg>
                                </span>
                                <span class="c1" style="font-size:.68rem; font-weight:800;">142 {{ app()->getLocale() === 'en' ? 'articles linked' : (app()->getLocale() === 'tg' ? 'модда пайваст' : 'статьи связаны') }}</span>
                            </div>
                            <div class="mock-globe-badge mock-globe-badge-b">
                                <span class="tint tint-success" style="width:22px;height:22px;border-radius:7px;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg>
                                </span>
                                <span class="c1" style="font-size:.68rem; font-weight:800;">5 {{ app()->getLocale() === 'en' ? 'codes of RT' : (app()->getLocale() === 'tg' ? 'кодекси ҶТ' : 'кодексов РТ') }}</span>
                            </div>
                            <div class="mock-globe-glow"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROBLEM / SOLUTION -->
    <section class="py-20 px-4 sm:px-6 reveal">
        <div class="max-w-7xl mx-auto">
            <div class="max-w-3xl mx-auto text-center mb-14">
                <div class="badge-pill mb-4" style="background:rgba(244,63,94,.08); color:#F43F5E; border-color:rgba(244,63,94,.2);"><span class="dot" style="background:#F43F5E"></span>{{ $L['prob']['badge'] }}</div>
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['prob']['title'] }} <span class="h-accent">{{ $L['prob']['accent'] }}</span></h2>
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
                                    <span class="mark mark-bad"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">{!! $icons['x'] !!}</svg></span>
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
                    <span class="ribbon ribbon-good">{{ $L['prob']['a_label'] }}</span>
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
                                    <span class="mark mark-good"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">{!! $icons['check'] !!}</svg></span>
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
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['tools']['title'] }} <span class="h-accent">{{ $L['tools']['accent'] }}</span></h2>
                <p class="text-lg c2">{{ $L['tools']['sub'] }}</p>
            </div>
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5">
                @foreach($L['tools']['items'] as $i => $tool)
                    <div class="card-gradient card-hover feature-card tool-card-3d tilt-card has-glare p-6 flex flex-col reveal reveal-3d rd{{ ($i % 4) + 1 }}" data-tilt-max="6">
                        <div class="icon-tile icon-tile-pop mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons[$toolIcons[$i]] !!}</svg>
                        </div>
                        <h3 class="text-[15px] font-bold mb-1.5 tool-card-layer">{{ $tool['t'] }}</h3>
                        <p class="text-[13px] mb-4 c2 tool-card-layer">{{ $tool['d'] }}</p>
                        <ul class="space-y-2 mb-4 flex-1">
                            @foreach($tool['b'] as $b)
                                <li class="bullet"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">{!! $icons['check'] !!}</svg><span>{{ $b }}</span></li>
                            @endforeach
                        </ul>
                        @isset($tool['note'])
                            <p class="tool-note">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $icons['warning'] !!}</svg>
                                <span>{{ $tool['note'] }}</span>
                            </p>
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
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $L['how']['title'] }} <span class="h-accent">{{ $L['how']['accent'] }}</span></h2>
                <p class="text-lg c2">{{ $L['how']['sub'] }}</p>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute top-[58px] left-[18%] right-[18%] step-line"></div>
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($L['how']['steps'] as $i => $step)
                        <div class="card-gradient card-hover p-8 text-center relative reveal rd{{ $i + 1 }}">
                            <div class="step-num">{{ $i + 1 }}</div>
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
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['demo']['title'] }}<br><span class="h-accent">{{ $L['demo']['accent'] }}</span></h2>
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
                            <div class="icon-tile icon-tile-round mx-auto mb-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">{!! $icons[$docIcons[$i]] !!}</svg>
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
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">{{ $L['examples']['title'] }}</h2>
                <p class="text-lg c2">{{ $L['examples']['sub'] }}</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($L['examples']['items'] as $i => $ex)
                    <div class="card-gradient card-hover feature-card p-6 reveal rd{{ ($i % 2) + 1 }}">
                        <div class="flex items-start gap-4">
                            <span class="icon-tile icon-tile-round shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">{!! $icons[$docIcons[$i]] !!}</svg>
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
            <h2 class="text-2xl md:text-4xl font-extrabold text-center mb-8">{{ $L['about']['title'] }}</h2>
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
                            <span class="avatar">{{ mb_substr($t['n'], 0, 1) }}</span>
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

    <!-- FAQ -->
    <section class="py-20 px-4 sm:px-6 reveal" x-data="{ open: 0 }">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <div class="badge-pill mb-4">{{ $L['faq']['badge'] }}</div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-3">{{ $L['faq']['title'] }} <span class="h-accent">{{ $L['faq']['accent'] }}</span></h2>
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
                            <span class="tint tint-accent !w-9 !h-9"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $icons['doc'] !!}</svg></span>
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
<footer class="site-footer py-14 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <svg class="w-8 h-8" style="color:var(--accent-primary)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $icons['shield'] !!}</svg>
                    <span class="text-lg font-extrabold c1">LegalAI Pro</span>
                </div>
                <p class="text-[13px] leading-relaxed c2">{{ $L['footer']['about'] }}</p>
            </div>
            <div>
                <h4 class="foot-h">{{ $L['footer']['product'] }}</h4>
                <ul class="space-y-2.5">
                    @foreach($L['tools']['items'] as $i => $tool)
                        <li><button onclick="openTool('t{{ $i + 1 }}')" class="foot-link">{{ $tool['t'] }}</button></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="foot-h">{{ $L['footer']['resources'] }}</h4>
                <ul class="space-y-2.5">@foreach($L['footer']['res'] as $r)<li><a href="#" class="foot-link">{{ $r }}</a></li>@endforeach</ul>
            </div>
            <div>
                <h4 class="foot-h">{{ $L['footer']['company'] }}</h4>
                <ul class="space-y-2.5">@foreach($L['footer']['comp'] as $c)<li><a href="#" class="foot-link">{{ $c }}</a></li>@endforeach</ul>
            </div>
        </div>
        <div class="h-px mb-6" style="background:var(--hairline);"></div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs c3">{{ str_replace(':year', date('Y'), $L['footer']['copyright']) }}</p>
            <div class="flex flex-wrap gap-x-6 gap-y-2">
                <a href="#" class="foot-link !text-xs">{{ $L['footer']['privacy'] }}</a>
                <a href="#" class="foot-link !text-xs">{{ $L['footer']['terms'] }}</a>
                <a href="#" class="foot-link !text-xs">{{ $L['footer']['support'] }}</a>
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
    /* Шапка обретает границу и тень только после отрыва от верха страницы */
    document.querySelector('nav')?.classList.toggle('scrolled', h.scrollTop > 8);
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
   CARD GLOW — мягкий блик за курсором + лёгкий подъём (без наклона)
   ================================================================ */
function initTilt3D() {
    if (REDUCED || !window.matchMedia('(pointer: fine)').matches) return;

    document.querySelectorAll('.tilt-card').forEach(el => {
        let raf = null;

        el.addEventListener('mouseenter', () => el.classList.add('tilting'));

        el.addEventListener('mousemove', (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width;
            const py = (e.clientY - r.top) / r.height;

            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(() => {
                el.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
                el.style.setProperty('--my', (py * 100).toFixed(1) + '%');
            });
        });

        el.addEventListener('mouseleave', () => {
            el.classList.remove('tilting');
        });
    });
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
    initTilt3D();
});
</script>
</body>
</html>