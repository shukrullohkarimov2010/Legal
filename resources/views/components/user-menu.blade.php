<div class="relative group">
    <button class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center cursor-pointer hover:shadow-lg transition duration-200 border-2 border-white/30">
        <span class="text-lg">👤</span>
    </button>
    <div class="absolute right-0 mt-1 w-48 bg-white text-gray-800 dark:bg-gray-800 dark:text-white rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50 overflow-hidden border border-gray-200 dark:border-gray-700">
        @auth
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                {{ __('ui.my_profile') ?? '👤 Профиль' }}
            </a>
            <a href="{{ route('cabinet.index') }}" class="block px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Кабинет
            </a>
            <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                {{ __('ui.nav_dashboard') ?? '📊 Панель' }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition border-t border-gray-200 dark:border-gray-700">
                    {{ __('ui.nav_logout') ?? '🚪 Выйти' }}
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-4 py-3 text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                {{ __('ui.nav_login') ?? 'Войти' }}
            </a>
            <a href="{{ route('register') }}" class="block px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                {{ __('ui.nav_register') ?? 'Регистрация' }}
            </a>
        @endauth
    </div>
</div>
