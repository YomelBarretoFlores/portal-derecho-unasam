@extends('layouts.app')

@section('title', 'Misión y Visión — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Misión y Visión"
        subtitle="El propósito que nos guía y el horizonte al que aspiramos." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Misión (dark) --}}
            <x-reveal>
                <div class="flex h-full flex-col rounded-3xl bg-navy-900 p-10 text-white">
                    <span class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-gold-400">Misión</span>
                    <div class="mt-4 h-0.5 w-10 bg-gold-500"></div>
                    <p class="mt-6 font-serif text-2xl leading-relaxed">
                        Formar abogados líderes, con sólida formación científica, humanística, jurídica,
                        ética e inclusiva, comprometidos con la investigación, la defensa de los derechos
                        humanos, la justicia y el desarrollo sostenible, capaces de responder con
                        responsabilidad a los desafíos de la realidad social y profesional, tanto a nivel
                        local como global.
                    </p>
                </div>
            </x-reveal>

            {{-- Visión (light) --}}
            <x-reveal :delay="0.12">
                <div class="flex h-full flex-col rounded-3xl border border-gray-200 bg-gray-50 p-10">
                    <span class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-gold-600">Visión</span>
                    <div class="mt-4 h-0.5 w-10 bg-gold-500"></div>
                    <p class="mt-6 font-serif text-2xl leading-relaxed text-navy-900">
                        Consolidarse como un programa de estudios acreditado y de referencia nacional e
                        internacional en la formación de abogados íntegros, con sólida preparación
                        científica, humanística y orientada a la investigación, comprometidos con la
                        justicia, la conciencia social, el desarrollo sostenible y la defensa del Estado
                        Constitucional de Derecho, capaces de enfrentar los retos de un entorno globalizado.
                    </p>
                </div>
            </x-reveal>
        </div>
    </section>
@endsection
