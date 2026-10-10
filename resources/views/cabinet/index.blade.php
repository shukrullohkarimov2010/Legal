<x-app-layout>
    @php
        // Даты и «2 недели назад» — на языке сайта (в Carbon таджикский называется tg).
        $dateLocale = app()->getLocale() === 'tg' ? 'tg' : 'ru';
        $firstName = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->first() ?: $user->name;
        $initial = mb_strtoupper(mb_substr(trim($user->name), 0, 1)) ?: '?';

        // Иконки (heroicons, outline) одним местом: путь -> svg.
        $icon = static fn (string $d, string $class = 'cab-i') =>
            '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . $d . '"/></svg>';
        $paths = [
            'analysis'   => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
            'comparison' => 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
            'generation' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z',
            'contract'   => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
            'law'        => 'M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z',
            'chat'       => 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z',
            'calc'       => 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z',
            'search'     => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z',
            'trash'      => 'M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0',
            'chevron'    => 'M19.5 8.25l-7.5 7.5-7.5-7.5',
            'desktop'    => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
            'phone'      => 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
            'edit'       => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
            'tasks'      => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'plus'       => 'M12 4.5v15m7.5-7.5h-15',
        ];

        // Русские формы числа: 1 задача, 2 задачи, 5 задач.
        $plural = static function (int $n, array $forms): string {
            $m10 = $n % 10; $m100 = $n % 100;
            return $m10 === 1 && $m100 !== 11 ? $forms[0]
                : ($m10 >= 2 && $m10 <= 4 && ($m100 < 12 || $m100 > 14) ? $forms[1] : $forms[2]);
        };

        $kindNames = ['analysis' => 'Анализ', 'comparison' => 'Сравнение', 'generation' => 'Генерация', 'contract' => 'Договор'];
        $tabNames = ['analysis' => 'Анализы', 'comparison' => 'Сравнения', 'generation' => 'Генерации', 'contract' => 'Договоры'];
        $kindCounts = $documents->countBy('kind');
        $allCount = $documents->where('in_all', true)->count();

        $statusNames = ['completed' => 'Готово', 'created' => 'Создан', 'signed' => 'Подписан', 'draft' => 'Черновик',
                        'pending' => 'В ожидании', 'failed' => 'Ошибка', 'in_progress' => 'В работе'];
        $taskStatus = [
            'completed' => ['Выполнено', 'ok'],
            'failed'    => ['Ошибка', 'bad'],
            'pending'   => ['В ожидании', 'warn'],
        ];

        $actions = [
            ['contract.analyze', 'Анализ договора', 'Загрузите файл — получите отчёт о рисках', 'analysis'],
            ['contract.create', 'Создать договор', 'Соберите договор из готовых шаблонов', 'contract'],
            ['contract.compare', 'Сравнить договоры', 'Найдите различия между двумя версиями', 'comparison'],
            ['contract.ugalov-kodex', 'Квалификация по УК РТ', 'Опишите деяние — найдём статьи', 'law'],
            ['tasks.chat', 'Юридический чат', 'Вопрос по законам Таджикистана', 'chat'],
            ['tasks.calc', 'Калькулятор неустойки', 'Рассчитайте сумму взыскания', 'calc'],
        ];
    @endphp

    <x-slot name="header">
        <div class="cab-head">
            <div>
                <h1 class="cab-head__title">Здравствуйте, {{ $firstName }}</h1>
                <p class="cab-head__sub">Ваши документы, задачи и устройства в одном месте</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="cab-btn cab-btn--dark">
                {!! $icon($paths['edit']) !!} Профиль и пароль
            </a>
        </div>
    </x-slot>

    <div class="cab">
        <div class="cab__wrap">

            @if (session('status'))
                <div class="cab-flash cab-flash--ok" role="status">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="cab-flash cab-flash--bad" role="alert">{{ session('error') }}</div>
            @endif

            {{-- Быстрые действия: то, зачем пользователь пришёл в сервис --}}
            <section aria-labelledby="cab-actions-title">
                <h2 id="cab-actions-title" class="cab-h">С чего начать</h2>
                <div class="cab-actions">
                    @foreach ($actions as [$routeName, $title, $text, $iconKey])
                        <a href="{{ route($routeName) }}" class="cab-action cab-action--{{ $iconKey }}">
                            <span class="cab-action__icon">{!! $icon($paths[$iconKey]) !!}</span>
                            <span class="cab-action__body">
                                <span class="cab-action__title">{{ $title }}</span>
                                <span class="cab-action__text">{{ $text }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- Цифры пользователя --}}
            <section class="cab-stats" aria-label="Ваши показатели">
                <div class="cab-stat"><span class="cab-stat__v">{{ $stats['documents'] }}</span><span class="cab-stat__l">документов всего</span></div>
                <div class="cab-stat"><span class="cab-stat__v">{{ $stats['analyses'] }}</span><span class="cab-stat__l">анализов договоров</span></div>
                <div class="cab-stat"><span class="cab-stat__v">{{ $stats['contracts_made'] }}</span><span class="cab-stat__l">создано договоров</span></div>
                <div class="cab-stat"><span class="cab-stat__v">{{ $stats['tasks_open'] }}</span><span class="cab-stat__l">{{ $plural($stats['tasks_open'], ['открытая задача', 'открытые задачи', 'открытых задач']) }}</span></div>
            </section>

            <div class="cab-grid">
                {{-- ───────── Документы ───────── --}}
                <section class="cab-card cab-docs" aria-labelledby="cab-docs-title">
                    <div class="cab-card__head">
                        <div>
                            <h2 id="cab-docs-title" class="cab-card__title">Мои документы</h2>
                            <p class="cab-card__sub">Анализы, сравнения и созданные договоры. Откройте запись, чтобы увидеть отчёт.</p>
                        </div>
                    </div>

                    @if ($documents->isEmpty())
                        <div class="cab-empty">
                            <span class="cab-empty__icon">{!! $icon($paths['contract']) !!}</span>
                            <p class="cab-empty__t">Документов пока нет</p>
                            <p class="cab-empty__s">Загрузите договор на проверку или соберите новый — результат появится здесь.</p>
                            <a href="{{ route('contract.analyze') }}" class="cab-btn cab-btn--primary">{!! $icon($paths['analysis']) !!} Проверить договор</a>
                        </div>
                    @else
                        <div class="cab-tools">
                            <div class="cab-tabs" role="group" aria-label="Фильтр по виду">
                                <button type="button" class="cab-tab" data-filter="all" aria-pressed="true">Все <b>{{ $allCount }}</b></button>
                                @foreach ($tabNames as $kind => $name)
                                    @if (($kindCounts[$kind] ?? 0) > 0)
                                        <button type="button" class="cab-tab" data-filter="{{ $kind }}" aria-pressed="false">{{ $name }} <b>{{ $kindCounts[$kind] }}</b></button>
                                    @endif
                                @endforeach
                            </div>
                            <label class="cab-search">
                                <span class="sr-only">Поиск по документам</span>
                                {!! $icon($paths['search'], 'cab-i cab-search__i') !!}
                                <input type="search" id="docSearch" placeholder="Поиск по названию или тексту" autocomplete="off">
                            </label>
                        </div>

                        <ul class="cab-list" id="docList">
                            @foreach ($documents as $i => $doc)
                                @php
                                    $searchText = \Illuminate\Support\Str::lower($doc['title'] . ' ' . $doc['label'] . ' ' . $doc['summary'] . ' ' . $doc['details']);
                                    $statusLabel = $doc['status'] !== '' ? ($statusNames[\Illuminate\Support\Str::lower($doc['status'])] ?? $doc['status']) : '';
                                    $hasMore = $doc['result'] !== '' || $doc['summary'] !== '' || $doc['details'] !== '';
                                @endphp
                                <li class="cab-doc" data-kind="{{ $doc['kind'] }}" data-all="{{ $doc['in_all'] ? '1' : '0' }}" data-text="{{ $searchText }}">
                                    <div class="cab-doc__row">
                                        <span class="cab-kind cab-kind--{{ $doc['kind'] }}" title="{{ $kindNames[$doc['kind']] }}">{!! $icon($paths[$doc['kind']]) !!}</span>
                                        <div class="cab-doc__main">
                                            <p class="cab-doc__title" title="{{ $doc['title'] }}">{{ $doc['title'] }}</p>
                                            <p class="cab-doc__meta">
                                                <span class="cab-badge cab-badge--{{ $doc['kind'] }}">{{ $kindNames[$doc['kind']] }}</span>
                                                @if ($doc['risk'])
                                                    <span class="cab-badge cab-badge--risk-{{ $doc['risk']['level'] }}">{{ $doc['risk']['label'] }} · {{ $doc['risk']['score'] }}/100</span>
                                                @endif
                                                @if ($doc['compat'] !== null)
                                                    <span class="cab-badge cab-badge--info">Совместимость {{ $doc['compat'] }}%</span>
                                                @endif
                                                @if ($statusLabel !== '')
                                                    <span class="cab-badge cab-badge--neutral">{{ $statusLabel }}</span>
                                                @endif
                                                <time datetime="{{ $doc['date']->toIso8601String() }}" title="{{ $doc['date']->locale($dateLocale)->translatedFormat('j F Y, H:i') }}">{{ $doc['date']->locale($dateLocale)->diffForHumans() }}</time>
                                            </p>
                                        </div>
                                        <div class="cab-doc__actions">
                                            @if ($hasMore)
                                                <button type="button" class="cab-iconbtn cab-open" aria-expanded="false" aria-label="Показать подробности: {{ $doc['title'] }}">
                                                    <span class="cab-open__t">Открыть</span>{!! $icon($paths['chevron'], 'cab-i cab-open__i') !!}
                                                </button>
                                            @endif
                                            <button type="button" class="cab-iconbtn cab-iconbtn--danger cab-del" data-title="{{ $doc['title'] }}" aria-label="Удалить: {{ $doc['title'] }}">
                                                {!! $icon($paths['trash']) !!}
                                            </button>
                                        </div>
                                    </div>

                                    @if ($hasMore)
                                        <div class="cab-doc__more" hidden>
                                            @if ($doc['details'] !== '' && $doc['details'] !== $doc['title'])
                                                <p class="cab-doc__details">{{ $doc['details'] }}</p>
                                            @endif
                                            @if ($doc['result'] !== '')
                                                <div class="cab-md">{!! \Illuminate\Support\Str::markdown($doc['result'], ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                                            @elseif ($doc['summary'] !== '')
                                                <p class="cab-doc__details">{{ $doc['summary'] }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('cabinet.documents.destroy', ['type' => $doc['delete_type'], 'id' => $doc['id']]) }}" class="cab-delform">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        <p class="cab-nothing" id="docNothing" hidden>Ничего не найдено. Измените запрос или выберите другой фильтр.</p>
                        <div class="cab-more"><button type="button" class="cab-btn cab-btn--ghost" id="docMore" hidden>Показать ещё</button></div>
                    @endif
                </section>

                {{-- ───────── Правая колонка ───────── --}}
                <aside class="cab-side">
                    <section class="cab-card cab-profile" aria-label="Профиль">
                        <div class="cab-profile__top">
                            <span class="cab-avatar" aria-hidden="true">{{ $initial }}</span>
                            <div class="cab-profile__id">
                                <p class="cab-profile__name">{{ $user->name }}</p>
                                <p class="cab-profile__mail">{{ $user->email }}</p>
                            </div>
                        </div>
                        <dl class="cab-kv">
                            <div><dt>Статус</dt><dd>
                                @if ($user->isOnline())
                                    <span class="cab-dot cab-dot--on"></span> Сейчас в сервисе
                                @else
                                    <span class="cab-dot"></span> Не в сети
                                @endif
                            </dd></div>
                            <div><dt>Последний визит</dt><dd>
                                @if ($user->last_seen_at)
                                    <span title="{{ $user->last_seen_at->locale($dateLocale)->translatedFormat('j F Y, H:i') }}">{{ $user->last_seen_at->locale($dateLocale)->diffForHumans() }}</span>
                                @else — @endif
                            </dd></div>
                            <div><dt>В сервисе с</dt><dd>{{ $user->created_at?->locale($dateLocale)->translatedFormat('j F Y') }}</dd></div>
                        </dl>
                    </section>

                    <section class="cab-card" aria-labelledby="cab-tasks-title">
                        <div class="cab-card__head cab-card__head--tight">
                            <h2 id="cab-tasks-title" class="cab-card__title cab-card__title--sm">Мои задачи</h2>
                            <a href="{{ route('tasks.index') }}" class="cab-link">Все задачи</a>
                        </div>
                        @forelse ($tasks as $task)
                            @php [$tLabel, $tTone] = $taskStatus[$task->status] ?? [$task->status, 'neutral']; @endphp
                            <a href="{{ route('tasks.show', $task) }}" class="cab-task">
                                <span class="cab-task__t">{{ $task->title }}</span>
                                <span class="cab-badge cab-badge--{{ $tTone }}">{{ $tLabel }}</span>
                            </a>
                        @empty
                            <div class="cab-mini-empty">
                                <p>Задач пока нет.</p>
                                <a href="{{ route('tasks.create') }}" class="cab-link">{!! $icon($paths['plus'], 'cab-i cab-i--sm') !!} Создать задачу</a>
                            </div>
                        @endforelse
                    </section>

                    <section class="cab-card" aria-labelledby="cab-dev-title">
                        <h2 id="cab-dev-title" class="cab-card__title cab-card__title--sm">Устройства</h2>
                        <ul class="cab-devices">
                            <li class="cab-device cab-device--current">
                                <span class="cab-device__i">{!! $icon($paths[$currentDevice['mobile'] ? 'phone' : 'desktop']) !!}</span>
                                <span class="cab-device__b">
                                    <span class="cab-device__t">{{ $currentDevice['browser'] }} · {{ $currentDevice['system'] }}</span>
                                    <span class="cab-device__s">{{ $currentDevice['ip'] }} · это устройство</span>
                                </span>
                            </li>
                            @foreach ($sessions->where('is_current', false)->take(4) as $session)
                                <li class="cab-device">
                                    <span class="cab-device__i">{!! $icon($paths[$session->device['mobile'] ? 'phone' : 'desktop']) !!}</span>
                                    <span class="cab-device__b">
                                        <span class="cab-device__t">{{ $session->device['browser'] }} · {{ $session->device['system'] }}</span>
                                        <span class="cab-device__s">{{ $session->ip_address ?? 'IP не указан' }} · {{ $session->last_activity_at->locale($dateLocale)->diffForHumans() }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="cab-note">Не узнаёте устройство? <a href="{{ route('profile.edit') }}" class="cab-link">Смените пароль</a>.</p>
                    </section>
                </aside>
            </div>
        </div>
    </div>

    {{-- Подтверждение удаления: своё окно вместо системного confirm() --}}
    <dialog id="delDialog" class="cab-dialog" aria-labelledby="delTitle">
        <form method="dialog" class="cab-dialog__box">
            <h3 id="delTitle" class="cab-dialog__t">Удалить запись?</h3>
            <p class="cab-dialog__s">Запись «<span id="delName"></span>» будет удалена из истории без возможности восстановления.</p>
            <div class="cab-dialog__btns">
                <button type="submit" value="cancel" class="cab-btn cab-btn--ghost">Отмена</button>
                <button type="submit" value="ok" class="cab-btn cab-btn--danger">Удалить</button>
            </div>
        </form>
    </dialog>

    <style>
        .cab { --ink:#111827; --ink2:#374151; --mut:#6b7280; --line:#e5e7eb; --soft:#f3f4f6; --card:#fff; --acc:#4f46e5; --acc-soft:#eef2ff;
               --r:16px; color:var(--ink); padding-block:28px 64px; }
        .cab *, .cab *::before, .cab *::after { box-sizing:border-box; }
        .cab__wrap { max-width:1280px; margin-inline:auto; padding-inline:16px; display:flex; flex-direction:column; gap:28px; }
        .cab p, .cab h1, .cab h2, .cab h3, .cab dl, .cab dd, .cab ul { margin:0; padding:0; }
        .cab ul { list-style:none; }
        .cab-i { width:20px; height:20px; flex:none; } .cab-i--sm { width:16px; height:16px; }
        @media (min-width:640px) { .cab__wrap { padding-inline:24px; } }
        @media (min-width:1024px) { .cab__wrap { padding-inline:32px; } }
        .sr-only { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; }

        .cab-head { display:flex; gap:16px; align-items:center; justify-content:space-between; flex-wrap:wrap; }
        .cab-head__title { font-size:clamp(22px,3.4vw,30px); font-weight:700; letter-spacing:-.01em; color:#111827; margin:0; }
        .cab-head__sub { margin:2px 0 0; color:#6b7280; font-size:15px; }

        .cab-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:44px; padding:0 18px; border-radius:12px; font:600 14px/1 inherit;
                   border:1px solid transparent; cursor:pointer; text-decoration:none; transition:background .15s, box-shadow .15s, transform .15s; }
        .cab-btn:focus-visible, .cab-tab:focus-visible, .cab-iconbtn:focus-visible, .cab-action:focus-visible, .cab-link:focus-visible, .cab-task:focus-visible,
        .cab-search input:focus-visible { outline:3px solid #a5b4fc; outline-offset:2px; }
        .cab-btn--dark { background:#111827; color:#fff; } .cab-btn--dark:hover { background:#1f2937; }
        .cab-btn--primary { background:var(--acc); color:#fff; } .cab-btn--primary:hover { background:#4338ca; }
        .cab-btn--ghost { background:#fff; color:var(--ink2); border-color:var(--line); } .cab-btn--ghost:hover { background:var(--soft); }
        .cab-btn--danger { background:#dc2626; color:#fff; } .cab-btn--danger:hover { background:#b91c1c; }

        .cab-flash { padding:12px 16px; border-radius:12px; font-size:14px; font-weight:500; }
        .cab-flash--ok { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
        .cab-flash--bad { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

        .cab-h { font-size:13px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; color:var(--mut); margin-bottom:12px; }

        /* быстрые действия */
        .cab-actions { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; }
        .cab-action { display:flex; align-items:center; gap:14px; padding:16px; background:var(--card); border:1px solid var(--line); border-radius:var(--r);
                      text-decoration:none; color:inherit; transition:border-color .15s, box-shadow .15s, transform .15s; min-width:0; }
        .cab-action:hover { border-color:#c7d2fe; box-shadow:0 8px 24px -12px rgba(79,70,229,.35); transform:translateY(-1px); }
        .cab-action__icon { width:44px; height:44px; border-radius:12px; display:grid; place-items:center; flex:none; background:var(--acc-soft); color:var(--acc); }
        .cab-action--comparison .cab-action__icon { background:#ccfbf1; color:#0f766e; }
        .cab-action--contract .cab-action__icon { background:#fef3c7; color:#b45309; }
        .cab-action--law .cab-action__icon { background:#ffe4e6; color:#be123c; }
        .cab-action--chat .cab-action__icon { background:#e0f2fe; color:#0369a1; }
        .cab-action--calc .cab-action__icon { background:#ede9fe; color:#6d28d9; }
        .cab-action__body { display:flex; flex-direction:column; gap:2px; min-width:0; }
        .cab-action__title { font-weight:650; font-size:15px; color:var(--ink); }
        .cab-action__text { font-size:13.5px; color:var(--mut); line-height:1.35; }

        /* цифры */
        .cab-stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
        .cab-stat { background:var(--card); border:1px solid var(--line); border-radius:var(--r); padding:16px 18px; display:flex; flex-direction:column; gap:2px; }
        .cab-stat__v { font-size:30px; font-weight:700; line-height:1.1; font-variant-numeric:tabular-nums; letter-spacing:-.02em; }
        .cab-stat__l { font-size:13.5px; color:var(--mut); }

        .cab-grid { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:20px; align-items:start; }
        .cab-side { display:flex; flex-direction:column; gap:16px; }
        .cab-card { background:var(--card); border:1px solid var(--line); border-radius:var(--r); padding:20px; min-width:0; }
        .cab-card__head { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:16px; }
        .cab-card__head--tight { align-items:center; margin-bottom:8px; }
        .cab-card__title { font-size:20px; font-weight:700; letter-spacing:-.01em; }
        .cab-card__title--sm { font-size:16px; }
        .cab-card__sub { margin-top:4px; font-size:14px; color:var(--mut); line-height:1.45; }
        .cab-link { display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:var(--acc); text-decoration:none; border-radius:6px; }
        .cab-link:hover { text-decoration:underline; }

        /* список документов */
        .cab-tools { display:flex; gap:12px; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:12px; }
        .cab-tabs { display:flex; gap:6px; overflow-x:auto; padding-bottom:2px; max-width:100%; }
        .cab-tab { flex:none; min-height:40px; padding:0 14px; border-radius:99px; border:1px solid var(--line); background:#fff; color:var(--ink2); font:600 14px/1 inherit; cursor:pointer; display:inline-flex; gap:8px; align-items:center; }
        .cab-tab b { font-weight:600; font-size:12px; background:var(--soft); color:var(--mut); border-radius:99px; padding:3px 7px; font-variant-numeric:tabular-nums; }
        .cab-tab:hover { background:var(--soft); }
        .cab-tab[aria-pressed="true"] { background:var(--ink); color:#fff; border-color:var(--ink); }
        .cab-tab[aria-pressed="true"] b { background:rgba(255,255,255,.18); color:#fff; }
        .cab-search { position:relative; flex:1 1 220px; max-width:320px; display:block; }
        .cab-search input { width:100%; height:40px; padding:0 12px 0 38px; border:1px solid var(--line); border-radius:12px; font:inherit; font-size:14px; background:#fff; color:var(--ink); }
        .cab-search__i { position:absolute; left:12px; top:10px; color:var(--mut); width:18px; height:18px; pointer-events:none; }

        .cab-list { display:flex; flex-direction:column; border-top:1px solid var(--line); }
        .cab-doc { border-bottom:1px solid var(--line); }
        .cab-doc[hidden] { display:none; }
        .cab-doc__row { display:flex; gap:14px; align-items:center; padding:14px 4px; }
        .cab-kind { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; flex:none; background:var(--acc-soft); color:var(--acc); }
        .cab-kind--comparison { background:#ccfbf1; color:#0f766e; }
        .cab-kind--generation { background:#ede9fe; color:#6d28d9; }
        .cab-kind--contract { background:#fef3c7; color:#b45309; }
        .cab-doc__main { flex:1; min-width:0; }
        .cab-doc__title { font-weight:650; font-size:15px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cab-doc__meta { display:flex; flex-wrap:wrap; gap:6px 8px; align-items:center; margin-top:6px; font-size:13px; color:var(--mut); }
        .cab-doc__actions { display:flex; gap:6px; flex:none; }
        .cab-iconbtn { min-height:40px; min-width:40px; padding:0 10px; border-radius:10px; border:1px solid transparent; background:transparent; color:var(--mut); cursor:pointer; display:inline-flex; align-items:center; justify-content:center; gap:6px; font:600 14px/1 inherit; }
        .cab-iconbtn:hover { background:var(--soft); color:var(--ink); }
        .cab-iconbtn--danger:hover { background:#fef2f2; color:#dc2626; }
        .cab-open { border-color:var(--line); color:var(--ink2); }
        .cab-open__i { width:16px; height:16px; transition:transform .2s; }
        .cab-open[aria-expanded="true"] .cab-open__i { transform:rotate(180deg); }
        .cab-doc__more { margin:0 4px 16px 56px; padding:16px; background:#f9fafb; border:1px solid var(--line); border-radius:12px; max-height:520px; overflow:auto; }
        .cab-doc__more[hidden] { display:none; }
        .cab-doc__details { font-size:14px; color:var(--ink2); line-height:1.55; white-space:pre-wrap; }
        .cab-doc__details + .cab-md { margin-top:12px; }
        .cab-delform { display:none; }
        .cab-more { display:flex; justify-content:center; padding-top:16px; }
        .cab-more [hidden], .cab-nothing[hidden] { display:none; }
        .cab-nothing { padding:28px 8px; text-align:center; color:var(--mut); font-size:14px; }

        .cab-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:99px; font-size:12px; font-weight:600; line-height:1.4; background:var(--soft); color:var(--ink2); }
        .cab-badge--analysis { background:var(--acc-soft); color:#4338ca; }
        .cab-badge--comparison { background:#ccfbf1; color:#0f766e; }
        .cab-badge--generation { background:#ede9fe; color:#6d28d9; }
        .cab-badge--contract { background:#fef3c7; color:#92400e; }
        .cab-badge--risk-low, .cab-badge--ok { background:#dcfce7; color:#166534; }
        .cab-badge--risk-medium, .cab-badge--warn { background:#fef3c7; color:#92400e; }
        .cab-badge--risk-high, .cab-badge--bad { background:#fee2e2; color:#991b1b; }
        .cab-badge--info { background:#e0f2fe; color:#075985; }
        .cab-badge--neutral { background:var(--soft); color:var(--ink2); }
        .cab-doc time { color:var(--mut); }

        /* отчёт (markdown) */
        .cab-md { font-size:14px; line-height:1.6; color:var(--ink2); overflow-wrap:anywhere; }
        .cab-md h1, .cab-md h2, .cab-md h3, .cab-md h4 { color:var(--ink); font-weight:700; line-height:1.3; margin:16px 0 6px; }
        .cab-md h1, .cab-md h2 { font-size:17px; } .cab-md h3 { font-size:15.5px; } .cab-md h4 { font-size:14.5px; }
        .cab-md > :first-child { margin-top:0; }
        .cab-md p { margin:0 0 10px; } .cab-md ul, .cab-md ol { margin:0 0 10px; padding-left:22px; list-style:revert; }
        .cab-md li { margin:3px 0; } .cab-md strong { color:var(--ink); }
        .cab-md code { background:#eef0f4; padding:1px 5px; border-radius:5px; font-size:12.5px; }
        .cab-md blockquote { margin:0 0 10px; padding:2px 12px; border-left:3px solid #c7d2fe; color:var(--mut); }
        .cab-md hr { border:0; border-top:1px solid var(--line); margin:14px 0; }
        .cab-md table { border-collapse:collapse; width:100%; font-size:13px; display:block; overflow-x:auto; }
        .cab-md th, .cab-md td { border:1px solid var(--line); padding:6px 8px; text-align:left; }

        .cab-empty { display:flex; flex-direction:column; align-items:center; text-align:center; gap:8px; padding:40px 16px; }
        .cab-empty__icon { width:56px; height:56px; border-radius:16px; display:grid; place-items:center; background:var(--acc-soft); color:var(--acc); margin-bottom:4px; }
        .cab-empty__icon svg { width:28px; height:28px; }
        .cab-empty__t { font-size:17px; font-weight:700; } .cab-empty__s { color:var(--mut); max-width:38ch; font-size:14.5px; margin-bottom:8px; }

        /* профиль */
        .cab-profile__top { display:flex; gap:14px; align-items:center; }
        .cab-avatar { width:56px; height:56px; border-radius:16px; display:grid; place-items:center; font-size:22px; font-weight:700; color:#fff; flex:none;
                      background:linear-gradient(135deg,#6366f1,#8b5cf6); }
        .cab-profile__id { min-width:0; }
        .cab-profile__name { font-weight:700; font-size:17px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cab-profile__mail { font-size:14px; color:var(--mut); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .cab-kv { margin-top:16px; border-top:1px solid var(--line); }
        .cab-kv > div { display:flex; justify-content:space-between; gap:12px; padding:10px 0; border-bottom:1px solid var(--soft); font-size:14px; }
        .cab-kv > div:last-child { border-bottom:0; padding-bottom:0; }
        .cab-kv dt { color:var(--mut); } .cab-kv dd { font-weight:600; text-align:right; }
        .cab-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:#9ca3af; margin-right:6px; }
        .cab-dot--on { background:#10b981; box-shadow:0 0 0 3px #d1fae5; }

        /* задачи и устройства */
        .cab-task { display:flex; justify-content:space-between; align-items:center; gap:10px; padding:10px 8px; margin-inline:-8px; border-radius:10px; text-decoration:none; color:inherit; }
        .cab-task:hover { background:var(--soft); }
        .cab-task__t { font-size:14.5px; font-weight:550; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; min-width:0; }
        .cab-mini-empty { padding:6px 0 2px; font-size:14px; color:var(--mut); display:flex; flex-direction:column; gap:8px; align-items:flex-start; }
        .cab-devices { display:flex; flex-direction:column; gap:4px; margin-top:12px; }
        .cab-device { display:flex; gap:12px; align-items:center; padding:8px; margin-inline:-8px; border-radius:10px; }
        .cab-device--current { background:var(--acc-soft); }
        .cab-device__i { width:36px; height:36px; border-radius:10px; display:grid; place-items:center; background:#fff; color:var(--ink2); border:1px solid var(--line); flex:none; }
        .cab-device__b { display:flex; flex-direction:column; min-width:0; }
        .cab-device__t { font-size:14.5px; font-weight:600; } .cab-device__s { font-size:13px; color:var(--mut); overflow-wrap:anywhere; }
        .cab-note { margin-top:12px; font-size:13.5px; color:var(--mut); }

        /* окно удаления */
        .cab-dialog { border:0; border-radius:18px; padding:0; width:min(420px, calc(100vw - 32px)); box-shadow:0 24px 64px -16px rgba(17,24,39,.45); color:#111827; }
        .cab-dialog::backdrop { background:rgba(17,24,39,.5); }
        .cab-dialog__box { padding:24px; display:flex; flex-direction:column; gap:10px; }
        .cab-dialog__t { margin:0; font-size:19px; font-weight:700; }
        .cab-dialog__s { margin:0; color:#4b5563; font-size:15px; line-height:1.5; overflow-wrap:anywhere; }
        .cab-dialog__btns { display:flex; gap:10px; justify-content:flex-end; margin-top:10px; }

        @media (max-width: 1023px) {
            .cab-grid { grid-template-columns:minmax(0,1fr); }
            .cab-actions { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .cab-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media (max-width: 560px) {
            .cab { padding-block:20px 48px; }
            .cab-actions { grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
            .cab-action { flex-direction:column; align-items:flex-start; gap:10px; padding:14px; }
            .cab-action__text { display:none; }
            .cab-stat { padding:14px; } .cab-stat__v { font-size:26px; }
            .cab-card { padding:16px; }
            .cab-doc__row { flex-wrap:wrap; }
            .cab-doc__main { flex-basis:calc(100% - 60px); }
            .cab-doc__actions { margin-left:56px; width:calc(100% - 56px); }
            .cab-doc__more { margin-left:0; }
            .cab-search { max-width:none; flex-basis:100%; }
            .cab-head .cab-btn { width:100%; }
        }
        @media (prefers-reduced-motion: reduce) { .cab * { transition:none !important; } }
    </style>

    <script>
        (function () {
            var list = document.getElementById('docList');
            if (!list) return;
            var items = Array.prototype.slice.call(list.querySelectorAll('.cab-doc'));
            var tabs = Array.prototype.slice.call(document.querySelectorAll('.cab-tab'));
            var search = document.getElementById('docSearch');
            var moreBtn = document.getElementById('docMore');
            var nothing = document.getElementById('docNothing');
            var PAGE = 8, shown = PAGE, filter = 'all';

            try { var saved = localStorage.getItem('cabinet_doc_filter'); if (saved && tabs.some(function (t) { return t.dataset.filter === saved; })) filter = saved; } catch (e) {}

            function matches(item, q) {
                var kindOk = filter === 'all' ? item.dataset.all === '1' : item.dataset.kind === filter;
                return kindOk && (!q || item.dataset.text.indexOf(q) !== -1);
            }

            function render() {
                var q = (search && search.value || '').trim().toLowerCase();
                var visible = 0, hiddenByLimit = 0;
                items.forEach(function (item) {
                    if (!matches(item, q)) { item.hidden = true; return; }
                    visible++;
                    if (visible > shown) { item.hidden = true; hiddenByLimit++; } else { item.hidden = false; }
                });
                tabs.forEach(function (t) { t.setAttribute('aria-pressed', String(t.dataset.filter === filter)); });
                if (nothing) nothing.hidden = visible !== 0;
                if (moreBtn) {
                    moreBtn.hidden = hiddenByLimit === 0;
                    moreBtn.textContent = 'Показать ещё ' + Math.min(PAGE, hiddenByLimit) + ' из ' + hiddenByLimit;
                }
            }

            tabs.forEach(function (t) {
                t.addEventListener('click', function () {
                    filter = t.dataset.filter; shown = PAGE;
                    try { localStorage.setItem('cabinet_doc_filter', filter); } catch (e) {}
                    render();
                });
            });
            if (search) search.addEventListener('input', function () { shown = PAGE; render(); });
            if (moreBtn) moreBtn.addEventListener('click', function () { shown += PAGE; render(); });

            // раскрыть / свернуть отчёт
            list.addEventListener('click', function (e) {
                var open = e.target.closest('.cab-open');
                if (open) {
                    var more = open.closest('.cab-doc').querySelector('.cab-doc__more');
                    var isOpen = open.getAttribute('aria-expanded') === 'true';
                    open.setAttribute('aria-expanded', String(!isOpen));
                    open.querySelector('.cab-open__t').textContent = isOpen ? 'Открыть' : 'Свернуть';
                    if (more) more.hidden = isOpen;
                    return;
                }
                var del = e.target.closest('.cab-del');
                if (del) askDelete(del);
            });

            // удаление с подтверждением
            var dialog = document.getElementById('delDialog');
            var nameEl = document.getElementById('delName');
            var pendingForm = null;
            function askDelete(btn) {
                pendingForm = btn.closest('.cab-doc').querySelector('.cab-delform');
                if (!dialog || typeof dialog.showModal !== 'function') {
                    if (window.confirm('Удалить «' + btn.dataset.title + '»?')) pendingForm.submit();
                    return;
                }
                nameEl.textContent = btn.dataset.title;
                dialog.showModal();
            }
            if (dialog) dialog.addEventListener('close', function () {
                if (dialog.returnValue === 'ok' && pendingForm) pendingForm.submit();
                dialog.returnValue = ''; pendingForm = null;
            });

            render();
        })();
    </script>
</x-app-layout>
