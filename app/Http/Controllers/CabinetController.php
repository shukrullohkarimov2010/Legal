<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CabinetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Задачи — только свои: раньше сюда попадали задачи всех пользователей,
        // а счётчики считались по всей таблице.
        $tasks = Task::query()->where('user_id', $user->id)->latest()->limit(5)->get();
        $sessions = $this->sessionsForUser((int) $user->id, $request->session()->getId());
        $documentHistory = $this->documentHistory((int) $user->id);
        $documents = $this->documents($documentHistory);

        // Цифры считаются по тому же списку, что вкладка «Все»: генерация и её запись в
        // реестре — один документ, а не два.
        $visible = $documents->where('in_all', true);
        $counts = $visible->countBy('kind');
        $stats = [
            'documents' => $visible->count(),
            'analyses' => (int) ($counts['analysis'] ?? 0),
            'comparisons' => (int) ($counts['comparison'] ?? 0),
            'contracts_made' => (int) ($counts['generation'] ?? 0) + (int) ($counts['contract'] ?? 0),
            'tasks_open' => Task::query()->where('user_id', $user->id)
                ->where('status', '!=', Task::STATUS_COMPLETED)->count(),
        ];

        $currentDevice = $this->describeDevice((string) $request->userAgent());
        $currentDevice['ip'] = $request->ip();

        return view('cabinet.index', compact(
            'user',
            'tasks',
            'sessions',
            'documents',
            'stats',
            'currentDevice'
        ));
    }

    /**
     * Данные для рабочего стола: те же цифры и тот же список документов, что в кабинете,
     * но коротко — счётчики, пять последних документов и открытые задачи.
     */
    public function dashboardSummary(int $userId, int $taskLimit = 4): array
    {
        $visible = $this->documents($this->documentHistory($userId))->where('in_all', true);
        $counts = $visible->countBy('kind');

        $openTasks = Task::query()->where('user_id', $userId)
            ->where('status', '!=', Task::STATUS_COMPLETED);

        return [
            'stats' => [
                'documents' => $visible->count(),
                'analyses' => (int) ($counts['analysis'] ?? 0),
                'contracts' => (int) ($counts['generation'] ?? 0) + (int) ($counts['contract'] ?? 0),
                'tasks' => (clone $openTasks)->count(),
            ],
            'recent' => $visible->take(5)->values(),
            'tasks' => $openTasks->orderByRaw('deadline is null')->orderBy('deadline')->orderBy('priority')->limit($taskLimit)->get(),
        ];
    }

    /**
     * Все документы пользователя одним списком, от новых к старым.
     *
     * История лежит в трёх массивах (действия, генерации, реестр договоров), и в
     * кабинете они были тремя узкими колонками. Человеку нужен один список с
     * фильтром по виду. Генерация пишет две записи (в генерации и в реестр) с одним
     * временем и файлом: в общем списке остаётся одна, договор показывается на
     * своей вкладке.
     */
    private function documents(array $history): \Illuminate\Support\Collection
    {
        $items = collect();

        foreach ($history['activity_log'] as $index => $record) {
            $title = (string) ($record['action_type'] ?? 'Действие');
            $isComparison = mb_stripos($title, 'сравнен') !== false;
            $summary = (string) ($record['summary'] ?? '');
            $result = $this->stringify($record['result'] ?? $record['response'] ?? $record['ai_response'] ?? '');
            $file = (string) ($record['file_name'] ?? '');

            $items->push([
                'kind' => $isComparison ? 'comparison' : 'analysis',
                'delete_type' => 'activity',
                'id' => (string) ($record['timestamp'] ?? $record['id'] ?? $index),
                'title' => $file !== '' ? $file : $title,
                'label' => $title,
                'details' => (string) ($record['details'] ?? ''),
                'summary' => $summary,
                'result' => $result,
                'status' => '',
                'risk' => $this->parseRisk($summary . ' ' . ($record['details'] ?? '')),
                'compat' => $this->parseCompatibility($result),
                'date' => $this->parseDate($record['timestamp'] ?? null),
                'in_all' => true,
            ]);
        }

        $generationKeys = [];
        foreach ($history['generation_history'] as $index => $record) {
            $created = (string) ($record['created_at'] ?? '');
            $file = (string) ($record['file_name'] ?? '');
            $generationKeys[$created . '|' . $file] = true;
            $prompt = (string) ($record['input_prompt'] ?? '');

            $items->push([
                'kind' => 'generation',
                'delete_type' => 'generation',
                'id' => (string) ($record['generation_id'] ?? $record['id'] ?? $index),
                'title' => $file !== '' ? $file : ($prompt !== '' ? \Illuminate\Support\Str::limit($prompt, 70) : 'Генерация договора'),
                'label' => 'Генерация договора',
                'details' => $prompt,
                'summary' => (string) ($record['summary'] ?? ''),
                'result' => $this->stringify($record['result'] ?? ''),
                'status' => (string) ($record['status'] ?? ''),
                'risk' => null,
                'compat' => null,
                'date' => $this->parseDate($record['created_at'] ?? null),
                'in_all' => true,
            ]);
        }

        foreach ($history['contract_registry'] as $index => $record) {
            $created = (string) ($record['created_at'] ?? '');
            $file = (string) ($record['file_name'] ?? '');
            $type = (string) ($record['contract_type'] ?? 'Договор');

            $items->push([
                'kind' => 'contract',
                'delete_type' => 'contract',
                'id' => (string) ($record['contract_id'] ?? $record['id'] ?? $index),
                'title' => $file !== '' ? $file : $type,
                'label' => $type,
                'details' => '',
                'summary' => (string) ($record['summary'] ?? ''),
                'result' => $this->stringify($record['result'] ?? ''),
                'status' => (string) ($record['status'] ?? ''),
                'risk' => null,
                'compat' => null,
                'date' => $this->parseDate($record['created_at'] ?? null),
                'in_all' => !isset($generationKeys[$created . '|' . $file]),
            ]);
        }

        return $items
            ->filter(fn (array $item) => $item['date'] instanceof Carbon)
            ->sortByDesc('date')
            ->values();
    }

    private function stringify(mixed $value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '';
        }

        return (string) ($value ?? '');
    }

    /** «Средний риск (43/100)» -> ['level' => 'medium', 'label' => 'Средний риск', 'score' => 43]. */
    private function parseRisk(string $text): ?array
    {
        if (!preg_match('/(Низкий|Средний|Высокий)\s+риск[^()]{0,20}\((\d{1,3})\s*\/\s*100\)/iu', $text, $m)) {
            return null;
        }

        $level = match (mb_strtolower($m[1])) {
            'низкий' => 'low',
            'высокий' => 'high',
            default => 'medium',
        };

        return ['level' => $level, 'label' => mb_convert_case($m[1], MB_CASE_TITLE) . ' риск', 'score' => min(100, (int) $m[2])];
    }

    private function parseCompatibility(string $text): ?int
    {
        return preg_match('/Совместимость[:\s]+(\d{1,3})\s*%/iu', $text, $m) ? min(100, (int) $m[1]) : null;
    }

    /** Браузер и система по User-Agent: «Chrome · Windows» вместо строки в 150 символов. */
    private function describeDevice(string $userAgent): array
    {
        $browser = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'YaBrowser') => 'Яндекс Браузер',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Браузер',
        };
        $system = match (true) {
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'неизвестная система',
        };

        return ['browser' => $browser, 'system' => $system, 'mobile' => in_array($system, ['Android', 'iOS'], true)];
    }

    public function storeActivity(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:analysis,generation,comparison,contract',
            'title' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:2000',
            'summary' => 'nullable|string|max:4000',
            'file_name' => 'nullable|string|max:500',
            'result' => 'nullable',
            'status' => 'nullable|string|max:80',
            'contract_type' => 'nullable|string|max:255',
            'result_link' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        $history = $this->readHistoryFile();
        $user = $request->user();
        $now = now()->format('Y-m-d H:i:s');

        if (in_array($data['type'], ['analysis', 'comparison'], true)) {
            array_unshift($history['activity_log'], [
                'timestamp' => $now,
                'user' => $user->id,
                'action_type' => $data['title'] ?? ($data['type'] === 'comparison' ? 'Сравнение договоров' : 'Анализ документа'),
                'ip_address' => $request->ip(),
                'details' => $data['details'] ?? ($data['type'] === 'comparison' ? 'Договоры сравнены.' : 'Документ отправлен на анализ.'),
                'summary' => $data['summary'] ?? '',
                'file_name' => $data['file_name'] ?? '',
                'result' => $data['result'] ?? '',
                'metadata' => $data['metadata'] ?? [],
            ]);
        }

        if ($data['type'] === 'generation') {
            array_unshift($history['generation_history'], [
                'generation_id' => 'gen_'.Str::lower(Str::random(10)),
                'input_prompt' => $data['details'] ?? ($data['title'] ?? 'Генерация договора'),
                'model_version' => $data['metadata']['model'] ?? 'local',
                'result_link' => $data['result_link'] ?? '',
                'summary' => $data['summary'] ?? '',
                'file_name' => $data['file_name'] ?? '',
                'result' => $data['result'] ?? '',
                'metadata' => $data['metadata'] ?? [],
                'status' => $data['status'] ?? 'completed',
                'created_at' => $now,
                'user' => $user->id,
            ]);
        }

        if (in_array($data['type'], ['generation', 'contract'], true)) {
            array_unshift($history['contract_registry'], [
                'contract_id' => 'cont_'.Str::lower(Str::random(10)),
                'contract_type' => $data['contract_type'] ?? ($data['title'] ?? 'Договор'),
                'counterparties' => $data['metadata']['counterparties'] ?? [],
                'status' => $data['status'] ?? 'created',
                'created_at' => $now,
                'signed_at' => null,
                'file_path' => $data['result_link'] ?? '',
                'summary' => $data['summary'] ?? '',
                'file_name' => $data['file_name'] ?? '',
                'result' => $data['result'] ?? '',
                'metadata' => $data['metadata'] ?? [],
                'user' => $user->id,
            ]);
        }

        $this->trimAndSummarize($history, $now);
        $this->writeHistoryFile($history);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, string $type, string $id): RedirectResponse
    {
        $history = $this->readHistoryFile();
        $keyMap = [
            'activity' => 'activity_log',
            'generation' => 'generation_history',
            'contract' => 'contract_registry',
        ];

        if (!isset($keyMap[$type])) {
            return back()->with('error', 'Неверный тип записи.');
        }

        $key = $keyMap[$type];
        if (!is_array($history[$key] ?? null)) {
            return back()->with('error', 'История для этого типа не найдена.');
        }

        $originalCount = count($history[$key]);
        $userId = (string) $request->user()->id;

        $history[$key] = array_values(array_filter($history[$key], function ($record, $index) use ($id, $type, $userId) {
            if (!is_array($record)) {
                return true;
            }

            $idKey = match ($type) {
                'activity' => 'timestamp',
                'generation' => 'generation_id',
                'contract' => 'contract_id',
                default => null,
            };

            $belongsToUser = !isset($record['user']) || (string) $record['user'] === $userId;
            $matchesRecordId = $idKey && isset($record[$idKey]) && (string) $record[$idKey] === (string) $id;
            $matchesLegacyIndex = !isset($record[$idKey]) && (string) $index === (string) $id;

            return !($belongsToUser && ($matchesRecordId || $matchesLegacyIndex));
        }, ARRAY_FILTER_USE_BOTH));

        if (count($history[$key]) === $originalCount) {
            return back()->with('error', 'Запись не найдена или недоступна.');
        }

        $this->trimAndSummarize($history, now()->format('Y-m-d H:i:s'));
        $this->writeHistoryFile($history);

        return back()->with('status', 'Запись удалена.');
    }

    private function sessionsForUser(int $userId, string $currentId = '')
    {
        if (!Schema::hasTable('sessions')) {
            return collect();
        }

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get()
            ->map(function ($session) use ($currentId) {
                $session->last_activity_at = Carbon::createFromTimestamp((int) $session->last_activity);
                $session->device = $this->describeDevice((string) ($session->user_agent ?? ''));
                $session->is_current = $currentId !== '' && ($session->id ?? '') === $currentId;

                return $session;
            });
    }

    private function documentHistory(?int $userId = null): array
    {
        $empty = [
            'activity_log' => [],
            'generation_history' => [],
            'contract_registry' => [],
            'summary' => [],
            'total' => 0,
        ];

        $decoded = $this->readHistoryFile();
        if (!is_array($decoded)) {
            return $empty;
        }

        $history = array_merge($empty, [
            'activity_log' => $this->validRecords($decoded['activity_log'] ?? [], $userId),
            'generation_history' => $this->validRecords($decoded['generation_history'] ?? [], $userId),
            'contract_registry' => $this->validRecords($decoded['contract_registry'] ?? [], $userId),
            'summary' => is_array($decoded['summary'] ?? null) ? $decoded['summary'] : [],
        ]);

        $history['total'] = count($history['activity_log'])
            + count($history['generation_history'])
            + count($history['contract_registry']);

        return $history;
    }

    private function readHistoryFile(): array
    {
        $fallback = [
            'activity_log' => [],
            'generation_history' => [],
            'contract_registry' => [],
            'summary' => [],
        ];

        $path = base_path('history/document.json');
        if (!File::exists($path)) {
            return $fallback;
        }

        $decoded = json_decode(File::get($path), true);

        return is_array($decoded) ? array_merge($fallback, $decoded) : $fallback;
    }

    private function writeHistoryFile(array $history): void
    {
        $path = base_path('history/document.json');
        File::ensureDirectoryExists(dirname($path));
        file_put_contents($path, json_encode($history, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    private function trimAndSummarize(array &$history, string $timestamp): void
    {
        $history['activity_log'] = array_slice($history['activity_log'] ?? [], 0, 100);
        $history['generation_history'] = array_slice($history['generation_history'] ?? [], 0, 100);
        $history['contract_registry'] = array_slice($history['contract_registry'] ?? [], 0, 100);
        $history['summary'] = [
            'total_records' => [
                'activity_log' => count($history['activity_log']),
                'generation_history' => count($history['generation_history']),
                'contract_registry' => count($history['contract_registry']),
            ],
            'last_update' => $timestamp,
            'notes' => 'История действий и документов приложения.',
        ];
    }

    private function validRecords(mixed $records, ?int $userId = null): array
    {
        if (!is_array($records)) {
            return [];
        }

        return array_values(array_filter($records, function ($record) use ($userId) {
            if (!is_array($record)) {
                return false;
            }

            foreach (['timestamp', 'created_at', 'signed_at'] as $dateKey) {
                if (isset($record[$dateKey]) && is_string($record[$dateKey]) && str_contains($record[$dateKey], 'YYYY')) {
                    return false;
                }
            }

            if ($userId && isset($record['user']) && (string) $record['user'] !== (string) $userId) {
                return false;
            }

            return true;
        }));
    }

    private function parseDate(?string $date): ?Carbon
    {
        if (!$date || str_contains($date, 'YYYY')) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }
    }

    private function taskStatusLabel(string $status): string
    {
        return match ($status) {
            Task::STATUS_COMPLETED => 'Задача выполнена',
            Task::STATUS_FAILED => 'Задача завершилась с ошибкой',
            default => 'Задача ожидает выполнения',
        };
    }
}
