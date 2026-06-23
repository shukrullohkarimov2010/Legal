@props([
    'title',
    'description',
    'date',
    'record' => [],
    'formatResult',
    'resultLabel' => 'AI-ответ',
])

<div class="rounded-md bg-gray-50 p-3 text-sm">
    <p class="font-medium text-gray-900">{{ $title }}</p>
    @if(!empty($record['file_name']))
        <p class="mt-1 text-xs font-medium text-gray-500">{{ $record['file_name'] }}</p>
    @endif
    <p class="mt-1 text-gray-600">{{ $description }}</p>
    @if(!empty($record['summary']))
        <p class="mt-2 rounded bg-white p-2 text-gray-700">{{ $record['summary'] }}</p>
    @endif
    @if(!empty($record['result']))
        <details class="mt-2 rounded border border-gray-200 bg-white p-2">
            <summary class="cursor-pointer text-xs font-semibold text-gray-700">{{ $resultLabel }}</summary>
            <pre class="mt-2 max-h-80 overflow-auto whitespace-pre-wrap break-words rounded bg-gray-50 p-3 text-xs leading-5 text-gray-700">{{ $formatResult($record['result']) }}</pre>
        </details>
    @endif
    <p class="mt-2 text-xs text-gray-500">{{ $date }}</p>
</div>
