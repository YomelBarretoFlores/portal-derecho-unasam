@props(['post', 'featured' => false, 'horizontal' => false])

@php
    $estilos = [
        'noticia' => ['bg-navy-100 text-navy-700', 'Noticia'],
        'opinion' => ['bg-stone-100 text-stone-600', 'Opinión'],
        'evento'  => ['bg-gold-100 text-gold-600', 'Evento'],
    ];
    [$badge, $etiqueta] = $estilos[$post->tipo] ?? $estilos['noticia'];

    // Imagen destacada subida desde el panel (colección «imagen», conversión «thumb»).
    // Funciona desde caché (_imagen_url) o en vivo (relación media cargada).
    $imagen = $post->_imagen_url ?? '';
    if (! $imagen && method_exists($post, 'relationLoaded') && $post->relationLoaded('media')) {
        $imagen = $post->getFirstMediaUrl('imagen', 'thumb');
    }
@endphp

<article @class([
    'card-hover overflow-hidden border border-stone-200 bg-white',
    'grid md:grid-cols-[minmax(15rem,0.72fr)_minmax(0,1.28fr)]' => $horizontal,
    'flex h-full flex-col' => ! $horizontal,
])>
    {{-- Imagen destacada (solo si se subió una) --}}
    @if ($imagen)
        <div @class(['overflow-hidden', 'min-h-64' => $horizontal, 'aspect-[3/2]' => ! $horizontal])>
            <img src="{{ $imagen }}" alt="{{ $post->titulo }}" loading="lazy" decoding="async"
                 class="h-full w-full object-cover">
        </div>
    @elseif ($horizontal)
        <div class="paper-grid relative flex min-h-60 items-end overflow-hidden border-b border-stone-200 p-8 md:min-h-full md:border-b-0 md:border-r">
            <span class="pointer-events-none absolute right-7 top-7 select-none font-serif text-[8rem] font-bold leading-none text-navy-900/[0.045] md:right-9 md:top-8 md:text-[9rem]" aria-hidden="true">D</span>
            <div class="relative">
                <span class="eyebrow">Actualidad institucional</span>
                <p class="mt-3 max-w-xs font-serif text-2xl font-semibold leading-tight text-navy-900">Derecho, cultura y vida universitaria</p>
            </div>
        </div>
    @endif

    <div class="flex grow flex-col {{ $horizontal ? 'p-7 md:p-10' : ($featured ? 'p-8' : 'p-6') }}">
        <div class="flex items-center gap-3">
            <span class=" px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $badge }}">{{ $etiqueta }}</span>
            <time datetime="{{ $post->fecha->toDateString() }}" class="text-xs text-stone-500">{{ $post->fecha->translatedFormat('d M Y') }}</time>
        </div>
        <h3 class="mt-3 font-semibold leading-snug text-navy-900 {{ $horizontal ? 'text-2xl md:text-3xl' : ($featured ? 'text-2xl' : 'text-lg') }}">
            <a href="{{ route('blog.show', $post->slug) }}" wire:navigate.hover class="transition hover:text-navy-600">{{ $post->titulo }}</a>
        </h3>
        <p class="mt-3 leading-relaxed text-stone-500 {{ ($featured || $horizontal) ? 'text-[15px]' : 'grow text-sm' }}">{{ $post->extracto }}</p>
        <div class="mt-4 flex items-center justify-between border-t border-stone-100 pt-4 text-xs text-stone-500 {{ $featured ? 'mt-6' : '' }}">
            <span>{{ $post->autor }}</span>
            @if (filled($post->tiempo_lectura))<span>{{ $post->tiempo_lectura }} de lectura</span>@endif
        </div>
        <a href="{{ route('blog.show', $post->slug) }}" wire:navigate.hover class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-navy-700">Leer publicación <x-ui-icon name="arrow-right" /></a>
    </div>
</article>
