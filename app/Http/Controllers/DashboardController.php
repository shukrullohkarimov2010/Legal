<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Сколько открытых задач показываем в панели «Мои задачи» (остальные — в счётчике). */
    private const TASK_LIMIT = 8;

    public function index(Request $request, CabinetController $cabinet): View
    {
        $summary = $cabinet->dashboardSummary((int) $request->user()->id, self::TASK_LIMIT);
        $summary['taskItems'] = $summary['tasks']->map(fn (Task $task) => $this->present($task))->values();

        return view('dashboard', $summary);
    }

    /** Быстрое добавление: достаточно названия, срок и важность — по желанию. */
    public function storeTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|min:2|max:200',
            'priority' => 'nullable|integer|min:1|max:3',
            'deadline' => 'nullable|date',
        ]);

        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => trim($data['title']),
            'priority' => $data['priority'] ?? 2,
            'deadline' => $data['deadline'] ?? null,
            'status' => Task::STATUS_PENDING,
        ]);

        return response()->json(['task' => $this->present($task), 'open' => $this->openCount($request)], 201);
    }

    public function completeTask(Request $request, Task $task): JsonResponse
    {
        $this->authorizeOwner($request, $task);
        $task->update(['status' => Task::STATUS_COMPLETED]);

        return response()->json(['ok' => true, 'open' => $this->openCount($request)]);
    }

    public function destroyTask(Request $request, Task $task): JsonResponse
    {
        $this->authorizeOwner($request, $task);
        $task->delete();

        return response()->json(['ok' => true, 'open' => $this->openCount($request)]);
    }

    private function authorizeOwner(Request $request, Task $task): void
    {
        abort_unless($task->user_id === $request->user()->id, 403);
    }

    private function openCount(Request $request): int
    {
        return Task::query()->where('user_id', $request->user()->id)
            ->where('status', '!=', Task::STATUS_COMPLETED)->count();
    }

    private function present(Task $task): array
    {
        return [
            'id' => $task->id,
            'title' => $task->title,
            'priority' => (int) $task->priority,
            'deadline' => $task->deadline?->format('Y-m-d'),
        ];
    }
}
