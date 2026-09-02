<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Роль пользователя.
 *
 * До этой миграции все вошедшие пользователи были равны между собой, поэтому
 * страницы вроде списка пользователей приходилось либо открывать всем, либо
 * закрывать полностью. Роль даёт основу для политик доступа.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('user')->after('email');
                $table->index('role');
            }
        });

        // Первый зарегистрированный пользователь становится администратором:
        // иначе после миграции админские страницы недоступны никому и систему
        // невозможно администрировать.
        if (Schema::hasColumn('users', 'role')) {
            $first = DB::table('users')->orderBy('id')->first();
            if ($first !== null) {
                DB::table('users')->where('id', $first->id)->update(['role' => 'admin']);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }
        });
    }
};
