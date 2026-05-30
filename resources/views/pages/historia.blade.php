@extends('layouts.app')

@section('title', 'Historia — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Nuestra historia"
        subtitle="Cuatro décadas formando abogados al servicio de Áncash y el Perú." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $hitos = [
                ['1986', 'Fundación de la Facultad', 'Se crea la Facultad de Derecho y Ciencias Políticas de la UNASAM, iniciando la formación jurídica universitaria en la región Áncash.'],
                ['2016', 'Bodas de Perla', 'La Facultad cumple 30 años de vida institucional, con muchos logros en su desarrollo social y físico, y un estándar de crecimiento sostenido en su población estudiantil dada la gran demanda por la carrera de abogacía.'],
                ['2019', 'Plan de Estudios 2019', 'Se implementa una malla curricular actualizada con objetivos educacionales orientados al sistema jurídico y la investigación.'],
                ['2023', 'Plan de Estudios 2023', 'Renovación curricular por competencias, con énfasis en las fuentes del Derecho, el patrocinio de intereses y la investigación jurídica con manejo metodológico y tecnológico.'],
            ];
        @endphp

        <div class="relative border-l-2 border-gray-200 pl-8">
            @foreach ($hitos as $i => [$anio, $titulo, $desc])
                <x-reveal :delay="$i * 0.06" class="relative mb-12 last:mb-0">
                    <span class="absolute -left-[42px] flex h-6 w-6 items-center justify-center rounded-full bg-gold-500 ring-4 ring-white"></span>
                    <span class="font-serif text-3xl font-bold text-gold-500">{{ $anio }}</span>
                    <h3 class="mt-1 text-xl font-semibold text-navy-900">{{ $titulo }}</h3>
                    <p class="mt-2 font-sans leading-relaxed text-gray-600">{{ $desc }}</p>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
