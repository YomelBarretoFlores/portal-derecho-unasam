@extends('layouts.app')

@section('title', 'Perfil de Egreso — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Perfil de Egreso"
        subtitle="El profesional que forma el Programa de Estudios de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @php
            $planes = [
                ['titulo' => 'Perfil de Egreso 2023', 'destacado' => true, 'cuerpo' => $egreso2023],
                ['titulo' => 'Perfil de Egreso 2019', 'destacado' => false, 'cuerpo' => $egreso2019],
            ];
        @endphp

        <div class="space-y-8">
            @foreach ($planes as $plan)
                <x-reveal>
                    <div class="rounded-3xl border border-stone-200 bg-white p-8 md:p-10">
                        <div class="flex items-center gap-3">
                            <div class="accent-line"></div>
                            <h2 class="text-2xl font-semibold text-navy-900">{{ $plan['titulo'] }}</h2>
                            @if ($plan['destacado'])
                                <span class="rounded-full bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                            @endif
                        </div>
                        <div class="mt-5 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                            @foreach (preg_split('/\R{2,}/', trim((string) $plan['cuerpo'])) as $parrafo)
                                <p>{{ $parrafo }}</p>
                            @endforeach
                        </div>
                    </div>
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
