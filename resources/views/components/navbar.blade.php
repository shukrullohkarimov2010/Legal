<header class="sticky top-0 z-40 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 backdrop-blur-sm bg-opacity-95 dark:bg-opacity-95 shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo & Title -->
            <a href="{{ route('welcome') }}" class="flex items-center space-x-3 hover:opacity-80 transition">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <span class="text-lg font-bold text-white">⚖️</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('ui.app_name') }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.welcome_title') ?? 'AI Legal Analysis' }}</p>
                </div>
            </a>

            <!-- Center: Navigation Links -->
            @if (Route::is('welcome'))
            <nav class="hidden md:flex items-center space-x-6">
                <a href="#features" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    {{ __('ui.nav_features') ?? 'Возможности' }}
                </a>
                <a href="#how" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    {{ __('ui.welcome_how_title') ?? 'Как это работает' }}
                </a>
                <a href="#use-cases" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                    {{ __('ui.welcome_examples_title') ?? 'Примеры' }}
                </a>
            </nav>
            @endif

            <!-- Right: Actions & Settings -->
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode()" class="sr-only peer">
                    <div class="w-10 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:peer-checked:bg-indigo-600"></div>
                </label>

                <!-- Language Switcher -->
                <x-language-switcher />

                <!-- User Menu -->
                <x-user-menu />
            </div>
        </div>
    </div>
</header>
