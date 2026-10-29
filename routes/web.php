<?php

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureFaceVerified;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', EnsureFaceVerified::class])->name('dashboard');

// Быстрые задачи на рабочем столе (только свои)
Route::middleware(['auth', 'verified', EnsureFaceVerified::class])->prefix('dashboard/tasks')->name('dashboard.tasks.')->group(function () {
    Route::post('/', [DashboardController::class, 'storeTask'])->name('store');
    Route::patch('{task}/complete', [DashboardController::class, 'completeTask'])->name('complete');
    Route::delete('{task}', [DashboardController::class, 'destroyTask'])->name('destroy');
});

// Старая ссылка переключателя на странице входа (/lang/tg). Она писала язык в ключ
// app_locale, который SetLocale не читает, поэтому выбор языка там не действовал.
// Теперь это тот же переключатель, что и /language/{locale}; «tj» — старое написание «tg».
Route::get('/lang/{locale}', function ($locale) {
    $locale = $locale === 'tj' ? 'tg' : $locale;
    if (array_key_exists($locale, config('app.supported_locales', []))) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Словарь перевода интерфейса на таджикский для слоя public/js/i18n-runtime.js.
Route::get('/i18n/tg.js', function () {
    $path = lang_path('tg/phrases.json');
    $json = is_file($path) ? file_get_contents($path) : '{}';
    $etag = '"' . md5($json) . '"';

    return response('window.__I18N_TG=' . $json . ';', 200, [
        'Content-Type' => 'application/javascript; charset=UTF-8',
        'Cache-Control' => 'public, max-age=3600',
        'ETag' => $etag,
    ]);
})->name('i18n.tg');
Route::get('/language/{locale}', function ($locale) {
    $supported = ['en', 'ru', 'tg'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('locale.switch');
Route::get('/contract/form', [ContractController::class, 'showForm'])->name('contract.form');
Route::get('/contract/generate', [ContractController::class, 'generate'])
    ->name('contract.generate');    
Route::get('/contract/compare', [ContractController::class, 'compare'])
    ->name('contract.compare');
Route::get('/contract/create', [ContractController::class, 'create'])
    ->name('contract.create');
    Route::get('/contract/ugalov-kodex', [ContractController::class, 'kodex'])
    ->name('contract.ugalov-kodex');
Route::get('/contract/analyze', [ContractController::class, 'showForm'])->name('contract.analyze');
Route::get('/contract/codex', [ContractController::class, 'codex'])
    ->name('contract.codex');
Route::get('/contract/court', [ContractController::class, 'court'])
    ->name('contract.court');
// Маршруты contract.upload и contract.stream убраны: они указывали на методы
// upload() и streamProxy(), которых в ContractController нет, и обращение к
// ним возвращало HTTP 500. Загрузка договора и поток анализа идут напрямую в
// python/app.py (POST /api/analyze и GET /api/stream/{id}) — см. вызовы в
// resources/views/contract/analyze.blade.php.
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
