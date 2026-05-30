@extends('layouts.app')

@section('title', 'Perfil de Egreso — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Perfil de Egreso"
        subtitle="El profesional que forma el Programa de Estudios de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $planes = [
                [
                    'titulo' => 'Perfil de Egreso 2023',
                    'destacado' => true,
                    'parrafos' => [
                        'El egresado de la carrera de Derecho es un profesional con formación jurídica sólida, capaz de asesorar y emitir opiniones legales con responsabilidad y ética, aplicando con criterio las fuentes del Derecho en la resolución de conflictos y en el patrocinio de intereses individuales y colectivos ante diversas instancias.',
                        'Cuenta con competencias en investigación jurídica, comunicación efectiva, trabajo en equipo y pensamiento crítico, actuando con liderazgo, compromiso social y respeto por la diversidad cultural. Su desempeño profesional se orienta a la calidad, la transparencia y la mejora continua, contribuyendo al desarrollo de una sociedad más justa y democrática.',
                    ],
                ],
                [
                    'titulo' => 'Perfil de Egreso 2019',
                    'destacado' => false,
                    'parrafos' => [
                        'El egresado de la carrera de Derecho es un profesional con sólida formación jurídica, ética y humanista, capaz de asesorar, defender y tomar decisiones fundamentadas en el marco del Estado Constitucional de Derecho.',
                        'Posee habilidades de comunicación, investigación, argumentación y resolución de conflictos jurídicos, actuando con responsabilidad, liderazgo y compromiso social. Valora la diversidad cultural, promueve la justicia y contribuye al bien común con una visión crítica, innovadora y orientada a la calidad.',
                    ],
                ],
            ];
        @endphp

        <div class="space-y-8">
            @foreach ($planes as $plan)
                <x-reveal>
                    <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-10">
                        <div class="flex items-center gap-3">
                            <div class="accent-line"></div>
                            <h2 class="text-2xl font-semibold text-navy-900">{{ $plan['titulo'] }}</h2>
                            @if ($plan['destacado'])
                                <span class="rounded-full bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                            @endif
                        </div>
                        <div class="mt-5 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                            @foreach ($plan['parrafos'] as $p)
                                <p>{{ $p }}</p>
                            @endforeach
                        </div>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
