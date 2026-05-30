@extends('layouts.app')

@section('title', 'Objetivos Educacionales — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Objetivos Educacionales"
        subtitle="Logros que se espera de nuestros egresados, según cada plan de estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $planes = [
                [
                    'titulo' => 'Plan de Estudios 2023',
                    'destacado' => true,
                    'objetivos' => [
                        'Emplea pertinentemente las diversas fuentes del Derecho para emitir opiniones técnicas y absolver consultas jurídicas fundamentadas a las instituciones públicas y privadas, con responsabilidad y eficiencia.',
                        'Aplica la dogmática y las fuentes jurídicas congruentemente para el patrocinio de intereses individuales y colectivos, así como la prevención y solución de conflictos ante órganos jurisdiccionales y no jurisdiccionales, con aptitud destacada y ética profesional.',
                        'Aplica con coherencia y correspondencia argumentativa la dogmática jurídica para su desempeño en el sector público, con transparencia y compromiso ético.',
                        'Evidencia un manejo teórico, metodológico y tecnológico destacado de la investigación jurídica para la solución de problemas en el campo dogmático, empírico e interdisciplinario, cumpliendo los parámetros exigidos por la comunidad jurídica y la ética de la investigación para su posterior publicación.',
                    ],
                ],
                [
                    'titulo' => 'Plan de Estudios 2019',
                    'destacado' => false,
                    'objetivos' => [
                        'Participa en la construcción, reconstrucción e innovación del sistema jurídico peruano en el contexto del derecho comparado, con rigor iusfilosófico y científico jurídico, para confrontar los ordenamientos e instituciones jurídicas existentes en el mundo.',
                        'Aplica las normas jurídicas con eficacia, empleando pertinentemente la doctrina y jurisprudencia nacional y comparada, para resolver los casos de carácter jurídico que se presenten.',
                        'Desarrolla criterios científicos y humanísticos en la aplicación de la ciencia jurídica, proponiendo alternativas de solución pertinentes a la problemática, para atender los casos que se presentan en el ámbito de desempeño laboral.',
                    ],
                ],
            ];
        @endphp

        <div class="space-y-14">
            @foreach ($planes as $plan)
                <div class="reveal">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-semibold text-navy-900">{{ $plan['titulo'] }}</h2>
                        @if ($plan['destacado'])
                            <span class="rounded-full bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                        @endif
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($plan['objetivos'] as $i => $texto)
                            <div class="flex gap-5 rounded-2xl border border-stone-200 bg-white p-6">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-sm font-semibold text-white">OE{{ $i + 1 }}</span>
                                <p class="pt-1 font-sans leading-relaxed text-stone-600">{{ $texto }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
