@extends('layouts.app')

@section('title', 'Plan de Estudios 2023 — Derecho UNASAM')
@section('description', 'Malla curricular vigente (Plan 2023) del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM: cursos por ciclo y créditos.')

@section('content')
    <x-page-hero seccion="Académico" title="Plan de Estudios 2023"
        subtitle="Malla curricular vigente del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        {{-- Metadatos: pares clave/valor → lista de definición --}}
        <dl class="reveal grid gap-4 sm:grid-cols-3">
            @foreach ([['Grado académico', $grado], ['Título profesional', $tituloProf], ['Modalidad', $modalidad]] as [$k, $v])
                <div class="rounded-2xl border border-stone-200 bg-paper p-5 text-center">
                    <dt class="font-sans text-xs uppercase tracking-wide text-stone-400">{{ $k }}</dt>
                    <dd class="mt-1 font-semibold text-navy-900">{{ $v }}</dd>
                </div>
            @endforeach
        </dl>

        {{-- Malla curricular por ciclos (si hay cursos cargados) --}}
        @if ($ciclos->isNotEmpty())
            <div class="reveal mt-12" data-reveal-delay="0.05" x-data="{ abierto: 1, todos: false }">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold text-navy-900">Malla curricular por ciclos</h2>
                        @if ($intro)
                            <p class="mt-2 font-sans text-stone-600">{{ $intro }}</p>
                        @endif
                    </div>
                    <button type="button" @click="todos = !todos; if (! todos) abierto = null"
                            class="link-arrow shrink-0 text-sm font-medium text-navy-700 hover:text-navy-900"
                            x-text="todos ? 'Colapsar todo' : 'Expandir todo'"></button>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach ($ciclos as $numCiclo => $cursos)
                        <div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
                            <button type="button" @click="abierto = (abierto === {{ $numCiclo }} ? null : {{ $numCiclo }}); todos = false"
                                    :aria-expanded="(todos || abierto === {{ $numCiclo }}).toString()"
                                    aria-controls="ciclo-{{ $numCiclo }}"
                                    class="flex w-full items-center justify-between px-5 py-4 text-left">
                                <span class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-navy-900 text-xs font-semibold text-white">{{ $cursos->first()->ciclo_romano }}</span>
                                    <span class="font-semibold text-navy-900">Ciclo {{ $cursos->first()->ciclo_romano }}</span>
                                    <span class="font-sans text-xs text-stone-400">{{ $cursos->count() }} curso(s)</span>
                                </span>
                                <svg class="h-5 w-5 text-stone-400 transition" :class="(todos || abierto === {{ $numCiclo }}) && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div x-show="todos || abierto === {{ $numCiclo }}" x-cloak
                                 id="ciclo-{{ $numCiclo }}" role="region" class="overflow-x-auto">
                                <table class="w-full min-w-[20rem] border-t border-stone-100 font-sans text-sm">
                                    <caption class="sr-only">Cursos del ciclo {{ $cursos->first()->ciclo_romano }}: nombre, tipo y créditos.</caption>
                                    <tbody class="divide-y divide-stone-100">
                                        @foreach ($cursos as $curso)
                                            <tr>
                                                <td class="px-5 py-3 text-navy-900">{{ $curso->nombre }}</td>
                                                <td class="px-5 py-3 text-right">
                                                    @if ($curso->tipo)
                                                        <span class="rounded-none bg-navy-50 px-2.5 py-0.5 text-[11px] font-medium text-navy-700">{{ $curso->tipo }}</span>
                                                    @endif
                                                </td>
                                                <td class="w-20 px-5 py-3 text-right text-stone-500">
                                                    @if ($curso->creditos !== null){{ $curso->creditos }} cr.@endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Documentos oficiales --}}
        <div class="reveal mt-12" data-reveal-delay="0.1">
            <h2 class="text-2xl font-semibold text-navy-900">Documentos oficiales</h2>
            <p class="mt-2 font-sans text-stone-600">Consulta y descarga la malla curricular completa con los cursos por ciclo y sus créditos.</p>

            <div class="mt-6 space-y-3">
                @if ($pdfUrl)
                    <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
                       class="card-hover flex items-center gap-4 rounded-2xl border border-stone-200 bg-white p-5">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-50 text-navy-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                        </span>
                        <div class="grow">
                            <h3 class="font-semibold text-navy-900">Malla curricular (PDF)</h3>
                            <p class="font-sans text-sm text-stone-500">Documento oficial firmado · SGA UNASAM</p>
                        </div>
                        <svg class="h-5 w-5 text-navy-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg>
                    </a>
                @endif

                @if ($sgaUrl)
                    <a href="{{ $sgaUrl }}" target="_blank" rel="noopener"
                       class="card-hover flex items-center gap-4 rounded-2xl border border-stone-200 bg-white p-5">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-50 text-navy-700">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 6.25v11.5m5.75-5.75H6.25"/></svg>
                        </span>
                        <div class="grow">
                            <h3 class="font-semibold text-navy-900">Plan curricular en línea</h3>
                            <p class="font-sans text-sm text-stone-500">Sistema de Gestión Académica (SGA)</p>
                        </div>
                        <svg class="h-5 w-5 text-navy-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 17 17 7M7 7h10v10"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </section>
@endsection
