@extends('layouts.app')

@section('title', 'Perfil de Ingreso — Derecho UNASAM')
@section('description', 'Perfil de ingreso del Programa de Derecho de la UNASAM: competencias que se esperan del estudiante que inicia el programa (planes 2019 y 2023).')

@section('content')
    <x-page-hero title="Perfil de Ingreso"
        subtitle="Competencias que se espera del estudiante que inicia el programa (igual en los planes 2019 y 2023)." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        {{-- Perfil de ingreso del área --}}
        <div class="reveal">
            <div class="accent-line"></div>
            <h2 class="mt-4 text-2xl font-semibold text-navy-900">Perfil de ingreso del área</h2>
        </div>

        @if (collect($areas)->isEmpty())
            <x-empty-state title="Perfil de ingreso" description="Las áreas del perfil de ingreso se publicarán próximamente." />
        @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($areas as $i => $area)
                <x-reveal :delay="$i * 0.08">
                    <div class="h-full rounded-2xl border border-stone-200 bg-white p-7">
                        <div class="h-0.5 w-10 bg-navy-600"></div>
                        <h3 class="mt-4 text-xl font-semibold text-navy-900">{{ $area->titulo }}</h3>
                        <ul class="mt-4 space-y-3 font-sans text-sm text-stone-600">
                            @foreach ($area->items ?? [] as $item)
                                <li class="flex gap-2.5">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-navy-600"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </x-reveal>
            @endforeach
        </div>
        @endif

        {{-- Perfil de ingreso específico --}}
        <div class="reveal mt-14 max-w-3xl" data-reveal-delay="0.05">
            <div class="accent-line"></div>
            <h2 class="mt-4 text-2xl font-semibold text-navy-900">Perfil de ingreso específico</h2>
            <p class="mt-4 rounded-2xl border border-stone-200 bg-paper p-6 font-sans text-[17px] leading-relaxed text-stone-600">
                {{ $especifico }}
            </p>
        </div>
    </section>
@endsection
