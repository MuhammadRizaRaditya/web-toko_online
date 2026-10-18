@props(['href', 'title', 'desc' => null])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex items-start gap-4 bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:border-blue-300 hover:shadow transition']) }}>
    @isset($icon)
        <div class="shrink-0 rounded-lg p-2.5 bg-gray-50 text-gray-500 group-hover:bg-blue-50 group-hover:text-blue-600 transition">
            {{ $icon }}
        </div>
    @endisset
    <div class="min-w-0">
        <p class="font-semibold text-gray-900 group-hover:text-blue-700">{{ $title }}</p>
        @if ($desc)
            <p class="mt-0.5 text-sm text-gray-500">{{ $desc }}</p>
        @endif
    </div>
    <svg class="ml-auto w-4 h-4 mt-1 text-gray-300 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>
