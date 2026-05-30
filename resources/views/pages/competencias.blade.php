@extends('layouts.app')

@section('title', 'Competencias — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Competencias"
        subtitle="Capacidades generales y específicas que desarrolla el programa (Plan 2023)." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        @php
            $generales = [
                'Comunicación efectiva.',
                'Pensamiento crítico.',
                'Trabajo en equipo y liderazgo.',
                'Compromiso social y respeto por la diversidad cultural.',
                'Desempeño orientado a la calidad, transparencia y mejora continua.',
            ];
            $especificas = [
                'Sólida formación jurídica.',
                'Asesora y emite opiniones legales con responsabilidad y ética.',
                'Aplica criterialmente las fuentes del Derecho.',
                'Patrocina intereses individuales y colectivos, y resuelve conflictos.',
                'Investigación jurídica con manejo metodológico y tecnológico.',
            ];
        @endphp

        <div class="grid gap-12 lg:grid-cols-2">
            @foreach ([['Competencias generales', $generales], ['Competencias específicas', $especificas]] as [$titulo, $items])
                <x-reveal>
                    <h2 class="text-2xl font-semibold text-navy-900">{{ $titulo }}</h2>
                    <ol class="mt-6 space-y-4">
                        @foreach ($items as $i => $item)
                            <li class="flex gap-4">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-navy-900 font-sans text-sm font-bold text-gold-400">{{ $i + 1 }}</span>
                                <p class="pt-1.5 font-sans leading-relaxed text-gray-600">{{ $item }}</p>
                            </li>
                        @endforeach
                    </ol>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
