@props(['docente'])

@php
    $foto = $docente->_foto_url ?: ($docente->relationLoaded('media') ? $docente->getFirstMediaUrl('foto', 'thumb') : '');
@endphp

<article class="card-hover flex h-full flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white">
    {{-- Foto / fallback iniciales --}}
    <div class="aspect-square overflow-hidden">
        @if ($foto)
            <img src="{{ $foto }}" alt="{{ $docente->name }}"
                 class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-800 to-navy-950">
                <span class="text-4xl font-semibold tracking-tight text-white/90">{{ $docente->iniciales }}</span>
            </div>
        @endif
    </div>

    {{-- Datos --}}
    <div class="p-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-gold-500">{{ $docente->area }}</p>
        <h3 class="mt-1 text-lg font-semibold leading-snug text-navy-900">{{ $docente->name }}</h3>
        <p class="mt-0.5 text-xs text-stone-400">{{ $docente->grado }}</p>
    </div>
</article>
