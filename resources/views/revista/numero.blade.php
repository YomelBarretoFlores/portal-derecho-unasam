@extends('layouts.app')

@section('title', $numero->titulo.' — '.$numero->revista->nombre)
@section('description', $numero->descripcion ?: 'Artículos publicados en el volumen '.$numero->volumen.', número '.$numero->numero.'.')
@section('og_image', $numero->portada_url ?: asset('img/escudo-unasam.png'))

@push('schema')
<x-breadcrumb-schema :items="[
    ['name' => 'Inicio', 'url' => route('home')],
    ['name' => 'Revista', 'url' => route('revista')],
    ['name' => $numero->titulo, 'url' => url()->current()],
]" />
@endpush

@section('content')
    <section class="border-b border-stone-200 bg-paper">
        <div class="mx-auto max-w-7xl px-6 py-16">
            <a href="{{ route('revista') }}" wire:navigate.hover class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700"><x-ui-icon name="arrow-left" /> Todos los números</a>
            <p class="mt-8 text-xs font-semibold uppercase tracking-widest text-navy-600">Vol. {{ $numero->volumen }} · Núm. {{ $numero->numero }}</p>
            <h1 class="mt-3 max-w-4xl text-4xl leading-tight md:text-6xl">{{ $numero->titulo }}</h1>
            @if ($numero->descripcion)<p class="mt-5 max-w-2xl text-lg leading-relaxed text-stone-600">{{ $numero->descripcion }}</p>@endif
            <div class="mt-6 flex flex-wrap gap-4 text-sm text-stone-500">
                @if($numero->fecha_publicacion)<time>{{ $numero->fecha_publicacion->translatedFormat('F Y') }}</time>@endif
                @if ($numero->pdf_url)<a href="{{ $numero->pdf_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 font-semibold text-navy-700">Descargar número completo <x-ui-icon name="external-link" /></a>@endif
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-16">
        <form method="get" class="grid gap-3 md:grid-cols-[1fr_16rem_auto]" role="search">
            <input name="q" value="{{ $q }}" type="search" aria-label="Buscar artículo" placeholder="Título o resumen…" class="border border-stone-300 px-4 py-3 text-sm">
            <select name="categoria" aria-label="Categoría" class="border border-stone-300 px-4 py-3 text-sm">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $item)<option value="{{ $item }}" @selected($categoria === $item)>{{ $item }}</option>@endforeach
            </select>
            <button class="btn btn-primary" type="submit">Filtrar</button>
        </form>

        @if ($articulos->isEmpty())
            <x-empty-state class="mt-10" title="No hay artículos disponibles" description="Este número todavía no tiene artículos públicos que coincidan con los criterios seleccionados." action="Volver a la revista" :href="route('revista')" />
        @else
            <div class="mt-10 grid gap-6 md:grid-cols-2">@foreach ($articulos as $articulo)<x-revista-card :articulo="$articulo" :numero="$numero" />@endforeach</div>
            <div class="mt-10">{{ $articulos->links() }}</div>
        @endif
    </section>
@endsection
