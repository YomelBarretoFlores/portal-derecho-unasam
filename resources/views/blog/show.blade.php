@extends('layouts.app')

@section('title', $post->titulo.' — Derecho UNASAM')
@section('description', $post->extracto ?: \Illuminate\Support\Str::limit(strip_tags($post->contenido), 155))
@section('og_type', 'article')
@section('og_image', $post->getFirstMediaUrl('imagen') ?: asset('img/escudo-unasam.png'))

@push('schema')
@php
    $articleSchema = [
        '@'.'context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post->titulo,
        'datePublished' => $post->fecha?->toAtomString(),
        'author' => ['@type' => 'Person', 'name' => $post->autor ?: 'Programa de Derecho UNASAM'],
        'mainEntityOfPage' => url()->current(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<x-breadcrumb-schema :items="[
    ['name' => 'Inicio', 'url' => route('home')],
    ['name' => 'Blog', 'url' => route('blog')],
    ['name' => $post->titulo, 'url' => url()->current()],
]" />
@endpush

@section('content')
    <article class="mx-auto max-w-3xl px-6 py-16 lg:py-24">
        <a href="{{ route('blog') }}" wire:navigate.hover class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700"><x-ui-icon name="arrow-left" /> Volver al blog</a>
        <div class="mt-8 flex flex-wrap items-center gap-3 text-sm text-stone-500">
            <span class="bg-navy-50 px-3 py-1 font-semibold text-navy-700">{{ ucfirst($post->tipo) }}</span>
            @if ($post->fecha)<time datetime="{{ $post->fecha->toDateString() }}">{{ $post->fecha->translatedFormat('d \d\e F \d\e Y') }}</time>@endif
            @if ($post->autor)<span>{{ $post->autor }}</span>@endif
        </div>
        <h1 class="mt-5 text-4xl leading-tight text-navy-900 md:text-6xl">{{ $post->titulo }}</h1>
        @if ($post->extracto)<p class="mt-6 text-xl leading-relaxed text-stone-600">{{ $post->extracto }}</p>@endif
        @if ($post->getFirstMediaUrl('imagen'))
            <img class="mt-10 w-full" src="{{ $post->getFirstMediaUrl('imagen') }}" alt="{{ $post->titulo }}">
        @endif
        <div class="prose-editorial mt-10">
            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($post->contenido) }}
        </div>
    </article>
@endsection
