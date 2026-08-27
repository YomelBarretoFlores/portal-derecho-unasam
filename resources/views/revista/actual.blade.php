@extends('layouts.app')
@section('title', 'Número actual — '.$revista->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" title="Actual" subtitle="Número vigente de Derecho y Cultura." />
<section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
@if ($numero)
    <div class="grid gap-12 lg:grid-cols-[20rem_minmax(0,1fr)]">
        @if ($numero->getFirstMediaUrl('portada'))<img src="{{ $numero->getFirstMediaUrl('portada') }}" alt="Portada de {{ $numero->titulo }}" class="w-full border border-stone-200">@else<div class="paper-grid flex aspect-[4/5] items-center justify-center border border-stone-200 text-6xl text-navy-800" aria-hidden="true">PDF</div>@endif
        <div><p class="eyebrow">Vol. {{ $numero->volumen }} · Núm. {{ $numero->numero }}</p><h1 class="mt-4 text-4xl">{{ $numero->titulo }}</h1>
            @if($numero->descripcion)<p class="mt-5 text-lg leading-relaxed">{{ $numero->descripcion }}</p>@endif
            <p class="mt-4 text-sm text-stone-500">{{ $numero->fecha_publicacion?->translatedFormat('F Y') }}</p>
            <div class="mt-8 flex flex-wrap gap-3"><a class="btn btn-primary" href="{{ route('revista.numero',$numero) }}">Ver artículos</a>@if($numero->getFirstMediaUrl('numero_pdf'))<a class="btn btn-ghost" href="{{ $numero->getFirstMediaUrl('numero_pdf') }}" target="_blank" rel="noopener">Descargar número completo (PDF)</a>@endif</div>
        </div>
    </div>
@else
    <div class="editorial-empty grid gap-8 md:grid-cols-[8rem_1fr] md:items-center">
        <div class="flex h-28 w-28 items-center justify-center border border-navy-200 bg-white text-2xl font-bold text-navy-800" aria-hidden="true">PDF</div>
        <div><p class="eyebrow">Primera edición</p><h1 class="mt-3 text-3xl">Próximamente se publicará la primera edición</h1><p class="mt-4 leading-relaxed">Aquí estará disponible el PDF completo del número actual para consulta y descarga.</p></div>
    </div>
@endif
</section>
@endsection
