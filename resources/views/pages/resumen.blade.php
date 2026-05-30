@extends('layouts.app')

@section('title', 'Resumen del Programa — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Resumen del Programa de Estudios"
        subtitle="Programa de Estudio de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        <div class="reveal max-w-3xl">
            <div class="accent-line"></div>
            <h2 class="mt-5 text-3xl font-semibold text-navy-900">Currículo flexible y por competencias</h2>

            <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                <p>
                    El Plan de Estudios, como parte del currículo universitario, se caracteriza por ser
                    flexible: nos permite modificaciones en función de la diversidad humana y social, y de
                    las particularidades, necesidades e intereses de los estudiantes de acuerdo a su
                    contexto en particular.
                </p>
                <p>
                    El modelo curricular que orienta la actualización está basado en el <strong>enfoque por
                    competencias</strong> —generales y específicas— con asignaturas generales, específicas y
                    de especialidad. Asimismo, se ha considerado la flexibilidad curricular mediante cursos
                    electivos.
                </p>
            </div>
        </div>

        {{-- Marco legal --}}
        <div class="reveal mt-10 space-y-5" data-reveal-delay="0.1">
            <blockquote class="rounded-2xl border-l-4 border-navy-300 bg-paper p-6">
                <p class="font-sans text-[15px] leading-relaxed text-stone-600">
                    <span class="font-semibold text-navy-900">Ley Universitaria N.º 30220, artículo 40.º (Diseño Curricular):</span>
                    «Cada universidad determina el diseño curricular de cada especialidad, en los niveles de
                    enseñanza respectivos, de acuerdo a las necesidades nacionales y regionales que
                    contribuyan al desarrollo del país. […] Cada universidad determina en la estructura
                    curricular el nivel de estudios de pregrado, la pertinencia y duración de las prácticas
                    preprofesionales, de acuerdo a sus especialidades. […] El currículo se debe actualizar
                    cada tres (3) años o cuando sea conveniente, según los avances científicos y
                    tecnológicos.» <span class="italic">(Énfasis nuestro.)</span>
                </p>
            </blockquote>

            <blockquote class="rounded-2xl border-l-4 border-navy-700 bg-paper p-6">
                <p class="font-sans text-[15px] leading-relaxed text-stone-600">
                    <span class="font-semibold text-navy-900">Artículo 79.º — De la Actualización del Currículo (2015):</span>
                    el currículo de cada carrera profesional se debe actualizar cada tres (03) años, según los
                    avances científicos y tecnológicos o cuando resulte necesario y/o conveniente. El
                    desarrollo curricular debe ser evaluado cada año por la Comisión respectiva. Los
                    estudiantes inician y terminan con un currículo único.
                </p>
            </blockquote>
        </div>

        <p class="reveal mt-8 max-w-3xl font-sans text-[17px] leading-relaxed text-stone-600" data-reveal-delay="0.15">
            La Ley Universitaria N.º 30220 del Perú, en su artículo 40, establece la importancia de la
            evaluación y actualización continua de los planes de estudio, lo que es fundamental para
            asegurar la calidad educativa y la pertinencia de la formación profesional.
        </p>
    </section>
@endsection
