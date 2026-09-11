@extends('layouts.app')

@section('title', 'Resumen del Programa — Derecho UNASAM')
@section('description', 'Resumen del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM: currículo flexible y por competencias.')

@section('content')
    <x-page-hero title="Resumen del Programa de Estudios"
        subtitle="Programa de Estudio de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        <div class="reveal max-w-3xl">
            <div class="accent-line"></div>
            <h2 class="mt-5 text-3xl font-semibold text-navy-900">{{ $titulo ?: 'Currículo flexible y por competencias' }}</h2>

            <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                @foreach (preg_split('/\R{2,}/', trim((string) $cuerpo)) as $parrafo)
                    <p>{{ $parrafo }}</p>
                @endforeach
            </div>
        </div>

        {{-- Marco legal --}}
        <div class="reveal mt-10 space-y-5" data-reveal-delay="0.1">
            <blockquote class="rounded-2xl border-l-4 border-navy-300 bg-paper p-6">
                <p class="font-sans text-[15px] leading-relaxed text-stone-600">
                    <span class="font-semibold text-navy-900">Ley Universitaria N.º 30220, artículo 40.º (Diseño Curricular):</span>
                    {{ $cita1 }}
                </p>
            </blockquote>

            <blockquote class="rounded-2xl border-l-4 border-navy-700 bg-paper p-6">
                <p class="font-sans text-[15px] leading-relaxed text-stone-600">
                    <span class="font-semibold text-navy-900">Artículo 79.º — De la Actualización del Currículo (2015):</span>
                    {{ $cita2 }}
                </p>
            </blockquote>
        </div>

        <p class="reveal mt-8 max-w-3xl font-sans text-[17px] leading-relaxed text-stone-600" data-reveal-delay="0.15">
            {{ $cierre }}
        </p>
    </section>
@endsection
