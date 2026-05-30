@extends('layouts.app')

@section('title', 'Historia — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Historia"
        subtitle="Casi cuatro décadas formando abogados al servicio de Áncash y el Perú." />

    <section class="mx-auto max-w-4xl px-6 py-20">

        {{-- Línea de tiempo de hitos institucionales (resoluciones) --}}
        @php
            $hitos = [
                ['1986', 'Creación de la Escuela', 'Mediante R.R. N.º 438-86-UNASAM (1 de setiembre de 1986) se crea la Facultad de Letras, con la Escuela de Formación Profesional de Derecho y Ciencias Políticas.'],
                ['1993', 'Categoría de Facultad', 'Por R.R. N.º 594-93-UNASAM (11 de noviembre de 1993) se suprime la Facultad de Letras y se eleva la Escuela de Derecho y Ciencias Políticas a la categoría de Facultad.'],
                ['2015', 'Nuevo Estatuto', 'La Resolución N.º 001-2015-AU-UNASAM (22 de enero de 2015) aprueba el nuevo Estatuto, que reconoce la Facultad y la Escuela Profesional de Derecho y Ciencias Políticas.'],
                ['2017', 'Modificación del Estatuto', 'Por R. de Asamblea Universitaria N.º 051-2017-UNASAM (20 de setiembre de 2017) se modifica el art. 28.º: «Escuela Profesional de Derecho».'],
                ['2018', 'Creación de la Carrera', 'La R. de Asamblea Universitaria N.º 007-2018-UNASAM (12 de setiembre de 2018) aprueba, con efecto anticipado al 1 de setiembre de 1986, la creación de la Carrera Profesional de Derecho y Ciencias Políticas.'],
                ['2019', 'Plan de Estudios 2019', 'Entra en vigencia el plan curricular 2019, en proceso de actualización durante el año 2024.'],
                ['2023', 'Plan de Estudios 2023', 'Entra en vigencia el nuevo plan de estudios de la carrera, basado en un enfoque por competencias.'],
            ];
        @endphp

        <div class="relative border-l-2 border-stone-200 pl-8">
            @foreach ($hitos as $i => [$anio, $titulo, $desc])
                <x-reveal :delay="$i * 0.05" class="relative mb-10 last:mb-0">
                    <span class="absolute -left-[42px] flex h-6 w-6 items-center justify-center rounded-full bg-navy-600 ring-4 ring-white"></span>
                    <span class="text-3xl font-bold text-navy-600">{{ $anio }}</span>
                    <h3 class="mt-1 text-xl font-semibold text-navy-900">{{ $titulo }}</h3>
                    <p class="mt-2 font-sans leading-relaxed text-stone-600">{{ $desc }}</p>
                </x-reveal>
            @endforeach
        </div>

        {{-- Reseña descriptiva --}}
        <div class="reveal mt-16 max-w-3xl border-t border-stone-200 pt-12">
            <div class="accent-line"></div>
            <h2 class="mt-5 text-2xl font-semibold text-navy-900">Nuestra trayectoria</h2>
            <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                <p>
                    La Escuela Profesional de Derecho y Ciencias Políticas responde a las necesidades y
                    expectativas de la sociedad. Está representada por su director de Escuela y cuenta con una
                    plana docente calificada, adscrita a los Departamentos Académicos de Derecho y Ciencias
                    Políticas.
                </p>
                <p>
                    Desde su creación, la Escuela estuvo orientada a ofrecer estudios presenciales, con un
                    enfoque integral que busca no solo impartir conocimientos técnicos, sino también formar
                    abogados comprometidos con los valores éticos y sociales.
                </p>
                <p>
                    Su misión ha sido preparar profesionales capaces de afrontar los retos de la modernidad,
                    adaptándose a las cambiantes demandas del entorno jurídico y social, promoviendo soluciones
                    innovadoras a los problemas contemporáneos. Además, se ha centrado en cerrar las brechas de
                    justicia, fomentando en sus egresados un fuerte sentido de responsabilidad social para
                    contribuir a la equidad y al acceso a la justicia para todos los sectores de la sociedad.
                </p>
                <p>
                    Actualmente se vienen implementando la biblioteca automatizada, el centro de conciliación y
                    arbitraje, el consultorio jurídico gratuito y otros escenarios educativos que promueven el
                    aprendizaje significativo. A la fecha, el programa cuenta con dos planes curriculares: el
                    Plan 2019 vigente —en actualización durante 2024— y el Plan 2023, basado en un enfoque por
                    competencias.
                </p>
            </div>
        </div>
    </section>
@endsection
