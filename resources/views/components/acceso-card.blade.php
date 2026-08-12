@props(['titulo', 'descripcion', 'href'])

<a href="{{ $href }}" wire:navigate.hover class="group flex min-h-40 flex-col bg-white p-6 transition-colors hover:bg-navy-950 md:p-7">
    <div class="flex items-start justify-between gap-5">
        <span class="font-serif text-2xl text-gold-500" aria-hidden="true">§</span>
        <svg class="h-5 w-5 text-stone-300 transition group-hover:translate-x-1 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </div>
    <h3 class="mt-6 text-lg font-semibold text-navy-900 transition-colors group-hover:text-white">{{ $titulo }}</h3>
    <p class="mt-2 grow text-sm leading-relaxed text-stone-500 transition-colors group-hover:text-white/65">{{ $descripcion }}</p>
    <span class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-navy-700 transition-colors group-hover:text-gold-300">
        Consultar
        <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </span>
</a>
