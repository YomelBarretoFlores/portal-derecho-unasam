@props(['post', 'featured' => false])

@php
    $estilos = [
        'noticia' => ['bg-navy-100 text-navy-700', 'Noticia'],
        'opinion' => ['bg-stone-100 text-stone-600', 'Opinión'],
        'evento'  => ['bg-gold-100 text-gold-600', 'Evento'],
    ];
    [$badge, $etiqueta] = $estilos[$post->tipo] ?? $estilos['noticia'];
@endphp

{{-- Destacado: hugea su contenido (sin h-full/grow) y agranda el título. En rejilla
     uniforme (no featured) usa h-full + grow para igualar alturas. --}}
<article class="card-hover flex flex-col rounded-none border border-stone-200 bg-white {{ $featured ? 'p-8' : 'h-full p-6' }}">
    <div class="flex items-center gap-3">
        <span class="rounded-none px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $badge }}">{{ $etiqueta }}</span>
        <time datetime="{{ $post->fecha->toDateString() }}" class="text-xs text-stone-400">{{ $post->fecha->translatedFormat('d M Y') }}</time>
    </div>
    <h3 class="mt-3 font-semibold leading-snug text-navy-900 {{ $featured ? 'text-2xl' : 'text-lg' }}">{{ $post->titulo }}</h3>
    <p class="mt-2 leading-relaxed text-stone-500 {{ $featured ? 'text-[15px]' : 'grow text-sm' }}">{{ $post->extracto }}</p>
    <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4 text-xs text-stone-400 {{ $featured ? 'mt-6' : '' }}">
        <span>{{ $post->autor }}</span>
        <span>{{ $post->tiempo_lectura }} de lectura</span>
    </div>
</article>
