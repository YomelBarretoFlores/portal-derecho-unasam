@extends('layouts.app')

@section('title', $articulo->titulo.' — '.$articulo->numero->revista->nombre)
@section('description', $articulo->resumen)
@section('og_type', 'article')

@push('schema')
@php
    $articleSchema = [
        '@'.'context' => 'https://schema.org',
        '@type' => 'ScholarlyArticle',
        'headline' => $articulo->titulo,
        'datePublished' => $articulo->fecha?->toAtomString(),
        'author' => collect($articulo->autores)->map(fn ($name) => ['@type' => 'Person', 'name' => $name])->all(),
        'isPartOf' => ['@type' => 'PublicationIssue', 'name' => $articulo->numero->titulo],
        'mainEntityOfPage' => url()->current(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<x-breadcrumb-schema :items="[
    ['name' => 'Inicio', 'url' => route('home')],
    ['name' => 'Revista', 'url' => route('revista')],
    ['name' => $numero->titulo, 'url' => route('revista.numero', $numero)],
    ['name' => $articulo->titulo, 'url' => url()->current()],
]" />
@endpush

@section('content')
    <article class="mx-auto max-w-3xl px-6 py-16 lg:py-24">
        <a href="{{ route('revista.numero', $numero) }}" wire:navigate.hover class="text-sm font-semibold text-navy-700">← Volver al número</a>
        <p class="mt-8 text-xs font-semibold uppercase tracking-wider text-navy-600">{{ $articulo->categoria }}</p>
        <h1 class="mt-4 text-4xl leading-tight md:text-6xl">{{ $articulo->titulo }}</h1>
        <p class="mt-6 text-base font-medium text-stone-700">{{ implode(' · ', $articulo->autores ?? []) }}</p>
        <div class="mt-3 flex flex-wrap gap-4 text-sm text-stone-500">
            <time>{{ $articulo->fecha?->translatedFormat('d \d\e F \d\e Y') ?: 'Fecha pendiente' }}</time>
            @if ($articulo->paginas)<span>pp. {{ $articulo->paginas }}</span>@endif
            @if ($articulo->doi)<span>DOI: {{ $articulo->doi }}</span>@endif
        </div>
        @if ($articulo->resumen)<div class="mt-8 border-l-2 border-gold-400 pl-6 text-lg leading-relaxed text-stone-600">{{ $articulo->resumen }}</div>@endif
        @if ($articulo->contenido)<div class="prose-editorial mt-10">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($articulo->contenido) }}</div>@endif
        @if ($articulo->getFirstMediaUrl('pdf'))<a href="{{ $articulo->getFirstMediaUrl('pdf') }}" target="_blank" rel="noopener" class="btn btn-primary mt-10">Descargar PDF</a>@endif
    </article>
@endsection
