@extends('layouts.app')

@section('title', 'Perfil de Ingreso — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Perfil de Ingreso"
        subtitle="Competencias que se espera del estudiante que inicia el programa." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        @php
            $cols = [
                ['Ciencias Sociales', ['Interpreta con pertinencia los hechos históricos a nivel regional, nacional y mundial.', 'Gestiona responsablemente el espacio y el ambiente de su entorno.']],
                ['Comunicación', ['Lee y escribe con pertinencia textos de diverso tipo y complejidad.']],
                ['Matemática', ['Resuelve problemas contextualizados de cantidad con orden y precisión.', 'Resuelve problemas contextualizados de gestión de datos e incertidumbre.']],
                ['Ciencia y Tecnología', ['Explica el mundo físico basándose en conocimientos sobre los seres vivos, la materia, la energía y la biodiversidad.']],
            ];
        @endphp

        <p class="reveal mb-10 max-w-3xl font-sans text-[17px] leading-relaxed text-gray-600">
            El aspirante debe demostrar actitud motivadora y aptitud significativa con claridad para
            cursar estudios en Derecho y Ciencias Políticas, evidenciando las siguientes competencias:
        </p>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
