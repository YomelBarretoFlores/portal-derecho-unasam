@extends('layouts.app')

@section('title', 'Campo Laboral — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Campo Laboral"
        subtitle="Las áreas profesionales donde se desempeñan nuestros egresados." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        @php
            $areas = [
                ['Ejercicio libre de la abogacía', 'Representación y defensa de personas naturales o jurídicas ante tribunales de justicia, autoridades policiales y administrativas.'],
                ['Magistratura y Ministerio Público', 'Desempeño como jueces o fiscales en el sistema judicial peruano.'],
                ['Asesoría legal', 'Consultoría jurídica para entidades públicas y privadas, incluyendo la participación en equipos multidisciplinarios.'],
                ['Docencia e investigación', 'Participación en la enseñanza universitaria y en proyectos de investigación jurídica.'],
                ['Participación política', 'Desempeño en funciones políticas, especialmente en la elaboración e interpretación de leyes conforme a principios democráticos.'],
                ['Resolución de conflictos', 'Intervención en la solución de conflictos individuales y colectivos, promoviendo la paz social y la equidad.'],
            ];
        @endphp

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($areas as $i => [$titulo, $desc])
                <x-reveal :delay="$i * 0.05">
                    <div class="card-hover h-full rounded-2xl border border-stone-200 bg-white p-6">
                        <span class="text-2xl font-bold text-stone-300">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-2 text-lg font-semibold text-navy-900">{{ $titulo }}</h3>
                        <p class="mt-2 font-sans text-sm leading-relaxed text-stone-500">{{ $desc }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
