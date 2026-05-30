@props(['post'])

@php
    $estilos = [
        'noticia' => ['bg-navy-100 text-navy-700', 'Noticia'],
        'opinion' => ['bg-stone-100 text-stone-600', 'Opinión'],
        'evento'  => ['bg-gold-100 text-gold-600', 'Evento'],
    ];
    [$badge, $etiqueta] = $estilos[$post->tipo] ?? $estilos['noticia'];
@endphp

<article class="card-hover flex h-full flex-col rounded-2xl border border-stone-200 bg-white p-6">
    <div class="flex items-center gap-3">
        <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $badge }}">{{ $etiqueta }}</span>
        <time class="text-xs text-stone-400">{{ $post->fecha->translatedFormat('d M Y') }}</time>
    </div>
    <h3 class="mt-3 text-lg font-semibold leading-snug text-navy-900">{{ $post->titulo }}</h3>
    <p class="mt-2 grow text-sm leading-relaxed text-stone-500">{{ $post->extracto }}</p>
    <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4 text-xs text-stone-400">
        <span>{{ $post->autor }}</span>
        <span>{{ $post->tiempo_lectura }} de lectura</span>
    </div>
</article>
