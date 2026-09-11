@extends('layouts.app')

@section('title', 'Organigrama — Derecho UNASAM')
@section('description', 'Estructura organizativa del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM.')

@section('content')
    <x-page-hero title="{{ $titulo ?: 'Organigrama' }}"
        subtitle="Estructura organizativa del Programa de Estudios de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-5xl px-6 py-20">
        @if ($descripcion)
            <div class="reveal mx-auto mb-10 max-w-3xl text-center font-sans text-[17px] leading-relaxed text-stone-600">
                {{ $descripcion }}
            </div>
        @endif

        @if ($imagen)
            <div class="reveal overflow-hidden border border-stone-200 bg-white p-4 shadow-card">
                <img src="{{ $imagen }}" alt="Organigrama del Programa de Derecho y Ciencias Políticas"
                     class="mx-auto h-auto w-full">
            </div>
        @else
            <x-empty-state class="reveal mx-auto max-w-3xl" title="Estructura organizativa" description="Consulta los documentos institucionales disponibles del Programa de Derecho y Ciencias Políticas." action="Consultar documentos" :href="route('documentos')" />
        @endif
    </section>
@endsection
