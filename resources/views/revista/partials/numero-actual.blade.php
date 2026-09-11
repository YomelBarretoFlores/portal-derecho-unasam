@props(['actual'])

{{-- El número en curso encabeza la portada de la revista.

     Antes la colección vivía al final de la página, bajo «Números publicados»,
     detrás de la presentación, la ficha institucional y el enfoque. En una
     publicación seria el número ES la noticia: Harvard Law Review abre con
     «Volumen 139 · Número 8 · Junio 2026» y la tabla de contenidos, y Derecho
     PUCP pone «Número actual» arriba del todo. Quien llega a la portada de una
     revista viene a ver qué se acaba de publicar. --}}
@php
    // Esta vista se sirve tanto desde la caché pública —que entrega stdClass—
    // como desde la vista previa del panel, que entrega modelos Eloquent. La
    // portada y la lista de artículos se resuelven distinto en cada caso.
    $portadaActual = $actual?->_portada_url
        ?? ($actual instanceof \App\Models\RevistaNumero ? $actual->portada_url : '');
    $articulosActual = collect($actual?->articulos ?? []);
@endphp

@if ($actual)
    <section class="border-b border-stone-200 bg-paper">
        <div class="mx-auto max-w-7xl px-6 py-14 md:py-16">
            {{-- La columna de portada solo se reserva cuando hay portada: sin
                 imagen dejaba dieciséis rems en blanco a la izquierda del número. --}}
            <div @class(['grid gap-10 lg:gap-16', 'lg:grid-cols-[16rem_minmax(0,1fr)]' => (bool) $portadaActual])>

                @if ($portadaActual)
                    <div>
                        <img src="{{ $portadaActual }}" alt="Portada de {{ $actual->titulo }}"
                             class="w-full border border-stone-200 object-cover" style="aspect-ratio: 4/5">
                        <a href="{{ route('revista.numero', $actual->slug) }}" wire:navigate.hover
                           class="btn btn-primary mt-5 w-full justify-center">Ver el número completo</a>
                    </div>
                @endif

                <div>
                    <p class="eyebrow">Número actual</p>
                    <p class="mt-3 font-serif text-lg text-navy-800">
                        Vol. {{ $actual->volumen }} · Núm. {{ $actual->numero }}@if ($actual->fecha_publicacion)
                            <span class="text-stone-500"> · {{ $actual->fecha_publicacion->translatedFormat('F \d\e Y') }}</span>
                        @endif
                    </p>
                    <h2 class="mt-2 text-3xl leading-tight md:text-4xl">
                        <a href="{{ route('revista.numero', $actual->slug) }}" wire:navigate.hover>{{ $actual->titulo }}</a>
                    </h2>
                    @if ($actual->descripcion)
                        <p class="mt-4 max-w-2xl leading-relaxed text-stone-600">{{ $actual->descripcion }}</p>
                    @endif

                    @if ($articulosActual->isNotEmpty())
                        <div class="mt-9 border-t border-stone-300 pt-7">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-navy-700">
                                Tabla de contenidos
                            </p>
                            <ol class="mt-5">
                                @foreach ($articulosActual as $i => $articulo)
                                    <li class="flex gap-5 border-b border-stone-200 py-4 last:border-b-0">
                                        <span class="num-editorial shrink-0 pt-1 text-sm leading-none" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <div class="min-w-0">
                                            @if ($articulo->categoria)
                                                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-navy-600">{{ $articulo->categoria }}</p>
                                            @endif
                                            <h3 class="mt-1 text-lg font-semibold leading-snug text-navy-900">
                                                <a href="{{ route('revista.articulo', [$actual->slug, $articulo->slug]) }}" wire:navigate.hover>{{ $articulo->titulo }}</a>
                                            </h3>
                                            <p class="mt-1 text-sm text-stone-500">
                                                {{ implode(' · ', $articulo->autores) }}@if ($articulo->paginas)<span class="text-stone-500"> — pp. {{ $articulo->paginas }}</span>@endif
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    @unless ($portadaActual)
                        <a href="{{ route('revista.numero', $actual->slug) }}" wire:navigate.hover
                           class="btn btn-primary mt-9">Ver el número completo</a>
                    @endunless
                </div>
            </div>
        </div>
    </section>
@endif
