        <x-app-layout>
            <x-slot name="header">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white shadow-md shadow-indigo-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Кабинет</h2>
                            <p class="mt-0.5 text-sm text-slate-500">
                                Активность, сессии и история <span class="font-medium text-slate-700">{{ $user->name }}</span>
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800 hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:rotate-12">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Редактировать профиль
                    </a>
                </div>
            </x-slot>

            @php
                $formatHistoryResult = static function ($value): string {
                    if (is_array($value) || is_object($value)) {
                        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '';
                    }
                    return (string) ($value ?? '');
                };

                $taskStatusStyles = [
                    'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'done' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'выполнено' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                    'ожидание' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                    'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-700/20',
                    'в работе' => 'bg-blue-50 text-blue-700 ring-blue-700/20',
                    'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                    'отменено' => 'bg-red-50 text-red-700 ring-red-600/20',
                    'default' => 'bg-slate-50 text-slate-600 ring-slate-500/10',
                ];
            @endphp

            <div class="bg-slate-50/50 py-10">
                <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                    <!-- Stats -->
                    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach([
                            ['label' => 'Всего задач', 'value' => $stats['tasks_total'], 'color' => 'slate', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.8 0c-.966.043-1.93.109-2.88.195-1.125.094-1.976 1.057-1.976 2.192v4.286c0 1.135.845 2.098 1.976 2.192.95.086 1.914.152 2.88.195m5.8 0c.966-.043 1.93-.109 2.88-.195 1.125-.094 1.976-1.057 1.976-2.192V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15 12h3.75M15 15h3.75M15 18h3.75'],
                            ['label' => 'Выполнено', 'value' => $stats['tasks_completed'], 'color' => 'emerald', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['label' => 'Сессии', 'value' => $stats['sessions_total'], 'color' => 'indigo', 'icon' => 'M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 21.75H6.375a1.125 1.125 0 01-1.125-1.125V14.25a1.125 1.125 0 011.125-1.125h.75a1.125 1.125 0 001.125-1.125v-.75c0-.563.026-1.159.43-1.563A6 6 0 0121 8.25m-18 0a6 6 0 0112 0m0 0a6 6 0 0012 0'],
                            ['label' => 'Записи истории', 'value' => $stats['history_total'], 'color' => 'amber', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ] as $stat)
                            <div class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all hover:shadow-md hover:-translate-y-0.5">
                                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-{{ $stat['color'] }}-50 opacity-40 transition-transform group-hover:scale-150"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                                    </div>
                                    <p class="mt-4 text-3xl font-bold tracking-tight text-{{ $stat['color'] }}-700">{{ $stat['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </section>

                    <!-- Profile & Activity -->
                    <section class="grid gap-5 lg:grid-cols-12">
                        <!-- Profile Card -->
                        <div class="lg:col-span-4">
                            <div class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5 transition-shadow hover:shadow-md">
                                <!-- Subtle gradient top accent -->
                                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500"></div>

                                <div class="p-5">
                                    <!-- User Header -->
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-lg font-bold text-white shadow-lg shadow-indigo-200/50 ring-2 ring-white">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            @if($user->isOnline())
                                                <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500 shadow-sm"></span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h2 class="truncate text-base font-semibold text-slate-900">{{ $user->name }}</h2>
                                            <p class="truncate text-sm text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>

                                    <!-- Stats Grid -->
                                    <div class="mt-5 grid grid-cols-2 gap-3">
                                        <div class="rounded-xl bg-slate-50/80 p-3 ring-1 ring-slate-900/5">
                                            <p class="text-xs font-medium text-slate-500">Статус</p>
                                            <div class="mt-1 flex items-center gap-1.5">
                                                @if($user->isOnline())
                                                    <span class="relative flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                </span>
                                                    <span class="text-sm font-semibold text-emerald-700">Онлайн</span>
                                                @else
                                                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                                    <span class="text-sm font-medium text-slate-600">Офлайн</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="rounded-xl bg-slate-50/80 p-3 ring-1 ring-slate-900/5">
                                            <p class="text-xs font-medium text-slate-500">Регистрация</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                                {{ $user->created_at?->format('d.m.Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <br>
                                    <!-- Last Seen -->
                                    <div class="mt-3 flex items-center justify-between rounded-xl bg-slate-50/80 px-3 py-2.5 ring-1 ring-slate-900/5">
                                        <span class="text-xs font-medium text-slate-500">Последний визит</span>
                                        <span class="text-sm font-medium text-slate-900">
                        {{ $user->last_seen_at ? $user->last_seen_at->format('d.m.Y H:i') : '—' }}
                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
<br>
                        <!-- Activity Feed -->
                        <div class="lg:col-span-8">
                            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-slate-900">Лента активности</h3>
                                            <p class="text-xs text-slate-500">{{ $activity->count() }} {{ trans_choice('запись|записи|записей', $activity->count()) }}</p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        id="activityToggle"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 active:scale-95"
                                        aria-expanded="true"
                                    >
                                        <span id="activityToggleText">Скрыть</span>
                                        <svg id="activityToggleIcon" class="h-3.5 w-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Feed Body -->
                                <div id="activityFeedBody" class="p-5">
                                    @if($activity->count())
                                        <div class="grid gap-3 md:grid-cols-2" id="activityFeedGrid">
                                            @foreach($activity as $item)
                                                <div class="activity-feed-item group relative overflow-hidden rounded-xl border border-slate-100 bg-slate-50/40 p-4 transition-all duration-200 hover:border-slate-200 hover:bg-slate-50 hover:shadow-sm">
                                                    <!-- Left accent line -->
                                                    <div class="absolute inset-y-0 left-0 w-0.5 bg-gradient-to-b from-indigo-400 to-violet-400 opacity-0 transition-opacity group-hover:opacity-100"></div>

                                                    <div class="flex items-start justify-between gap-3">
                                                        <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                                                        <time class="shrink-0 text-xs font-medium text-slate-400 tabular-nums">
                                                            {{ $item['date']->format('d.m.Y') }}
                                                        </time>
                                                    </div>
                                                    <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $item['description'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center justify-center py-14 text-center">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="mt-3 text-sm font-medium text-slate-900">История активности пуста</p>
                                            <p class="mt-0.5 text-xs text-slate-500">Здесь будут отображаться последние действия</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Tasks & Sessions -->
                    <section class="grid gap-6 xl:grid-cols-2">
                        <!-- Tasks -->
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                            <div class="border-b border-slate-100 p-6">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    История задач
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                                        <thead class="bg-slate-50">
                                        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <th class="px-4 py-3">Задача</th>
                                            <th class="px-4 py-3">Статус</th>
                                            <th class="px-4 py-3">Обновлено</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 bg-white">
                                        @forelse($tasks as $task)
                                            <tr class="transition-colors hover:bg-slate-50">
                                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900">{{ $task->title }}</td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    @php
                                                        $statusKey = strtolower($task->status);
                                                        $statusClass = $taskStatusStyles[$statusKey] ?? $taskStatusStyles['default'];
                                                    @endphp
                                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                                            {{ $task->status }}
                                                        </span>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $task->updated_at?->format('d.m.Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-8 text-center text-slate-500">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.25 9.75h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008z" /></svg>
                                                        Задач пока нет.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sessions -->
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                            <div class="border-b border-slate-100 p-6">
                                <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                    История сессий
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @forelse($sessions as $session)
                                        <div class="group flex items-start gap-4 rounded-xl border border-slate-100 bg-slate-50/30 p-4 transition-all hover:border-slate-200 hover:bg-slate-50 hover:shadow-sm">
                                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-900/5">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                    <p class="font-mono text-sm font-medium text-slate-900">{{ $session->ip_address ?? 'IP не указан' }}</p>
                                                    <time class="whitespace-nowrap text-xs font-medium text-slate-400">{{ $session->last_activity_at->format('d.m.Y H:i') }}</time>
                                                </div>
                                                <p class="mt-1 break-all text-xs text-slate-500">{{ $session->user_agent ?? 'User agent не указан' }}</p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center rounded-xl bg-slate-50 py-12 text-center">
                                            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 21.75H6.375a1.125 1.125 0 01-1.125-1.125V14.25a1.125 1.125 0 011.125-1.125h.75a1.125 1.125 0 001.125-1.125v-.75c0-.563.026-1.159.43-1.563A6 6 0 0121 8.25m-18 0a6 6 0 0112 0m0 0a6 6 0 0012 0" /></svg>
                                            <p class="mt-2 text-sm text-slate-500">История сессий недоступна или пуста.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Document History with Delete & Styled AI Reports -->
                    <section class="grid gap-6 xl:grid-cols-3">

                        <!-- Activity Log -->
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                            <div class="border-b border-slate-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        Действия документов
                                    </h3>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ count($documentHistory['activity_log']) }}</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @forelse($documentHistory['activity_log'] as $index => $record)
                                        @php
                                            $recordId = $record['timestamp'] ?? $record['id'] ?? $index;
                                            $result = $formatHistoryResult($record['result'] ?? $record['response'] ?? $record['ai_response'] ?? '');
                                            $hasAiReport = !empty($result) && str_contains(strtolower($result), 'отчет');
                                        @endphp

                                        <div class="group relative rounded-xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">
                                            {{-- Delete button — всегда в правом верхнем углу карточки --}}
                                            3<form action="{{ route('cabinet.documents.destroy', ['type' => 'activity', 'id' => $recordId]) }}"
                                                  method="POST"
                                                  class="absolute right-2 top-2 z-10"
                                                  onsubmit="return confirm('Удалить эту запись?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                                        title="Удалить">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>

                                            @if($hasAiReport)
                                                {{-- Styled AI Report Card --}}
                                                <div class="overflow-hidden rounded-xl">
                                                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/80 px-4 py-3 pr-10">
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                                                            </div>
                                                            <span class="text-sm font-semibold text-slate-900">{{ $record['action_type'] ?? 'AI-анализ' }}</span>
                                                        </div>
                                                        <span class="text-xs text-slate-500">{{ $record['timestamp'] ?? '' }}</span>
                                                    </div>

                                                    <div class="p-4">
                                                        @if(str_contains($result, 'Совместимость'))
                                                            @php
                                                                preg_match('/Совместимость[:\s]+(\d+)%/i', $result, $match);
                                                                $percent = $match[1] ?? 0;
                                                                $percentColor = $percent >= 85 ? 'bg-emerald-500' : ($percent >= 70 ? 'bg-amber-500' : 'bg-red-500');
                                                                $percentText = $percent >= 85 ? 'text-emerald-700' : ($percent >= 70 ? 'text-amber-700' : 'text-red-700');
                                                                $percentBg = $percent >= 85 ? 'bg-emerald-50' : ($percent >= 70 ? 'bg-amber-50' : 'bg-red-50');
                                                            @endphp
                                                            <div class="mb-4 rounded-lg {{ $percentBg }} p-3">
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span class="text-xs font-medium text-slate-600">Совместимость документов</span>
                                                                    <span class="text-sm font-bold {{ $percentText }}">{{ $percent }}%</span>
                                                                </div>
                                                                <div class="h-2 w-full overflow-hidden rounded-full bg-white">
                                                                    <div class="h-full rounded-full {{ $percentColor }} transition-all" style="width: {{ $percent }}%"></div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="ai-report-content space-y-3 text-sm text-slate-700">
                                                            {!! nl2br(e($result)) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Обычная запись — тоже с отступом справа, чтобы кнопка удаления не перекрывала текст --}}
                                                <div class="p-4 pr-10">
                                                    <x-cabinet-history-record
                                                        :title="$record['action_type'] ?? 'Действие'"
                                                        :description="$record['details'] ?? 'Без описания'"
                                                        :date="$record['timestamp'] ?? 'Дата не указана'"
                                                        :record="$record"
                                                        :format-result="$formatHistoryResult"
                                                        result-label="AI-ответ / отчёт"
                                                    />
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center rounded-xl bg-slate-50 py-8 text-center">
                                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                            <p class="mt-2 text-sm text-slate-500">Нет сохранённых действий.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Generations -->
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                            <div class="border-b border-slate-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                                        Генерации
                                    </h3>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ count($documentHistory['generation_history']) }}</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @forelse($documentHistory['generation_history'] as $index => $record)
                                        @php $genId = $record['generation_id'] ?? $record['id'] ?? $index; @endphp
                                        <div class="group relative rounded-xl border border-slate-100 bg-slate-50/30 p-4 transition-all hover:border-slate-200 hover:bg-slate-50 hover:shadow-sm">
                                            {{-- Delete button --}}
                                            <form action="{{ route('cabinet.documents.destroy', ['type' => 'generation', 'id' => $genId]) }}"
                                                  method="POST"
                                                  class="absolute right-2 top-2"
                                                  onsubmit="return confirm('Удалить эту генерацию?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                                        title="Удалить">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <x-cabinet-history-record
                                                :title="$record['generation_id'] ?? 'Генерация'"
                                                :description="$record['input_prompt'] ?? 'Промпт не указан'"
                                                :date="($record['status'] ?? 'status unknown') . ' · ' . ($record['created_at'] ?? 'Дата не указана')"
                                                :record="$record"
                                                :format-result="$formatHistoryResult"
                                                result-label="AI-ответ / договор"
                                            />
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center rounded-xl bg-slate-50 py-8 text-center">
                                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                                            <p class="mt-2 text-sm text-slate-500">Нет сохранённых генераций.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Contract Registry -->
                        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-900/5">
                            <div class="border-b border-slate-100 p-6">
                                <div class="flex items-center justify-between">
                                    <h3 class="flex items-center gap-2 text-base font-semibold text-slate-900">
                                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                        Реестр договоров
                                    </h3>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ count($documentHistory['contract_registry']) }}</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    @forelse($documentHistory['contract_registry'] as $index => $record)
                                        @php $contractId = $record['contract_id'] ?? $record['id'] ?? $index; @endphp
                                        <div class="group relative rounded-xl border border-slate-100 bg-slate-50/30 p-4 transition-all hover:border-slate-200 hover:bg-slate-50 hover:shadow-sm">
                                            {{-- Delete button --}}
                                            <form action="{{ route('cabinet.documents.destroy', ['type' => 'contract', 'id' => $contractId]) }}"
                                                  method="POST"
                                                  class="absolute right-2 top-2"
                                                  onsubmit="return confirm('Удалить этот договор?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                                        title="Удалить">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <x-cabinet-history-record
                                                :title="$record['contract_type'] ?? 'Договор'"
                                                :description="$record['status'] ?? 'Статус не указан'"
                                                :date="$record['created_at'] ?? 'Дата не указана'"
                                                :record="$record"
                                                :format-result="$formatHistoryResult"
                                                result-label="AI-ответ / договор"
                                            />
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center rounded-xl bg-slate-50 py-8 text-center">
                                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                            <p class="mt-2 text-sm text-slate-500">Нет сохранённых договоров.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            @once
                <style>
                    .ai-report-content { font-family: ui-sans-serif, system-ui, -apple-system, sans-serif; line-height: 1.6; }
                    .ai-report-content br { display: block; margin-bottom: 0.5rem; content: ""; }
                    .ai-report-content strong, .ai-report-content b { color: rgb(15 23 42); font-weight: 600; }
                    .activity-feed-item.is-hidden { display: none; }
                </style>
                <script>
                        (function() {
                        const toggle = document.getElementById('activityToggle');
                        const body = document.getElementById('activityFeedBody');
                        const text = document.getElementById('activityToggleText');
                        const icon = document.getElementById('activityToggleIcon');

                        if (!toggle || !body) return;

                        toggle.addEventListener('click', () => {
                        const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                        if (isExpanded) {
                        body.style.maxHeight = body.scrollHeight + 'px';
                        requestAnimationFrame(() => {
                        body.style.maxHeight = '0px';
                        body.style.paddingTop = '0px';
                        body.style.paddingBottom = '0px';
                        body.style.opacity = '0';
                        body.style.overflow = 'hidden';
                    });
                        text.textContent = 'Показать';
                        icon.style.transform = 'rotate(180deg)';
                        toggle.setAttribute('aria-expanded', 'false');
                    } else {
                        body.style.maxHeight = body.scrollHeight + 'px';
                        body.style.paddingTop = '';
                        body.style.paddingBottom = '';
                        body.style.opacity = '1';
                        body.style.overflow = '';
                        text.textContent = 'Скрыть';
                        icon.style.transform = '';
                        toggle.setAttribute('aria-expanded', 'true');

                        setTimeout(() => {
                        body.style.maxHeight = '';
                    }, 200);
                    }
                    });
                    })();
                    document.addEventListener('DOMContentLoaded', () => {
                        const card = document.querySelector('[data-activity-card]');
                        const toggle = document.getElementById('activityToggle');
                        if (!card || !toggle) return;

                        const items = Array.from(card.querySelectorAll('.activity-feed-item'));
                        const text = document.getElementById('activityToggleText');
                        const icon = document.getElementById('activityToggleIcon');
                        const oldBadge = card.querySelector('.border-b .rounded-full');

                        if (oldBadge) oldBadge.classList.add('hidden');
                        if (!items.length) return;

                        const applyState = (showAll) => {
                            items.forEach((item, index) => {
                                item.classList.toggle('is-hidden', !showAll && index >= 2);
                            });
                            toggle.setAttribute('aria-expanded', showAll ? 'true' : 'false');
                            if (text) text.textContent = showAll ? 'Скрыть' : 'Показать';
                            if (icon) icon.classList.toggle('-rotate-90', !showAll);
                            localStorage.setItem('cabinet_activity_show_all', showAll ? '1' : '0');
                        };

                        applyState(localStorage.getItem('cabinet_activity_show_all') === '1');
                        toggle.addEventListener('click', () => {
                            applyState(toggle.getAttribute('aria-expanded') !== 'true');
                        });
                    });
                </script>
            @endonce
        </x-app-layout>
