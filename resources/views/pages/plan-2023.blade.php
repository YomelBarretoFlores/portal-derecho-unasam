@extends('layouts.app')

@section('title', 'Plan de Estudios 2023 — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Académico" title="Plan de Estudios 2023"
        subtitle="Malla curricular vigente del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        <div class="reveal grid gap-4 sm:grid-cols-3">
            @foreach ([['Grado académico', 'Bachiller en Derecho'], ['Título profesional', 'Abogado(a)'], ['Modalidad', 'Presencial']] as [$k, $v])
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 text-center">
                    <p class="font-sans text-xs uppercase tracking-wide text-gray-400">{{ $k }}</p>
                    <p class="mt-1 font-semibold text-navy-900">{{ $v }}</p>
                </div>
            @endforeach
        </div>

        <div class="reveal mt-10" data-reveal-delay="0.1">
            <h2 class="text-2xl font-semibold text-navy-900">Documentos oficiales</h2>
            <p class="mt-2 font-sans text-gray-600">Consulta y descarga la malla curricular completa con los cursos por ciclo y sus créditos.</p>

            <div class="mt-6 space-y-3">
                <a href="https://sga.unasam.edu.pe/res/mallas_firmadas/DERECHO%20Y%20CIENCIAS%20POL%C3%8DTICAS.pdf"
                   target="_blank" rel="noopener"
                   class="card-hover flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gold-100 text-gold-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                    </span>
                    <div class="grow">
                        <h3 class="font-semibold text-navy-900">Malla curricular (PDF)</h3>
                        <p class="font-sans text-sm text-gray-500">Documento oficial firmado · SGA UNASAM</p>
                    </div>
                    <svg class="h-5 w-5 text-gold-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg>
                </a>

                <a href="https://sga.unasam.edu.pe/escuela/16/plancurricular/06"
                   target="_blank" rel="noopener"
                   class="card-hover flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 6.25v11.5m5.75-5.75H6.25"/></svg>
                    </span>
                    <div class="grow">
                        <h3 class="font-semibold text-navy-900">Plan curricular en línea</h3>
                        <p class="font-sans text-sm text-gray-500">Sistema de Gestión Académica (SGA)</p>
                    </div>
                    <svg class="h-5 w-5 text-gold-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg>
                </a>
            </div>

            <p class="mt-6 rounded-xl bg-blue-50 px-4 py-3 font-sans text-sm text-navy-700">
                Cuando se conecte el CMS, los cursos por ciclo podrán administrarse y mostrarse aquí
                directamente, sin depender del PDF.
            </p>
        </div>
    </section>
@endsection
