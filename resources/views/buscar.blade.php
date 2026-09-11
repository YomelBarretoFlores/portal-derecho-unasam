@extends('layouts.app')

@section('title', ($q !== '' ? 'Buscar: '.$q : 'Buscar').' — Derecho UNASAM')
@section('robots', 'noindex, follow')

@section('content')
    <x-page-hero seccion="Portal" title="Buscar en el portal"
        subtitle="Páginas institucionales, comunicados, plana docente, documentos normativos y la revista Derecho y Cultura." />

    <section class="mx-auto max-w-4xl px-6 py-14 md:py-16">
        <form method="get" action="{{ route('buscar') }}" role="search" class="flex flex-col gap-3 sm:flex-row">
            <label class="sr-only" for="q">Términos de búsqueda</label>
            {{-- autofocus solo cuando no hay consulta.

                 Con autofocus incondicional, al pulsar Enter la página recargaba
                 y el navegador devolvía el foco al campo dejando el cursor ANTES
                 del texto ya escrito: para corregir la búsqueda había que pulsar
                 Fin. Además, robar el foco tras enviar aparta la atención de los
                 resultados, que es lo que se acaba de pedir; con lector de
                 pantalla el efecto es peor, porque el foco vuelve al formulario
                 en vez de quedar donde está la respuesta. --}}
            <input id="q" name="q" type="search" value="{{ $q }}" maxlength="120"
                   @if ($q === '') autofocus @endif
                   placeholder="Plan de estudios, normas para autores, un docente…"
                   class="min-w-0 flex-1 border border-stone-300 px-4 py-3 text-base focus:border-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-700/10">
            <button type="submit" class="btn btn-primary shrink-0">Buscar</button>
        </form>

        @if ($q === '')
            <p class="mt-10 leading-relaxed text-stone-600">
                Escribe lo que busques. La búsqueda no distingue mayúsculas ni tildes.
            </p>
        @elseif ($resultados->isEmpty())
            {{-- La mascota acompaña el vacío, no lo decora: una búsqueda sin
                 resultados es el momento más seco del portal, y es además el
                 único sitio de esta página donde no hay contenido con el que
                 competir. Decorativa y sin animar: aquí el visitante busca
                 algo, no quiere que le llamen la atención. --}}
            <div class="mt-10 flex items-center gap-8 border border-stone-200 bg-paper p-8">
                <div class="min-w-0">
                    <p class="font-serif text-2xl text-navy-900">Sin resultados para «{{ $q }}»</p>
                    <p class="mt-3 leading-relaxed text-stone-600">
                        Pruebe con menos palabras o con otras. También puede recorrer el portal desde el menú.
                    </p>
                </div>
                <img src="{{ asset('img/mascota-leyendo.webp') }}" alt="" aria-hidden="true"
                     width="640" height="735" loading="lazy" decoding="async"
                     class="hidden w-28 shrink-0 select-none sm:block lg:w-36">
            </div>
        @else
            <p class="mt-10 text-sm text-stone-500">
                {{ trans_choice('{1}:count resultado|[2,*]:count resultados', $resultados->count(), ['count' => $resultados->count()]) }}
                para «{{ $q }}»
            </p>

            <ol class="mt-6 border-t border-stone-200">
                @foreach ($resultados as $resultado)
                    <li class="border-b border-stone-200">
                        <a href="{{ $resultado->url }}" wire:navigate.hover class="group block py-6 transition-colors hover:bg-paper">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-navy-700">{{ $resultado->tipo }}</p>
                            <h2 class="mt-1.5 text-xl font-semibold leading-snug text-navy-900">{{ $resultado->titulo }}</h2>
                            @if (filled($resultado->extracto))
                                <p class="mt-1.5 text-sm leading-relaxed text-stone-600">{{ $resultado->extracto }}</p>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
@endsection
