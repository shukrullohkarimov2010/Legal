<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Калькулятор неустойки</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca'
                        },
                        slateplus: {
                            950: '#09111f'
                        },
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            input: '#334155',
                            border: '#475569'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 50px rgba(15, 23, 42, 0.12)'
                    }
                }
            }
        };
    </script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .page-bg {
            background:
                radial-gradient(circle at top, rgba(99, 102, 241, 0.16), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }

        .dark .page-bg {
            background:
                radial-gradient(circle at top, rgba(99, 102, 241, 0.18), transparent 28%),
                linear-gradient(180deg, #020617 0%, #0f172a 100%);
        }

        .nav-surface {
            background: rgba(9, 17, 31, 0.92);
            backdrop-filter: blur(14px);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(18px);
        }

        .dark .glass-card {
            background: rgba(15, 23, 42, 0.88);
        }

        .selected-card {
            border-color: #6366f1;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            color: #4338ca;
            box-shadow: 0 16px 30px rgba(99, 102, 241, 0.18);
        }

        .dark .selected-card {
            border-color: #818cf8;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.35) 0%, rgba(67, 56, 202, 0.2) 100%);
            color: #e0e7ff;
        }

        .gradient-btn {
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
        }

        .gradient-btn:hover {
            background: linear-gradient(90deg, #4338ca 0%, #6d28d9 100%);
        }

        .gradient-result {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .theme-toggle {
            box-shadow: 0 18px 40px rgba(79, 70, 229, 0.3);
        }

        .surface-border {
            border-color: rgba(148, 163, 184, 0.22);
        }

        .dark .surface-border {
            border-color: rgba(71, 85, 105, 0.75);
        }

        .animate-fade-in {
            animation: fadeIn 0.35s ease-out;
        }

        .animate-rise {
            animation: riseIn 0.35s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="page-bg min-h-screen text-slate-800 transition-colors duration-300 dark:text-slate-100">
<div id="app">
    <nav class="nav-surface fixed inset-x-0 top-0 z-50 border-b border-white/10">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016" />
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-semibold tracking-[0.25em] text-blue-200">LEGALAI PRO</div>
                    <div class="text-sm text-slate-300">Калькулятор неустойки</div>
                </div>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ route('tasks.create') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Возможности</a>
                <a href="{{ route('tasks.calc') }}" class="text-sm font-medium text-white">Калькулятор</a>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Дашборд</a>
                <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                    Войти
                </a>
            </div>
        </div>
    </nav>

    <main class="px-4 pb-8 pt-24 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-600 dark:text-brand-100">Smart Claim Flow</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                        Рассчитайте сумму неустойки быстро и без перегруженного интерфейса
                    </h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">
                        Выберите тип договора, укажите сумму и срок просрочки, а затем получите итоговую сумму взыскания в читаемом формате.
                    </p>
                </div>

                <button
                    @click="toggleTheme"
                    class="theme-toggle hidden h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 text-white transition hover:scale-105 md:flex"
                    title="Переключить тему"
                >
                    <svg v-if="isDark" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0" />
                    </svg>
                    <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <div class="glass-card overflow-hidden rounded-[2rem] border surface-border shadow-soft">
                <div class="flex flex-col md:flex-row">
                    <section class="w-full border-b surface-border p-6 md:w-2/3 md:border-b-0 md:border-r md:p-10">
                        <div class="mb-8 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-brand-600 dark:text-brand-200">Параметры расчета</p>
                                <h2 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Настройте условия взыскания</h2>
                            </div>
                            <span class="rounded-full bg-brand-50 px-4 py-2 text-xs font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-100">
                                Юридический калькулятор
                            </span>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Сумма договора
                                </label>
                                <div class="relative">
                                    <input
                                        v-model.number="amount"
                                        type="number"
                                        placeholder="Например, 100000"
                                        class="w-full rounded-2xl border surface-border bg-white/80 px-4 py-4 pr-16 text-lg text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:bg-dark-input dark:text-slate-100"
                                    >
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400 dark:text-slate-400">сом</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Срок просрочки
                                </label>
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <div class="relative flex-1">
                                        <input
                                            v-model.number="days"
                                            type="number"
                                            placeholder="Например, 30"
                                            class="w-full rounded-2xl border surface-border bg-white/80 px-4 py-4 pr-16 text-lg text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:bg-dark-input dark:text-slate-100"
                                        >
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400 dark:text-slate-400">ед.</span>
                                    </div>
                                    <select
                                        v-model="timeUnit"
                                        class="rounded-2xl border surface-border bg-white/80 px-4 py-4 text-base text-slate-800 shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:bg-dark-input dark:text-slate-100"
                                    >
                                        <option value="days">Дней</option>
                                        <option value="weeks">Недель</option>
                                        <option value="months">Месяцев</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Тип договора
                                </label>
                                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                                    <button
                                        v-for="type in contractTypes"
                                        :key="type.id"
                                        type="button"
                                        @click="contractType = type.id"
                                        :class="[
                                            'rounded-2xl border p-4 text-left transition duration-200',
                                            contractType === type.id
                                                ? 'selected-card'
                                                : 'border surface-border bg-white/70 text-slate-600 hover:-translate-y-0.5 hover:shadow-md dark:bg-dark-input/70 dark:text-slate-300'
                                        ]"
                                    >
                                        <div class="text-sm font-semibold">@{{ type.label }}</div>
                                        <div class="mt-1 text-xs opacity-70">@{{ type.note }}</div>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="mb-3 block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    Процент неустойки
                                </label>
                                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                                    <button
                                        v-for="rate in rates"
                                        :key="rate.id"
                                        type="button"
                                        @click="selectRate(rate)"
                                        :class="[
                                            'rounded-2xl border p-4 text-left transition duration-200',
                                            selectedRate.id === rate.id
                                                ? 'selected-card'
                                                : 'border surface-border bg-white/70 text-slate-600 hover:-translate-y-0.5 hover:shadow-md dark:bg-dark-input/70 dark:text-slate-300'
                                        ]"
                                    >
                                        <div class="text-base font-bold">@{{ rate.label }}</div>
                                        <div class="mt-1 text-xs opacity-70">@{{ rate.desc }}</div>
                                    </button>
                                </div>

                                <div v-if="selectedRate.id === 'custom'" class="animate-rise mt-4">
                                    <div class="relative">
                                        <input
                                            v-model.number="customRateValue"
                                            type="number"
                                            step="0.001"
                                            placeholder="Введите свой процент"
                                            class="w-full rounded-2xl border surface-border bg-white/80 px-4 py-4 pr-12 text-base text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:bg-dark-input dark:text-slate-100"
                                        >
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400 dark:text-slate-400">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10">
                            <button
                                @click="calculate"
                                type="button"
                                class="gradient-btn flex w-full items-center justify-center gap-3 rounded-2xl px-6 py-5 text-lg font-bold text-white shadow-lg transition duration-200 hover:-translate-y-0.5"
                            >
                                <span>Рассчитать неустойку</span>
                            </button>
                        </div>
                    </section>

                    <aside class="w-full bg-slate-50/70 p-6 md:w-1/3 md:p-10 dark:bg-slate-950/40">
                        <div class="mb-8 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m4 6V7m4 10v-4M5 19h14" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-brand-600 dark:text-brand-200">Результат</p>
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Итоги расчета</h2>
                            </div>
                        </div>

                        <div v-if="!hasCalculated" class="flex min-h-[420px] flex-col items-center justify-center text-center animate-fade-in">
                            <div class="flex h-28 w-28 items-center justify-center rounded-[2rem] bg-gradient-to-br from-brand-500 to-purple-600 text-white shadow-2xl shadow-indigo-500/30">
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m-6 4h6m-7 8h8a2 2 0 002-2V7.828a2 2 0 00-.586-1.414l-2.828-2.828A2 2 0 0013.172 3H8a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="mt-6 text-xl font-bold text-slate-900 dark:text-white">Введите данные для расчета</h3>
                            <p class="mt-3 max-w-xs text-sm leading-6 text-slate-500 dark:text-slate-400">
                                После ввода суммы, срока и ставки справа появится детальная разбивка и итоговая сумма взыскания.
                            </p>
                        </div>

                        <div v-else class="animate-rise">
                            <div class="gradient-result rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-500/20">
                                <p class="text-sm font-medium text-indigo-100">Общая сумма к взысканию</p>
                                <p class="mt-3 text-4xl font-extrabold leading-none sm:text-5xl">@{{ formatCurrency(totalAmount) }}</p>
                                <p class="mt-3 text-sm text-indigo-100">Основной долг плюс рассчитанная неустойка</p>
                            </div>

                            <div class="mt-6 rounded-[2rem] border surface-border bg-white/80 p-5 shadow-sm dark:bg-dark-card/80">
                                <ul class="space-y-4 text-sm">
                                    <li class="flex items-center justify-between gap-4 border-b surface-border pb-4">
                                        <span class="text-slate-500 dark:text-slate-400">Основной долг</span>
                                        <span class="font-bold text-slate-900 dark:text-white">@{{ formatCurrency(amount) }}</span>
                                    </li>
                                    <li class="flex items-center justify-between gap-4 border-b surface-border pb-4">
                                        <span class="text-slate-500 dark:text-slate-400">Сумма неустойки</span>
                                        <span class="font-bold text-slate-900 dark:text-white">@{{ formatCurrency(resultAmount) }}</span>
                                    </li>
                                    <li class="flex items-center justify-between gap-4 border-b surface-border pb-4">
                                        <span class="text-slate-500 dark:text-slate-400">Ставка</span>
                                        <span class="font-bold text-slate-900 dark:text-white">@{{ selectedRateText() }}</span>
                                    </li>
                                    <li class="flex items-center justify-between gap-4 border-b surface-border pb-4">
                                        <span class="text-slate-500 dark:text-slate-400">Период просрочки</span>
                                        <span class="font-bold text-slate-900 dark:text-white">@{{ days }} @{{ getPeriodLabel() }}</span>
                                    </li>
                                    <li class="flex items-center justify-between gap-4 pt-1">
                                        <span class="text-base font-semibold text-slate-900 dark:text-white">Итого</span>
                                        <span class="text-lg font-extrabold text-brand-600 dark:text-brand-200">@{{ formatCurrency(totalAmount) }}</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-6 rounded-[2rem] border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100">
                                Полученный расчет является ориентировочным. Для процессуальных документов лучше проверить ставку и правовую основу вручную.
                            </div>

                            <button
                                @click="reset"
                                type="button"
                                class="mt-6 w-full rounded-2xl border surface-border px-4 py-3 font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-dark-input"
                            >
                                Сбросить
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                isDark: false,
                amount: null,
                days: null,
                timeUnit: 'days',
                contractType: 'general',
                contractTypes: [
                    { id: 'general', label: 'Общий', note: 'Базовый сценарий' },
                    { id: 'consumer', label: 'Потребительский', note: 'Для споров с физлицом' },
                    { id: 'construction', label: 'Подряд', note: 'Строительство и работы' },
                    { id: 'labor', label: 'Трудовой', note: 'Кадровые обязательства' }
                ],
                selectedRate: { id: 'key', label: '0.033%', desc: 'Ключевая ставка ЦБ' },
                customRateValue: null,
                rates: [
                    { id: 'key', label: '0.033%', desc: 'Ключевая ставка ЦБ' },
                    { id: 'standard', label: '0.1%', desc: 'Стандартная договорная ставка' },
                    { id: 'consumer', label: '0.5%', desc: 'Потребительская модель' },
                    { id: 'custom', label: 'Свой %', desc: 'Ручной ввод' }
                ],
                hasCalculated: false,
                resultAmount: 0,
                totalAmount: 0
            };
        },
        mounted() {
            this.initTheme();
        },
        methods: {
            initTheme() {
                const html = document.documentElement;
                const shouldUseDark = localStorage.theme === 'dark'
                    || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

                this.isDark = shouldUseDark;
                html.classList.toggle('dark', shouldUseDark);
            },
            toggleTheme() {
                this.isDark = !this.isDark;
                document.documentElement.classList.toggle('dark', this.isDark);
                localStorage.theme = this.isDark ? 'dark' : 'light';
            },
            selectRate(rate) {
                this.selectedRate = rate;
            },
            calculate() {
                if (!this.amount || !this.days || this.amount <= 0 || this.days <= 0) {
                    alert('Укажите сумму договора и срок просрочки больше нуля.');
                    return;
                }

                if (this.selectedRate.id === 'custom' && (!this.customRateValue || this.customRateValue <= 0)) {
                    alert('Введите корректный пользовательский процент.');
                    return;
                }

                let rateDecimal = 0;

                if (this.selectedRate.id === 'custom') {
                    rateDecimal = this.customRateValue / 100;
                } else {
                    rateDecimal = parseFloat(this.selectedRate.label) / 100;
                }

                let totalDays = this.days;

                if (this.timeUnit === 'weeks') totalDays *= 7;
                if (this.timeUnit === 'months') totalDays *= 30;

                this.resultAmount = this.amount * rateDecimal * totalDays;
                this.totalAmount = Number(this.amount) + this.resultAmount;
                this.hasCalculated = true;
            },
            reset() {
                this.amount = null;
                this.days = null;
                this.timeUnit = 'days';
                this.contractType = 'general';
                this.selectedRate = this.rates[0];
                this.customRateValue = null;
                this.hasCalculated = false;
                this.resultAmount = 0;
                this.totalAmount = 0;
            },
            formatCurrency(value) {
                return `${new Intl.NumberFormat('ru-RU', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }).format(value || 0)} сом`;
            },
            getPeriodLabel() {
                if (this.timeUnit === 'days') return 'дней';
                if (this.timeUnit === 'weeks') return 'недель';
                if (this.timeUnit === 'months') return 'месяцев';
                return '';
            },
            selectedRateText() {
                if (this.selectedRate.id === 'custom') {
                    return `${this.customRateValue}%`;
                }

                return this.selectedRate.label;
            }
        }
    }).mount('#app');
</script>
</body>
</html>
