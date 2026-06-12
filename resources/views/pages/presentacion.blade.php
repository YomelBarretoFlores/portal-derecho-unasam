@extends('layouts.app')

@section('title', 'Presentación — Derecho UNASAM')
@section('description', 'Conoce el Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM, en Huaraz, Áncash.')

@section('content')
    <x-page-hero
        seccion="Programa"
        title="Presentación"
        subtitle="Conoce el Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-16 lg:grid-cols-[1fr_360px]">
            {{-- Texto --}}
            <div class="reveal max-w-2xl">
                <div class="accent-line"></div>
                <h2 class="mt-5 text-3xl font-semibold text-navy-900">{{ $titulo ?: 'Bienvenido al Programa de Estudio de Derecho y Ciencias Políticas' }}</h2>
                <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                    @if (filled($cuerpo))
                        @foreach (preg_split('/\R{2,}/', trim($cuerpo)) as $parrafo)
                            <p>{{ $parrafo }}</p>
                        @endforeach
                    @else
                        <p>Esta carrera forma al profesional liberal competente y capaz de aportar a la sociedad los componentes sociales de justicia, equidad, estado de derecho y paz social, apelando a su actuación como científico social que utiliza tecnología actualizada y humanísticamente pertinente.</p>
                        <p>El abogado forjado en la UNASAM se caracteriza por sus rasgos conductuales coherentes con su formación académica y doctrinaria en función de la calidad de vida justa de todos los ciudadanos.</p>
                        <p>La Facultad de Derecho y Ciencias Políticas ha cumplido sus «Bodas de Perla» precisamente el año 2016, son 30 años de vida institucional que ha significado evaluar muchos logros en nuestro desarrollo social y físico, habiendo mantenido un estándar de crecimiento en su población estudiantil, dada la gran demanda que existe por la carrera de abogacía en nuestro medio.</p>
                    @endif
                </div>
            </div>

            {{-- Sidebar datos --}}
            <aside class="reveal" data-reveal-delay="0.12">
                <div class="rounded-2xl border border-stone-200 bg-paper p-7">
                    <h3 class="text-lg font-semibold text-navy-900">Datos del programa</h3>
                    <dl class="mt-5 space-y-4 font-sans text-sm">
                        @foreach ($datos as [$k, $v])
                            <div class="flex justify-between gap-4 border-b border-stone-200 pb-3">
                                <dt class="text-stone-500">{{ $k }}</dt>
                                <dd class="text-right font-medium text-navy-900">{{ $v }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </aside>
        </div>
    </section>
@endsection
