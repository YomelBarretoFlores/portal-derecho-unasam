@extends('layouts.app')

@section('title', 'Organigrama — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="{{ $titulo ?: 'Organigrama' }}"
        subtitle="Estructura organizativa del Programa de Estudios de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-5xl px-6 py-20">
        @if ($descripcion)
            <div class="reveal mx-auto mb-10 max-w-3xl text-center font-sans text-[17px] leading-relaxed text-stone-600">
                {{ $descripcion }}
            </div>
        @endif

        @if ($imagen)
            <div class="reveal overflow-hidden rounded-2xl border border-stone-200 bg-white p-4 shadow-card">
                <img src="{{ $imagen }}" alt="Organigrama del Programa de Derecho y Ciencias Políticas"
                     class="mx-auto h-auto w-full rounded-lg">
            </div>
        @else
            <div class="reveal mx-auto max-w-2xl rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-navy-50">
                    <svg class="h-8 w-8 text-navy-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                </div>
                <p class="mt-5 font-sans text-stone-500">
                    El organigrama se publicará próximamente. El personal puede subir la imagen
                    desde el panel de administración.
                </p>
            </div>
        @endif
    </section>
@endsection
