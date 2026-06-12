@extends('layouts.app')

@section('title', 'Misión y Visión — Derecho UNASAM')
@section('description', 'Misión y visión del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM: el propósito que nos guía y el horizonte al que aspiramos.')

@section('content')
    <x-page-hero seccion="Programa" title="Misión y Visión"
        subtitle="El propósito que nos guía y el horizonte al que aspiramos." />

    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="grid gap-8 lg:grid-cols-2">
            {{-- Misión (oscura) --}}
            <x-reveal>
                <div class="flex h-full flex-col rounded-3xl bg-navy-900 p-10 text-white">
                    <h2 class="font-sans text-xs font-semibold uppercase tracking-[0.14em] text-white/50">Misión</h2>
                    <div class="accent-line mt-4"></div>
                    <p class="mt-6 text-2xl leading-relaxed text-white/90">{{ $mision }}</p>
                </div>
            </x-reveal>

            {{-- Visión (clara) --}}
            <x-reveal :delay="0.12">
                <div class="flex h-full flex-col rounded-3xl border border-stone-200 bg-paper p-10">
                    <h2 class="font-sans text-xs font-semibold uppercase tracking-[0.14em] text-stone-500">Visión</h2>
                    <div class="accent-line mt-4"></div>
                    <p class="mt-6 text-2xl leading-relaxed text-navy-900">{{ $vision }}</p>
                </div>
            </x-reveal>
        </div>
    </section>
@endsection
