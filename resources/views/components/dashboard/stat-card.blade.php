@props(['title', 'value', 'sub' => null, 'accent' => 'blue'])
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-gray-200 shadow-sm p-5']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900 tracking-tight">{{ $value }}</p>
            @if ($sub)
                <p class="mt-1 text-xs text-gray-400">{{ $sub }}</p>
            @endif
        </div>
        @isset($icon)
            <div class="shrink-0 rounded-lg p-2.5 bg-{{ $accent }}-50 text-{{ $accent }}-600">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>
