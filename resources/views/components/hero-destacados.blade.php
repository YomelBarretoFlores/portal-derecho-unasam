@props(['destacados'])

@php
    $items = collect($destacados)->take(3)->values();
@endphp

@if ($items->isNotEmpty())
    {{-- Tira de lo último publicado, al pie del hero.

         En lg caben las tres a la vez: no hay carrusel porque no hay nada que
         esconder. Por debajo se muestra una sola y se pasa con las flechas; la
         numeración editorial 01/02 indica cuántas hay, que es justamente el caso
         en que DESIGN.md admite la cifra dorada: cuando hay secuencia real. --}}
    <div x-data="{ i: 0, n: {{ $items->count() }} }"
         {{-- Banda opaca, no translúcida: el texto de aquí baja a 11px y su
              contraste no puede depender de qué zona de la fotografía haya
              detrás. --}}
         class="relative border-t border-white/15 bg-navy-950">
        <div class="mx-auto flex max-w-7xl items-center gap-6 px-6 py-4">

            <p class="hidden shrink-0 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/45 lg:block">
                Lo último
            </p>

            <ul class="min-w-0 flex-1 lg:grid lg:grid-cols-3 lg:gap-8">
                @foreach ($items as $k => $item)
                    <li @if ($items->count() > 1) x-show="i === {{ $k }}" x-cloak @endif
                        class="min-w-0 lg:!block">
                        <a href="{{ $item->url }}" wire:navigate.hover
                           class="group flex items-baseline gap-3 text-left">
                            <span class="num-editorial num-editorial-claro shrink-0 text-sm leading-none" aria-hidden="true">{{ str_pad((string) ($k + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="min-w-0">
                                <span class="block text-[11px] font-semibold uppercase tracking-[0.12em] text-gold-300">
                                    {{ $item->etiqueta }}
                                    @if ($item->fecha)
                                        <span class="font-normal tracking-normal text-white/45"> · {{ $item->fecha->translatedFormat('d M Y') }}</span>
                                    @endif
                                </span>
                                <span class="mt-1 block truncate text-sm text-white/80 transition-colors group-hover:text-white lg:whitespace-normal lg:[display:-webkit-box] lg:[-webkit-box-orient:vertical] lg:[-webkit-line-clamp:2] lg:overflow-hidden">
                                    {{ $item->titulo }}
                                </span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($items->count() > 1)
                <div class="flex shrink-0 items-center gap-1 lg:hidden">
                    <button type="button" @click="i = (i - 1 + n) % n" aria-label="Anterior"
                            class="border border-white/20 p-2 text-white/70 transition-colors hover:border-white/50 hover:text-white">
                        <x-ui-icon name="chevron-down" class="h-4 w-4 rotate-90" />
                    </button>
                    <button type="button" @click="i = (i + 1) % n" aria-label="Siguiente"
                            class="border border-white/20 p-2 text-white/70 transition-colors hover:border-white/50 hover:text-white">
                        <x-ui-icon name="chevron-down" class="h-4 w-4 -rotate-90" />
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif
