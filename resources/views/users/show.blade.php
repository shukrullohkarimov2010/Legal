<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Профиль пользователя
            </h2>
            <p class="text-sm text-gray-600">
                {{ $user->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- ID -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID</label>
                        <p class="text-gray-900">{{ $user->id }}</p>
                    </div>

                    <!-- Name -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <p class="text-gray-900">
                            <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:text-blue-900">{{ $user->email }}</a>
                        </p>
                    </div>

                    <!-- Status -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Статус</label>
                        <div>
                            @if($user->isOnline())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>
                                    Онлайн
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-600 rounded-full mr-2"></span>
                                    Офлайн
                                </span>
                                @if($user->last_seen_at)
                                    <p class="text-sm text-gray-600 mt-2">Был в сети: {{ $user->last_seen_at->diffForHumans() }}</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Provider -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Поставщик</label>
                        <div>
                            @if($user->provider)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $user->provider }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Email Verified -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email подтвержден</label>
                        <div>
                            @if($user->email_verified_at)
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Да</span>
                                <p class="text-sm text-gray-600 mt-2">{{ $user->email_verified_at->format('d.m.Y H:i') }}</p>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Нет</span>
                            @endif
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Дата регистрации</label>
                        <p class="text-gray-900">{{ $user->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <!-- Updated At -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Последнее обновление</label>
                        <p class="text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    ← Вернуться к списку
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
