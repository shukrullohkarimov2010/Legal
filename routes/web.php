<?php

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/lang/{locale}', function ($locale) {
    $allowed = ['ru', 'en', 'tj'];
    if (in_array($locale, $allowed)) {
        session(['app_locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
Route::get('/language/{locale}', function ($locale) {
    $supported = ['en', 'ru', 'tg'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('locale.switch');
Route::get('/contract/generate', [ContractController::class, 'generate'])
    ->name('contract.generate');
Route::get('/contract/compare', [ContractController::class, 'compare'])
    ->name('contract.compare');
Route::get('/contract/create', [ContractController::class, 'create'])
    ->name('contract.create');
Route::get('/contract/analyze', [ContractController::class, 'index'])
    ->name('contract.analyze');
Route::get('/contract/codex', [ContractController::class, 'codex'])
    ->name('contract.codex');
Route::get('/contract/analyze', [ContractController::class, 'showForm'])->name('contract.form');
Route::post('/contract/analyze', [ContractController::class, 'upload'])->name('contract.upload');
Route::get('/contract/stream/{requestId}', [ContractController::class, 'streamProxy'])->name('contract.stream');
Route::get('/tasks/chat', function () {return view('admin.pages.tasks.chat');
})->name('tasks.chat');Route::get('/tasks/calc', [TaskController::class, 'calc'])->name('tasks.calc');
Route::post('/tasks/report', [TaskController::class, 'report'])->name('tasks.report');
Route::resource('tasks', TaskController::class);
Route::patch('/tasks/{task}/complete', [TaskController::class, 'markCompleted'])->name('tasks.complete');

// Маршруты для управления пользователями
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');


Route::middleware('auth')->group(function () {
    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet.index');
    Route::post('/cabinet/activity', [CabinetController::class, 'storeActivity'])->name('cabinet.activity.store');
    Route::delete('/cabinet/documents/{type}/{id}', [CabinetController::class, 'destroy'])
        ->name('cabinet.documents.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware(['web'])->group(function () {
    Route::get('/auth/{provider}', [SocialController::class, 'redirectToProvider']);
    Route::get('/auth/{provider}/callback', [SocialController::class, 'handleProviderCallback']);
});

require __DIR__.'/auth.php';
