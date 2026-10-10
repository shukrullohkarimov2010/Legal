<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * При языке «tg» подключает к HTML-странице слой перевода интерфейса.
 *
 * Страницы проекта написаны по-русски и частично собирают текст в JavaScript, поэтому
 * словарь (lang/tg/phrases.json) применяется в браузере: сразу после загрузки и при
 * каждом изменении страницы. Пока слой не отработал, страница скрыта, чтобы русский
 * текст не мелькал; если скрипт не загрузился, она показывается через 2,5 секунды.
 *
 * Главная и страницы, переведённые через __(), слою не нужны, но он им не мешает:
 * строки, которых нет в словаре, он не трогает.
 */
class InjectTajikRuntime
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (App::getLocale() !== 'tg' || $request->is('/') || $request->is('i18n/*')) {
            return $response;
        }

        if (!str_contains((string) $response->headers->get('Content-Type', ''), 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || !str_contains($content, '</head>')) {
            return $response;
        }

        $dictVersion = @filemtime(lang_path('tg/phrases.json')) ?: 0;
        $runtimeVersion = @filemtime(public_path('js/i18n-runtime.js')) ?: 0;

        $snippet = '<style>html.i18n-wait body{visibility:hidden}</style>'
            . '<script>document.documentElement.classList.add("i18n-wait");'
            . 'setTimeout(function(){document.documentElement.classList.remove("i18n-wait")},2500);</script>'
            . '<script src="' . route('i18n.tg', ['v' => $dictVersion], false) . '" defer></script>'
            . '<script src="/js/i18n-runtime.js?v=' . $runtimeVersion . '" defer></script>';

        // Один раз, перед закрывающим </head>.
        $response->setContent(preg_replace('#</head>#i', $snippet . '</head>', $content, 1));

        return $response;
    }
}
