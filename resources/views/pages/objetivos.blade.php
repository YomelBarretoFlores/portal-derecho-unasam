@extends('layouts.app')

@section('title', 'Objetivos Educacionales — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Objetivos Educacionales"
        subtitle="Logros que se espera de nuestros egresados (Plan de Estudios 2023)." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $objetivos = [
                ['OE1', 'Emplea pertinentemente las diversas fuentes del Derecho para emitir opiniones técnicas y absolver consultas jurídicas fundamentadas a las instituciones públicas y privadas.'],
                ['OE2', 'Aplica la dogmática y fuentes jurídicas congruentemente para el patrocinio de intereses individuales y colectivos, así como la prevención de conflictos.'],
                ['OE3', 'Aplica con coherencia y correspondencia argumentativa la dogmática jurídica para su desempeño en el sector público con transparencia y compromiso ético.'],
                ['OE4', 'Evidencia manejo teórico, metodológico y tecnológico destacado de la investigación jurídica para la solución de problemas en el campo dogmático y empírico.'],
            ];
        @endphp

        <div class="space-y-5">
            @foreach ($objetivos as $i => [$codigo, $texto])
                <x-reveal :delay="$i * 0.06">
                    <div class="flex gap-5 rounded-2xl border border-gray-200 bg-white p-6">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-navy-900 font-sans text-sm font-bold text-gold-400">{{ $codigo }}</span>
                        <p class="pt-1 font-sans leading-relaxed text-gray-600">{{ $texto }}</p>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
