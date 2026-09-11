@extends('layouts.app')
@section('title','Formatos y plantillas — '.$ficha->nombre_corto)
@section('content')
<x-page-hero title="Formatos y plantillas" subtitle="Documentos editables requeridos para presentar un manuscrito." />
<section class="mx-auto max-w-5xl px-6 py-16 md:py-20">
    <div class="grid gap-5 md:grid-cols-3">
        @foreach($documentos as $documento)
            <article class="border border-stone-200 p-6">
                <div class="flex items-center gap-3 text-navy-800"><x-ui-icon name="document" class="h-9 w-9" /><span class="text-xs font-semibold uppercase tracking-wider">DOCX</span></div>
                <h2 class="mt-5 text-xl">{{ $documento->titulo }}</h2>
                <p class="mt-3 text-sm leading-relaxed">{{ $documento->descripcion }}</p>
                <a href="{{ $documento->download_url }}" class="btn btn-primary mt-6" download>Descargar Word</a>
            </article>
        @endforeach
    </div>
</section>

@include('revista.partials.continuar')
@endsection
