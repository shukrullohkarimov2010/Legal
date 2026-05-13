@php
    $supportedLocales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
@endphp

<div class="flex items-center gap-2">
    @foreach ($supportedLocales as $locale => $label)
        <a
            href="{{ route('locale.switch', $locale) }}"
            class="px-3 py-1.5 rounded-md text-sm font-medium transition border {{ $currentLocale === $locale ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white/80 border-slate-300 text-slate-700 hover:border-slate-400 hover:text-slate-900' }}"
        >
            {{ $label }}
        </a>
    @endforeach
</div>
