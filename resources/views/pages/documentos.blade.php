@extends('layouts.app')

@section('title', 'Documentos Normativos — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Transparencia" title="Documentos Normativos"
        subtitle="Reglamentos, planes y resoluciones del Programa de Estudios de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-5xl px-6 py-20">
        @php
            // Documentos administrables desde el panel (título, categoría, fecha, PDF).
            $categorias = $documentos->pluck('categoria')->unique()->values();
            // Estructura serializada para el filtro reactivo de Alpine.
            $items = $documentos->map(fn ($d) => [
                'titulo' => $d->titulo,
                'categoria' => $d->categoria,
                'fecha' => optional($d->fecha)->translatedFormat('d M Y') ?? '',
                'busqueda' => \Illuminate\Support\Str::lower($d->titulo.' '.$d->categoria),
                'url' => $d->enlace ?: '#',
            ])->values();
        @endphp

        <div class="reveal" x-data="{ q: '', cat: 'all', items: @js($items) }">
            {{-- Controles: buscador + filtro por categoría --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="relative sm:w-72">
                    <svg class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-stone-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input x-model="q" type="search" placeholder="Buscar documento…"
                           class="w-full rounded-xl border border-stone-300 py-2.5 pl-10 pr-4 font-sans text-sm focus:border-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-700/10">
                </div>
                <select x-model="cat"
                        class="rounded-xl border border-stone-300 px-4 py-2.5 font-sans text-sm text-stone-700 focus:border-navy-700 focus:outline-none">
                    <option value="all">Todas las categorías</option>
                    @foreach ($categorias as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tabla --}}
            <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200">
                <table class="w-full font-sans text-sm">
                    <thead class="bg-navy-900 text-left text-white">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Documento</th>
                            <th class="hidden px-5 py-3.5 font-semibold sm:table-cell">Categoría</th>
                            <th class="hidden px-5 py-3.5 font-semibold md:table-cell">Fecha</th>
                            <th class="px-5 py-3.5 text-right font-semibold">Archivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <template x-for="(doc, i) in items.filter(d => (cat === 'all' || d.categoria === cat) && d.busqueda.includes(q.toLowerCase()))" :key="i">
                            <tr class="transition hover:bg-stone-50/50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 shrink-0 text-navy-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
                                        <span class="font-medium text-navy-900" x-text="doc.titulo"></span>
                                    </div>
                                </td>
                                <td class="hidden px-5 py-4 sm:table-cell">
                                    <span class="rounded-full bg-navy-50 px-2.5 py-1 text-[11px] font-semibold text-navy-700" x-text="doc.categoria"></span>
                                </td>
                                <td class="hidden px-5 py-4 text-stone-500 md:table-cell" x-text="doc.fecha"></td>
                                <td class="px-5 py-4 text-right">
                                    <template x-if="doc.url !== '#'">
                                        <a :href="doc.url" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-navy-900 px-3 py-1.5 text-xs font-semibold text-navy-900 transition hover:bg-navy-900 hover:text-white">
                                            PDF
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 21h16"/></svg>
                                        </a>
                                    </template>
                                    <template x-if="doc.url === '#'">
                                        <span class="rounded-full bg-stone-100 px-2.5 py-1 text-[11px] font-medium text-stone-400">Próximamente</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Estado vacío --}}
            <p x-show="items.filter(d => (cat === 'all' || d.categoria === cat) && d.busqueda.includes(q.toLowerCase())).length === 0"
               x-cloak class="mt-6 rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-12 text-center font-sans text-sm text-stone-500">
                No se encontraron documentos con ese criterio.
            </p>
        </div>
    </section>
@endsection
