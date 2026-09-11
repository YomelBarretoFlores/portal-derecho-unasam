@extends('layouts.app')

@section('title', 'Campo Laboral — Derecho UNASAM')
@section('description', 'Campo laboral del abogado UNASAM: las áreas profesionales donde se desempeñan los egresados del Programa de Derecho y Ciencias Políticas.')

@section('content')
    <x-page-hero seccion="Programa" title="Campo Laboral"
        subtitle="Las áreas profesionales donde se desempeñan nuestros egresados." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        @if (collect($areas)->isEmpty())
            <x-empty-state title="Campo laboral" description="Las áreas de desempeño profesional se publicarán próximamente." />
        @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($areas as $i => $area)
                <x-reveal :delay="$i * 0.05">
                    <div class="card-hover h-full rounded-2xl border border-stone-200 bg-white p-6">
                        <h3 class="text-lg font-semibold text-navy-900">{{ $area->titulo }}</h3>
                        <p class="mt-2 font-sans text-sm leading-relaxed text-stone-500">{{ $area->descripcion }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        @endif
    </section>
@endsection
