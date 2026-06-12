@props(['titulo', 'descripcion', 'href'])

<a href="{{ $href }}" wire:navigate.hover class="card-hover group flex h-full flex-col rounded-none border border-stone-200 bg-white p-7">
    <h3 class="text-lg font-semibold text-navy-900">{{ $titulo }}</h3>
    <p class="mt-2 grow text-sm leading-relaxed text-stone-500">{{ $descripcion }}</p>
    <span class="mt-5 inline-flex items-center gap-1 text-sm font-medium text-navy-700">
        Ver más
        <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </span>
</a>
