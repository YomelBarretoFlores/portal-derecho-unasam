@extends('layouts.app')

@section('title', 'Objetivos Educacionales — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Programa" title="Objetivos Educacionales"
        subtitle="Logros que se espera de nuestros egresados, según cada plan de estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
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
