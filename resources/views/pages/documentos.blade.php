@extends('layouts.app')

@section('title', 'Documentos Normativos — Derecho UNASAM')
@section('description', 'Reglamentos, planes y resoluciones del Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM.')

@section('content')
    <x-page-hero seccion="Transparencia" title="Documentos Normativos" subtitle="Reglamentos, planes y resoluciones del Programa de Estudios." />
    <section class="mx-auto max-w-5xl px-6 py-20">
        <form method="get" class="grid gap-3 border-y border-stone-200 bg-paper px-4 py-5 sm:grid-cols-[1fr_16rem_auto] sm:px-5" role="search">
            <input name="q" value="{{ $q }}" type="search" aria-label="Buscar documento" placeholder="Buscar documento…" class="border border-stone-300 bg-white px-4 py-3 text-sm focus:border-navy-800 focus:outline-none">
            <select name="categoria" aria-label="Categoría" class="border border-stone-300 bg-white px-4 py-3 text-sm focus:border-navy-800 focus:outline-none">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $item)<option value="{{ $item }}" @selected($categoria === $item)>{{ $item }}</option>@endforeach
            </select>
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </form>

        @if ($documentos->isEmpty())
            <x-empty-state class="mt-8" title="No encontramos documentos" description="No existen documentos publicados que coincidan con el texto o la categoría seleccionada." action="Limpiar búsqueda" :href="route('documentos')" />
        @else
            <div class="mt-8 overflow-x-auto border border-stone-200">
                <table class="w-full min-w-[42rem] text-sm">
                    <caption class="sr-only">Documentos normativos publicados</caption>
                    <thead class="bg-navy-900 text-left text-white"><tr><th class="px-5 py-4">Documento</th><th class="px-5 py-4">Categoría</th><th class="px-5 py-4">Fecha</th><th class="px-5 py-4 text-right">Archivo</th></tr></thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($documentos as $documento)
                            <tr><td class="px-5 py-4 font-medium text-navy-900">{{ $documento->titulo }}</td><td class="px-5 py-4">{{ $documento->categoria }}</td><td class="px-5 py-4">{{ $documento->fecha?->translatedFormat('d M Y') }}</td><td class="px-5 py-4 text-right">@if ($documento->enlace)<a href="{{ $documento->enlace }}" target="_blank" rel="noopener" class="font-semibold text-navy-700">Abrir PDF ↗</a>@else<span class="text-stone-400">Pendiente</span>@endif</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-10">{{ $documentos->links() }}</div>
        @endif
    </section>
@endsection
