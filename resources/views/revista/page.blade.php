@extends('layouts.app')
@section('title', $title.' — '.$revista->nombre_corto)
@section('content')
<x-page-hero :title="$title" :subtitle="$revista->nombre" />
<section class="mx-auto max-w-4xl px-6 py-16 md:py-20">
    <div class="prose-editorial">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($content) }}</div>
    @if ($title === 'Indexación' && $revista->issn)
        <div class="mt-10 border-l-2 border-gold-400 bg-paper p-6">
            <p class="font-semibold text-navy-900">{{ $revista->issn }}</p>
        </div>
    @endif
</section>

@include('revista.partials.continuar')
@endsection
