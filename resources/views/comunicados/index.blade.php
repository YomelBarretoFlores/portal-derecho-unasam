@extends('layouts.app')

@section('title', 'Comunicados — Derecho UNASAM')
@section('description', 'Avisos y comunicados oficiales del Programa de Estudios de Derecho y Ciencias Políticas.')

@section('content')
    <x-page-hero seccion="Transparencia" title="Comunicados" subtitle="Avisos y comunicados oficiales del Programa de Estudios." />
    <section class="mx-auto max-w-6xl px-6 py-20">
        @if ($comunicados->isEmpty())
            <x-empty-state title="Sin comunicados vigentes" description="Los avisos oficiales aparecerán aquí después de su aprobación y publicación por la Facultad." action="Volver al inicio" :href="route('home')" />
        @else
            <div @class([
                'grid gap-8',
                'max-w-3xl' => $comunicados->count() === 1,
                'sm:grid-cols-2' => $comunicados->count() === 2,
                'sm:grid-cols-2 lg:grid-cols-3' => $comunicados->count() >= 3,
            ])>
                @foreach ($comunicados as $comunicado)
                    <article class="card-hover flex flex-col overflow-hidden border border-stone-200 bg-white">
                        @if ($comunicado->_imagen_url)
                            <img src="{{ $comunicado->_imagen_url }}" alt="{{ $comunicado->titulo }}" loading="lazy" class="aspect-[3/2] w-full object-cover">
                        @endif
                        <div class="flex grow flex-col p-6">
                            <time class="text-xs text-stone-500" datetime="{{ $comunicado->fecha_publicacion->toAtomString() }}">{{ $comunicado->fecha_publicacion->translatedFormat('d M Y') }}</time>
                            <h2 class="mt-3 text-xl leading-snug"><a href="{{ route('comunicados.show', $comunicado->slug) }}" wire:navigate.hover>{{ $comunicado->titulo }}</a></h2>
                            @if ($comunicado->resumen)<p class="mt-3 grow text-sm leading-relaxed text-stone-500">{{ $comunicado->resumen }}</p>@endif
                            <a href="{{ route('comunicados.show', $comunicado->slug) }}" wire:navigate.hover class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-navy-700">Leer comunicado <x-ui-icon name="arrow-right" /></a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-10">{{ $comunicados->links() }}</div>
        @endif
    </section>
@endsection
