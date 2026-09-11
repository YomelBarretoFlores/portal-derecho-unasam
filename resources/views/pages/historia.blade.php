@extends('layouts.app')

@section('title', 'Historia — Derecho UNASAM')
@section('description', 'Casi cuatro décadas formando abogados al servicio de Áncash y el Perú: hitos y trayectoria del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM.')

@section('content')
    <x-page-hero title="Historia"
        subtitle="Casi cuatro décadas formando abogados al servicio de Áncash y el Perú." />

    <section class="mx-auto max-w-4xl px-6 py-20">

        {{-- Línea de tiempo de hitos institucionales (resoluciones) --}}
        @if (collect($hitos)->isEmpty())
            <x-empty-state title="Trayectoria institucional" description="Los hitos de la historia del programa se publicarán conforme se documenten sus resoluciones." />
        @else
        <ol class="relative border-l-2 border-stone-200 pl-8">
            @foreach ($hitos as $i => $hito)
                <li class="reveal relative mb-10 last:mb-0" @if ($i) data-reveal-delay="{{ $i * 0.05 }}" @endif>
                    <span class="absolute -left-[42px] flex h-6 w-6 items-center justify-center rounded-full bg-navy-600 ring-4 ring-white" aria-hidden="true"></span>
                    <span class="text-3xl font-bold text-navy-600">{{ $hito->anio }}</span>
                    <h3 class="mt-1 text-xl font-semibold text-navy-900">{{ $hito->titulo }}</h3>
                    <p class="mt-2 font-sans leading-relaxed text-stone-600">{{ $hito->descripcion }}</p>
                </li>
            @endforeach
        </ol>
        @endif

        {{-- Reseña descriptiva --}}
        <div class="reveal mt-16 max-w-3xl border-t border-stone-200 pt-12">
            <div class="accent-line"></div>
            <h2 class="mt-5 text-2xl font-semibold text-navy-900">{{ $trayectoriaTitulo }}</h2>
            <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-stone-600">
                @foreach (preg_split('/\R{2,}/', trim((string) $trayectoriaCuerpo)) as $parrafo)
                    <p>{{ $parrafo }}</p>
                @endforeach
            </div>
        </div>
    </section>
@endsection
