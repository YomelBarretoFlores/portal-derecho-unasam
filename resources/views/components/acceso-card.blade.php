@props(['numero', 'titulo', 'descripcion', 'href'])

<a href="{{ $href }}" wire:navigate class="card-hover group block rounded-2xl border border-stone-200 bg-white p-7">
    <span class="text-2xl font-semibold tracking-tight text-stone-300">{{ $numero }}</span>
    <h3 class="mt-3 text-lg font-semibold text-navy-900">{{ $titulo }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-stone-500">{{ $descripcion }}</p>
    <span class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-navy-700">
        Ver más
        <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </span>
</a>
