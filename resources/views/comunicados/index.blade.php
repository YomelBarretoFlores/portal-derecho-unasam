@extends('layouts.app')

@section('title', 'Comunicados — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Transparencia" title="Comunicados"
        subtitle="Avisos y comunicados oficiales del Programa de Estudios." />

    <section class="mx-auto max-w-6xl px-6 py-20">
        @if ($comunicados->isEmpty())
            <div class="mx-auto max-w-2xl rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-16 text-center">
                <p class="font-sans text-stone-500">Aún no hay comunicados publicados.</p>
            </div>
        @else
            <div class="stagger-children grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($comunicados as $comunicado)
                    <article class="reveal card-hover flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white">
                        {{-- Media con número --}}
                        <div class="relative aspect-[3/2] overflow-hidden">
                            @if ($comunicado->_imagen_url)
                                <img src="{{ $comunicado->_imagen_url }}" alt="{{ $comunicado->titulo }}"
                                     class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-navy-800 to-navy-950">
                                    <svg class="h-12 w-12 text-white/20" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 flex h-10 w-10 items-center justify-center rounded-lg bg-navy-900 font-sans text-sm font-semibold text-white shadow-card">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>

                        {{-- Cuerpo --}}
                        <div class="flex grow flex-col p-6">
                            <h3 class="text-lg font-semibold leading-snug text-navy-900">{{ $comunicado->titulo }}</h3>
                            @if ($comunicado->resumen)
                                <p class="mt-2 line-clamp-2 grow font-sans text-sm leading-relaxed text-stone-500">{{ $comunicado->resumen }}</p>
                            @endif
                            @if ($comunicado->fecha)
                                <time class="mt-4 block font-sans text-xs text-stone-400">{{ $comunicado->fecha->translatedFormat('d \d\e F, Y') }}</time>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
