@extends('layouts.app')

@section('title', 'Normas para autores — '.$revista->nombre_corto)
@section('description', 'Política editorial, evaluación y requisitos de publicación de la revista '.$revista->nombre_corto.'.')

@section('content')
    <x-page-hero seccion="Revista" title="Normas para autores" subtitle="Directrices de presentación, evaluación y publicación de manuscritos." />

    <section class="mx-auto grid max-w-7xl gap-12 px-6 py-16 md:py-20 lg:grid-cols-[minmax(0,1fr)_19rem] lg:gap-20">
        <div>
            <div class="mb-10 flex flex-wrap gap-3 border-b border-stone-200 pb-8">
                <a href="{{ route('revista') }}" wire:navigate.hover class="btn btn-ghost">Volver a la revista</a>
                <a href="{{ route('revista.equipo') }}" wire:navigate.hover class="btn btn-primary">Equipo editorial</a>
            </div>
            <div class="prose-editorial max-w-3xl">
                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->normas_publicacion) }}
            </div>
        </div>

        <aside class="h-fit border-t-2 border-navy-900 bg-paper p-6 lg:sticky lg:top-8" aria-label="Resumen de política editorial">
            <p class="eyebrow">Resumen editorial</p>
            <dl class="mt-5 space-y-5 text-sm">
                <div><dt class="font-semibold text-navy-900">Periodicidad</dt><dd class="mt-1 leading-relaxed">{{ $revista->periodicidad }}</dd></div>
                <div><dt class="font-semibold text-navy-900">Arbitraje</dt><dd class="mt-1">{{ $revista->sistema_arbitraje }}</dd></div>
                <div><dt class="font-semibold text-navy-900">Citación</dt><dd class="mt-1">{{ $revista->norma_citacion }}</dd></div>
                <div><dt class="font-semibold text-navy-900">Contacto</dt><dd class="mt-1"><a class="text-navy-700 underline underline-offset-4" href="mailto:{{ $revista->contacto_email }}">{{ $revista->contacto_email }}</a></dd></div>
            </dl>
            @if ($revista->getFirstMediaUrl('resolucion'))
                <a href="{{ $revista->getFirstMediaUrl('resolucion') }}" target="_blank" rel="noopener" class="mt-7 inline-flex text-sm font-semibold text-navy-800 underline underline-offset-4">Consultar documento completo ↗</a>
            @endif
        </aside>
    </section>
@endsection
