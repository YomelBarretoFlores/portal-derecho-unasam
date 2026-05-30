@extends('layouts.app')

@section('title', 'Perfil de Egreso — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Perfil de Egreso"
        subtitle="Competencias del abogado que forma el programa (Plan de Estudios 2023)." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        @php
            $cols = [
                ['Formación jurídica', ['Sólida formación jurídica.', 'Asesora y emite opiniones legales con responsabilidad y ética.', 'Aplica criterialmente las fuentes del Derecho.', 'Patrocina intereses individuales y colectivos.', 'Resuelve conflictos.']],
                ['Investigación y desempeño', ['Competencias en investigación jurídica.', 'Comunicación efectiva.', 'Pensamiento crítico.', 'Desempeño orientado a la calidad, transparencia y mejora continua.']],
                ['Valores y liderazgo', ['Trabajo en equipo.', 'Liderazgo.', 'Compromiso social.', 'Respeto por la diversidad cultural.']],
            ];
        @endphp

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($cols as $i => [$titulo, $items])
                <x-reveal :delay="$i * 0.08">
                    <div class="h-full rounded-2xl border border-gray-200 bg-white p-7">
                        <div class="h-0.5 w-10 bg-gold-500"></div>
                        <h3 class="mt-4 text-xl font-semibold text-navy-900">{{ $titulo }}</h3>
                        <ul class="mt-4 space-y-3 font-sans text-sm text-gray-600">
                            @foreach ($items as $item)
                                <li class="flex gap-2.5">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
