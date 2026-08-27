@extends('layouts.app')
@section('title', $title.' — '.$revista->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" :title="$title" :subtitle="$revista->nombre" />
<section class="mx-auto max-w-4xl px-6 py-16 md:py-20">
    <div class="prose-editorial">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content) }}</div>
    @if ($title === 'Indexación')
        <div class="mt-10 border-l-2 border-gold-400 bg-paper p-6">
            <p class="font-semibold text-navy-900">{{ $revista->issn ?: 'ISSN en línea en proceso de gestión' }}</p>
        </div>
    @endif
</section>
@endsection
