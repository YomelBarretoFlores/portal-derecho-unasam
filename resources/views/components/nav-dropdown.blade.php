@props(['label', 'items' => []])

<div class="group relative">
    <button aria-haspopup="true" class="flex items-center gap-1 rounded-lg px-3.5 py-2 text-[15px] font-medium text-navy-800 transition hover:bg-stone-100 hover:text-navy-900">
        {{ $label }}
        <svg class="h-3.5 w-3.5 transition group-hover:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div class="invisible absolute left-0 top-full z-50 min-w-56 translate-y-1 pt-2 opacity-0 transition-all duration-200 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
        <div role="menu" class="overflow-hidden rounded-xl border border-stone-200 bg-white py-2 shadow-card-lg">
            @foreach ($items as $i => [$texto, $url])
                <a href="{{ $url }}" wire:navigate.hover role="menuitem"
                   style="transition-delay: {{ $i * 30 }}ms"
                   class="block px-4 py-2.5 text-sm text-stone-600 opacity-0 translate-y-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-y-0 hover:bg-stone-100 hover:text-navy-900">
                    {{ $texto }}
                </a>
            @endforeach
        </div>
    </div>
</div>
