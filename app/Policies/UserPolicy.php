<?php

namespace App\Policies;

use App\Models\User;

/**
 * Права на карточки пользователей.
 *
 * Список пользователей и чужие карточки — только для администратора:
 * там видны адреса почты, способ входа и дата регистрации. Свою карточку
 * пользователь видит всегда.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->id === $target->id;
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        // Администратор не может удалить сам себя: иначе система
        // остаётся без администратора.
        return $user->isAdmin() && $user->id !== $target->id;
    }
}
