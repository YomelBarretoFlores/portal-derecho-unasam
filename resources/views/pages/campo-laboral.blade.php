@extends('layouts.app')

@section('title', 'Campo Laboral — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Campo Laboral"
        subtitle="Las áreas profesionales donde se desempeñan nuestros egresados." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($areas as $i => $area)
                <x-reveal :delay="$i * 0.05">
                    <div class="card-hover h-full rounded-2xl border border-stone-200 bg-white p-6">
                        <span class="text-2xl font-bold text-stone-300">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-2 text-lg font-semibold text-navy-900">{{ $area->titulo }}</h3>
                        <p class="mt-2 font-sans text-sm leading-relaxed text-stone-500">{{ $area->descripcion }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
