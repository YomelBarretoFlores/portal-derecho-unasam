@extends('layouts.app')

@section('title', 'Competencias — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Competencias"
        subtitle="Capacidades generales y específicas que desarrolla el Programa de Estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $grupos = [
                [
                    'grupo' => 'Competencias Generales',
                    'prefijo' => 'CG',
                    'planes' => [
                        [
                            'titulo' => 'Plan de Estudios 2023',
                            'destacado' => true,
                            'items' => [
                                ['texto' => 'Demuestra capacidad comunicativa en situaciones y temáticas académico-cotidianas, empleando procedimientos de análisis y síntesis en la gestión de la información y la resolución de problemas contextualizados con rigor científico.'],
                                ['texto' => 'Demuestra liderazgo y responsabilidad en el trabajo en equipo, y habilidades intra e interpersonales para generar soluciones con creatividad y visión de futuro, mediante la práctica de valores morales.'],
                                ['texto' => 'Demuestra compromiso con la calidad y su mejora continua, con responsabilidad socioambiental, respetando la diversidad social y cultural con sentido crítico y reflexivo.'],
                            ],
                        ],
                        [
                            'titulo' => 'Plan de Estudios 2019',
                            'destacado' => false,
                            'items' => [
                                ['texto' => 'Demuestra capacidad comunicativa adecuada y eficaz en situaciones y temáticas académico-cotidianas, y de análisis y síntesis en la gestión responsable y eficiente de la información orientada a la investigación.'],
                                ['texto' => 'Toma decisiones con rigor científico al plantear y resolver problemas en situaciones jurídicas reales.'],
                                ['texto' => 'Demuestra liderazgo y responsabilidad en el trabajo en equipo, para generar soluciones con creatividad, innovación y visión de futuro, ante situaciones y problemas del contexto de forma sistémica.'],
                                ['texto' => 'Demuestra habilidades intra e interpersonales con actitud crítica, basada en la práctica de valores morales, en el desarrollo personal y el ejercicio de la profesión.'],
                                ['texto' => 'Demuestra compromiso con la calidad y su mejora permanente, con responsabilidad social y ambiental.'],
                                ['texto' => 'Desarrolla actitudes interculturales valorando la diversidad social y cultural del entorno local, regional, nacional y mundial, con sentido crítico y reflexivo.'],
                            ],
                        ],
                    ],
                ],
                [
                    'grupo' => 'Competencias Específicas',
                    'prefijo' => 'CE',
                    'planes' => [
                        [
                            'titulo' => 'Plan de Estudios 2023',
                            'destacado' => true,
                            'items' => [
                                ['nombre' => 'Asesoría y consultoría', 'texto' => 'Emplea las fuentes del Derecho para emitir opiniones técnicas y absolver consultas jurídicas a las instituciones públicas y privadas con responsabilidad.'],
                                ['nombre' => 'Patrocinio y resolución de conflictos', 'texto' => 'Aplica la dogmática y las diversas fuentes jurídicas para el patrocinio de intereses individuales y colectivos, así como la prevención de los conflictos y la solución de los mismos ante órganos jurisdiccionales y no jurisdiccionales, con empeño y ética profesional.'],
                                ['nombre' => 'Desempeño en la función pública', 'texto' => 'Aplica con coherencia argumentativa la dogmática jurídica para su desempeño en el sector público, con transparencia y compromiso ético.'],
                                ['nombre' => 'Investigación jurídica', 'texto' => 'Evidencia un manejo teórico, metodológico y tecnológico básico de la investigación jurídica para la solución de problemas en el campo dogmático, empírico e interdisciplinario, cumpliendo los parámetros exigidos por la comunidad jurídica y la ética de la investigación para su posterior publicación.'],
                            ],
                        ],
                        [
                            'titulo' => 'Plan de Estudios 2019',
                            'destacado' => false,
                            'items' => [
                                ['texto' => 'Emplea con pertinencia la dogmática, la jurisprudencia y la norma jurídica en la asesoría y consultoría de las distintas instituciones públicas y privadas, con transparencia y responsabilidad, conforme exige un servicio profesional de calidad.'],
                                ['texto' => 'Maneja con capacidad argumentativa los fundamentos doctrinarios, jurisprudenciales y normativos del derecho sustantivo para la defensa de casos ante el órgano pre jurisdiccional, jurisdiccional y no jurisdiccional, acorde a las exigencias del Estado Constitucional de Derecho.'],
                                ['texto' => 'Emplea coherentemente los fundamentos doctrinarios y jurisprudenciales, así como los procedimientos que regula el derecho procesal, para resolver los conflictos de interés e incertidumbre jurídica en todos los ámbitos del derecho, conforme exige el logro del bien común y la justicia.'],
                                ['texto' => 'Aplica las doctrinas, teorías, principios lógico-jurídicos y normatividad con pertinencia argumentativa en la toma de sus decisiones funcionariales, asumiendo su compromiso responsable acorde a la vigencia del Estado Constitucional de Derecho.'],
                                ['texto' => 'Manifiesta capacidad básica en la comprensión de los fundamentos teóricos y el manejo metodológico de la investigación jurídica, valorando su utilidad para la solución de problemas jurídicos y respetando la ética de la investigación.'],
                            ],
                        ],
                    ],
                ],
            ];
        @endphp

        <div class="space-y-16">
            @foreach ($grupos as $grupo)
                <div>
                    <div class="reveal">
                        <div class="accent-line"></div>
                        <h2 class="mt-4 text-2xl font-semibold text-navy-900">{{ $grupo['grupo'] }}</h2>
                    </div>

                    <div class="mt-8 space-y-12">
                        @foreach ($grupo['planes'] as $plan)
                            <div class="reveal">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-navy-700">{{ $plan['titulo'] }}</h3>
                                    @if ($plan['destacado'])
                                        <span class="rounded-full bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                                    @endif
                                </div>

                                <div class="mt-5 space-y-4">
                                    @foreach ($plan['items'] as $i => $item)
                                        <div class="flex gap-5 rounded-2xl border border-stone-200 bg-white p-6">
                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-xs font-semibold text-white">{{ $grupo['prefijo'] }}{{ $i + 1 }}</span>
                                            <div class="pt-0.5">
                                                @isset($item['nombre'])
                                                    <p class="font-semibold text-navy-900">{{ $item['nombre'] }}</p>
                                                @endisset
                                                <p class="@isset($item['nombre']) mt-1 @endisset font-sans leading-relaxed text-stone-600">{{ $item['texto'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
