@props(['label', 'items' => []])

<div class="group relative">
    <button class="flex items-center gap-1 rounded-lg px-3.5 py-2 text-sm font-medium text-gray-600 transition hover:bg-blue-50 hover:text-navy-900">
        {{ $label }}
        <svg class="h-3.5 w-3.5 transition group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div class="invisible absolute left-0 top-full z-50 min-w-56 translate-y-1 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white py-2 shadow-xl">
            @foreach ($items as [$texto, $url])
                <a href="{{ $url }}" wire:navigate
                   class="block px-4 py-2.5 text-sm text-gray-600 transition hover:bg-blue-50 hover:text-navy-900">
                    {{ $texto }}
                </a>
            @endforeach
        </div>
    </div>
</div>
