@props(['docente'])

@php
    $foto = $docente->_foto_url ?? '';
    if (! $foto && method_exists($docente, 'relationLoaded') && $docente->relationLoaded('media')) {
        $foto = $docente->getFirstMediaUrl('foto', 'thumb');
    }
@endphp

<article class="card-hover group flex h-full flex-col overflow-hidden rounded-none border border-stone-200 bg-white">
    {{-- Foto / fallback iniciales --}}
    <div class="aspect-[4/5] overflow-hidden bg-stone-50 p-2">
        @if ($foto)
            <img src="{{ $foto }}" alt="Retrato de {{ $docente->name }}" loading="lazy" decoding="async"
                 class="h-full w-full object-contain object-top grayscale-[8%] transition duration-300 group-hover:grayscale-0">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-800 to-navy-950">
                <span class="text-4xl font-semibold tracking-tight text-white/90">{{ $docente->iniciales }}</span>
            </div>
        @endif
    </div>

    {{-- Datos --}}
    <div class="p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-gold-600">{{ $docente->area }}</p>
        <h3 class="mt-1 text-lg font-semibold leading-snug text-navy-900">
            <a href="{{ route('docentes.show', $docente->slug) }}" wire:navigate.hover class="transition hover:text-gold-600">
                {{ $docente->name }}
            </a>
        </h3>
        <p class="mt-0.5 text-xs text-stone-500">{{ $docente->grado }}</p>
        <p class="mt-4 text-sm font-semibold text-navy-700">
            <a href="{{ route('docentes.show', $docente->slug) }}" wire:navigate.hover class="inline-flex items-center gap-2">Ver perfil <x-ui-icon name="arrow-right" /></a>
        </p>
    </div>
</article>
