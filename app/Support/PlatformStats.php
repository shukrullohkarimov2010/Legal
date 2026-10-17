<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Настоящие цифры платформы для главной страницы.
 *
 * Почти всё здесь проверяемо: сколько статей и кодексов РТ реально проиндексировано
 * (спрашиваем сервис анализа), сколько документов обработано (история приложения) и сколько
 * AI-инструментов есть на рабочем столе. Исключение — «точность» (ACCURACY): её значение задаёт владелец продукта.
 */
class PlatformStats
{
    /** Инструментов на рабочем столе (resources/views/dashboard.blade.php, массив $tools). */
    public const TOOLS = 8;

    /** Заявленная точность анализа, %. Это маркетинговое значение, а не результат замера: меняется здесь. */
    public const ACCURACY = 98.7;

    private const CACHE_KEY = 'platform_stats_v2';
    private const LAST_GOOD_KEY = 'platform_stats_law_base_last_good';

    /** @return array{accuracy: float, articles: ?int, codes: ?int, processed: int, tools: int} */
    public static function snapshot(): array
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $law = self::lawBase();
        $stats = [
            'accuracy' => self::ACCURACY,
            'articles' => $law['articles'] ?? null,
            'codes' => $law['codes'] ?? null,
            'processed' => self::processedDocuments(),
            'tools' => self::TOOLS,
        ];

        // Если сервис анализа недоступен, не долбим его при каждом открытии страницы: повторим через полминуты.
        Cache::put(self::CACHE_KEY, $stats, $law ? now()->addMinutes(10) : now()->addSeconds(30));

        return $stats;
    }

    /** Статьи и кодексы из каталога сервиса анализа; при сбое — последнее удачное значение. */
    private static function lawBase(): ?array
    {
        try {
            $response = Http::timeout(1.5)->get(rtrim((string) env('PYTHON_API_URL', 'http://127.0.0.1:5000'), '/') . '/api/codex/catalog');
            if ($response->ok()) {
                $data = $response->json();
                $articles = (int) ($data['total_articles'] ?? 0);
                $codes = is_array($data['codes'] ?? null) ? count($data['codes']) : 0;
                if ($articles > 0 && $codes > 0) {
                    $law = ['articles' => $articles, 'codes' => $codes];
                    Cache::forever(self::LAST_GOOD_KEY, $law);

                    return $law;
                }
            }
        } catch (\Throwable) {
            // сервис выключен — ниже возьмём последнее известное
        }

        $last = Cache::get(self::LAST_GOOD_KEY);

        return is_array($last) ? $last : null;
    }

    /**
     * Документы, обработанные всеми пользователями: анализы и сравнения, генерации и договоры из реестра.
     * Генерация пишет две записи (в историю генераций и в реестр) с одним временем и файлом — считаем её один раз,
     * так же, как кабинет пользователя.
     */
    private static function processedDocuments(): int
    {
        $path = base_path('history/document.json');
        if (!File::exists($path)) {
            return 0;
        }
        $data = json_decode((string) File::get($path), true);
        if (!is_array($data)) {
            return 0;
        }

        $count = count($data['activity_log'] ?? []);
        $generationKeys = [];
        foreach ($data['generation_history'] ?? [] as $record) {
            $generationKeys[($record['created_at'] ?? '') . '|' . ($record['file_name'] ?? '')] = true;
            $count++;
        }
        foreach ($data['contract_registry'] ?? [] as $record) {
            if (!isset($generationKeys[($record['created_at'] ?? '') . '|' . ($record['file_name'] ?? '')])) {
                $count++;
            }
        }

        return $count;
    }
}
