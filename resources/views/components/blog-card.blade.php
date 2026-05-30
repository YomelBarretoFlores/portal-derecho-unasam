@props(['post'])

@php
    $estilos = [
        'noticia' => ['bg-blue-50 text-blue-600', 'Noticia'],
        'opinion' => ['bg-gold-100 text-gold-600', 'Opinión'],
        'evento'  => ['bg-[#E8F5E9] text-[#2E7D32]', 'Evento'],
    ];
    [$badge, $etiqueta] = $estilos[$post->tipo] ?? $estilos['noticia'];
@endphp

<article class="card-hover flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6">
    <div class="flex items-center gap-3">
        <span class="rounded-full px-2.5 py-1 font-sans text-[11px] font-bold uppercase tracking-wide {{ $badge }}">{{ $etiqueta }}</span>
        <time class="font-sans text-xs text-gray-400">{{ $post->fecha->translatedFormat('d M Y') }}</time>
    </div>
    <h3 class="mt-3 text-lg font-semibold leading-snug text-navy-900">{{ $post->titulo }}</h3>
    <p class="mt-2 grow font-sans text-sm leading-relaxed text-gray-500">{{ $post->extracto }}</p>
    <div class="mt-4 flex items-center justify-between font-sans text-xs text-gray-400">
        <span>{{ $post->autor }}</span>
        <span>{{ $post->tiempo_lectura }} de lectura</span>
    </div>
</article>
