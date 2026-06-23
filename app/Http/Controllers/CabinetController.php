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
        $tasks = Task::query()->latest()->limit(12)->get();
        $sessions = $this->sessionsForUser((int) $user->id);
        $documentHistory = $this->documentHistory((int) $user->id);

        $stats = [
            'tasks_total' => Task::query()->count(),
            'tasks_completed' => Task::query()->where('status', Task::STATUS_COMPLETED)->count(),
            'sessions_total' => $sessions->count(),
            'history_total' => $documentHistory['total'],
        ];

        $activity = collect()->push([
            'title' => 'Регистрация аккаунта',
            'description' => 'Пользователь создан в системе.',
            'date' => $user->created_at,
        ]);

        if ($user->last_seen_at) {
            $activity->push([
                'title' => 'Последняя активность',
                'description' => 'Пользователь был активен в приложении.',
                'date' => $user->last_seen_at,
            ]);
        }

        foreach ($tasks as $task) {
            $activity->push([
                'title' => $task->title,
                'description' => $this->taskStatusLabel($task->status),
                'date' => $task->updated_at ?? $task->created_at,
            ]);
        }

        foreach ($documentHistory['activity_log'] as $item) {
            $activity->push([
                'title' => $item['action_type'] ?? 'Запись истории',
                'description' => $item['details'] ?? 'Действие из истории документов.',
                'date' => $this->parseDate($item['timestamp'] ?? null),
            ]);
        }

        $activity = $activity
            ->filter(fn (array $item) => $item['date'] instanceof Carbon)
            ->sortByDesc('date')
            ->values()
            ->take(20);

        return view('cabinet.index', compact(
            'user',
            'tasks',
            'sessions',
            'documentHistory',
            'stats',
            'activity'
        ));
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

    private function sessionsForUser(int $userId)
    {
        if (!Schema::hasTable('sessions')) {
            return collect();
        }

        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get()
            ->map(function ($session) {
                $session->last_activity_at = Carbon::createFromTimestamp((int) $session->last_activity);

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
