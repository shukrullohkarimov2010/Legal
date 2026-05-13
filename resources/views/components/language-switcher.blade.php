<div class="relative" x-data="{ open: false }" @click.away="open = false">
    @php
        $currentLocale = app()->getLocale();
    @endphp

        <!-- Кнопка переключения -->
    <button
        type="button"
        @click.stop="open = !open"
        class="group inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-white/10 backdrop-blur-md rounded-xl border border-white/20 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-indigo-400/50 transition-all duration-200 shadow-lg"
        :aria-expanded="open"
        aria-haspopup="true"
    >
        <span class="text-white font-bold">{{ strtoupper($currentLocale) }}</span>
        <svg
            class="w-4 h-4 text-white/60 transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown Panel -->
    <div
        x-show="open"
        x-cloak
        @keydown.escape.window="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        class="absolute right-0 z-50 mt-2 w-64 origin-top-right rounded-2xl bg-slate-900/98 backdrop-blur-xl border border-white/10 shadow-2xl ring-1 ring-black/20 overflow-hidden"
        role="menu"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-white/10 bg-white/5">
            <h3 class="text-xs font-bold text-white/60 uppercase tracking-wider">
                {{ __('ЗАБОНИ ИНТЕРФЕЙС') }}
            </h3>
        </div>

        <!-- Language Options -->
        <div class="p-2 space-y-1">
            @php
                $locales = [
                    'ru' => ['label' => 'Русский', 'flag' => 'https://flagcdn.com/w40/ru.png'],
                    'en' => ['label' => 'English', 'flag' => 'https://flagcdn.com/w40/gb.png'],
                    'tg' => ['label' => 'Тоҷикӣ', 'flag' => 'https://flagcdn.com/w40/tj.png'],
                ];
            @endphp

            @foreach($locales as $code => $data)
                <a
                    href="{{ route('locale.switch', ['locale' => $code]) }}"
                    class="group flex items-center gap-3 px-3.5 py-3 text-sm rounded-xl transition-all duration-200 {{ $code === $currentLocale ? 'bg-indigo-600/20 text-white' : 'text-white/80 hover:bg-white/10 hover:text-white' }}"
                    role="menuitem"
                >
                    <!-- Flag Image -->
                    <img src="{{ $data['flag'] }}" alt="{{ $data['label'] }}" class="w-6 h-4 object-cover rounded shadow-sm flex-shrink-0">

                    <!-- Label -->
                    <span class="flex-1 font-medium">{{ $data['label'] }}</span>

                    <!-- Checkmark -->
                    @if($code === $currentLocale)
                        <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @else
                        <span class="w-5 h-5 flex-shrink-0"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush
