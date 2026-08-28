@extends('layouts.app')

@section('title', $comunicado->titulo.' — Derecho UNASAM')
@section('description', $comunicado->resumen ?: \Illuminate\Support\Str::limit(strip_tags($comunicado->contenido), 155))
@section('og_type', 'article')
@section('og_image', $comunicado->getFirstMediaUrl('imagen') ?: asset('img/escudo-unasam.png'))

@push('schema')
<x-breadcrumb-schema :items="[
    ['name' => 'Inicio', 'url' => route('home')],
    ['name' => 'Comunicados', 'url' => route('comunicados')],
    ['name' => $comunicado->titulo, 'url' => url()->current()],
]" />
@endpush

@section('content')
    <article class="mx-auto max-w-3xl px-6 py-16 lg:py-24">
        <a href="{{ route('comunicados') }}" wire:navigate.hover class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700"><x-ui-icon name="arrow-left" /> Volver a comunicados</a>
        @if ($comunicado->fecha_publicacion)<time class="mt-8 block text-sm text-stone-500" datetime="{{ $comunicado->fecha_publicacion->toAtomString() }}">{{ $comunicado->fecha_publicacion->translatedFormat('d \d\e F \d\e Y') }}</time>@endif
        <h1 class="mt-4 text-4xl leading-tight md:text-6xl">{{ $comunicado->titulo }}</h1>
        @if ($comunicado->resumen)<p class="mt-6 text-xl leading-relaxed text-stone-600">{{ $comunicado->resumen }}</p>@endif
        @if ($comunicado->getFirstMediaUrl('imagen'))<img class="mt-10 w-full" src="{{ $comunicado->getFirstMediaUrl('imagen') }}" alt="{{ $comunicado->titulo }}">@endif
        <div class="prose-editorial mt-10">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($comunicado->contenido) }}</div>
    </article>
@endsection
