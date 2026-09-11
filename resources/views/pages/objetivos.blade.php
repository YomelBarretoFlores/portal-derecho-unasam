@extends('layouts.app')

@section('title', 'Objetivos Educacionales — Derecho UNASAM')
@section('description', 'Objetivos educacionales del Programa de Derecho de la UNASAM: logros que se esperan de nuestros egresados según cada plan de estudios.')

@section('content')
    <x-page-hero title="Objetivos Educacionales"
        subtitle="Logros que se espera de nuestros egresados, según cada plan de estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @if (collect($planes)->isEmpty())
            <x-empty-state title="Objetivos educacionales" description="Los objetivos por plan de estudios se publicarán conforme se aprueben." />
        @else
        <div class="space-y-14">
            @foreach ($planes as $plan)
                <div class="reveal">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-semibold text-navy-900">{{ $plan['titulo'] }}</h2>
                        @if ($plan['destacado'])
                            <span class=" bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                        @endif
                    </div>

                    <ol class="mt-6 space-y-4">
                        @foreach ($plan['objetivos'] as $i => $texto)
                            <li class="flex gap-5 border border-stone-200 bg-white p-6">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center bg-navy-900 text-sm font-semibold text-white">OE{{ $i + 1 }}</span>
                                <p class="pt-1 font-sans leading-relaxed text-stone-600">{{ $texto }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
        @endif
    </section>
@endsection
