@extends('layouts.app')

@section('title', 'Comunicados — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Transparencia" title="Comunicados"
        subtitle="Avisos y comunicados oficiales del Programa de Estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        <div class="space-y-4">
            @foreach ($comunicados as $i => $comunicado)
                <x-reveal :delay="$i * 0.05">
                    <article class="card-hover flex items-start gap-5 rounded-2xl border border-stone-200 bg-white p-6">
                        <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-navy-900 text-white">
                            <span class="text-xl font-semibold leading-none text-white">{{ $comunicado->fecha->format('d') }}</span>
                            <span class="font-sans text-[10px] uppercase">{{ $comunicado->fecha->translatedFormat('M') }}</span>
                        </div>
                        <div class="grow">
                            <h3 class="text-lg font-semibold text-navy-900">{{ $comunicado->titulo }}</h3>
                            <p class="mt-1 font-sans text-sm leading-relaxed text-stone-500">{{ $comunicado->resumen }}</p>
                            <time class="mt-2 block font-sans text-xs text-stone-400">{{ $comunicado->fecha->translatedFormat('d \d\e F, Y') }}</time>
                        </div>
                    </article>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
