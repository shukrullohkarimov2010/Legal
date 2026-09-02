<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

/**
 * Права на задачи.
 *
 * Владелец распоряжается своей задачей, администратор — любой. Задачи без
 * владельца (созданные до появления проверки прав) доступны только
 * администратору: молча отдать их первому вошедшему было бы хуже, чем
 * потребовать разбора вручную.
 */
class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->owns($user, $task);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return $this->owns($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->owns($user, $task);
    }

    private function owns(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $task->user_id !== null && $task->user_id === $user->id;
    }
}
