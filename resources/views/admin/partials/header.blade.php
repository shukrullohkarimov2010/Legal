<nav class="fixed top-0 left-0 right-0 z-50 card-gradient border-b border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="window.location.href='{{ route('welcome') }}'">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xl font-bold glow-text">LegalAI Pro</span>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('welcome') }}" class="text-slate-300 hover:text-white transition">{{ __('ui.nav_home') }}</a>
                <a href="{{ route('tasks.create') }}" class="text-slate-300 hover:text-white transition">{{ __('ui.nav_features') }}</a>
                <a href="{{ route('tasks.calc') }}" class="text-slate-300 hover:text-white transition">{{ __('ui.nav_calculator') }}</a>
                <a href="{{ route('tasks.chat') }}" class="text-slate-300 hover:text-white transition">{{ __('ui.nav_ai_chat') }}</a>
                <x-language-switcher />
                <div class="relative">
                    <button onclick="toggleNotifications()" class="text-slate-300 hover:text-white transition relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="notification-badge absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs flex items-center justify-center">3</span>
                    </button>
                    <div id="notificationPanel" class="hidden absolute right-0 mt-2 w-80 card-gradient rounded-xl border border-slate-700 shadow-xl z-50">
                        <div class="p-4 border-b border-slate-700">
                            <h4 class="font-semibold">{{ __('ui.notifications') }}</h4>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    <div>
                                        <p class="text-sm">{{ __('ui.notification_analysis_done') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.minutes_ago_2') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 border-b border-slate-700 hover:bg-slate-800/50 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                                    <div>
                                        <p class="text-sm">{{ __('ui.notification_high_risk') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.minutes_ago_15') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 hover:bg-slate-800/50 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                    <div>
                                        <p class="text-sm">{{ __('ui.notification_expiring') }}</p>
                                        <p class="text-xs text-slate-400 mt-1">{{ __('ui.hour_ago_1') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('login') }}"
                   class="btn-primary px-6 py-2 rounded-lg font-medium inline-block text-center">
                    {{ __('ui.nav_login') }}
                </a>
            </div>
        </div>
    </div>
</nav>
