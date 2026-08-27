@extends('layouts.app')
@section('title','Formatos y plantillas — '.$ficha->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" title="Formatos y plantillas" subtitle="Documentos editables requeridos para presentar un manuscrito." />
<section class="mx-auto max-w-5xl px-6 py-16 md:py-20"><div class="grid gap-5 md:grid-cols-3">@foreach($documentos as $documento)<article class="border border-stone-200 p-6"><div class="text-3xl text-navy-800" aria-hidden="true">DOCX</div><h2 class="mt-5 text-xl">{{ $documento->titulo }}</h2><p class="mt-3 text-sm leading-relaxed">{{ $documento->descripcion }}</p><a href="{{ $documento->download_url }}" class="btn btn-primary mt-6" download>Descargar Word</a></article>@endforeach</div><p class="mt-10 border-l-2 border-gold-400 bg-paper p-5 text-sm">La plantilla publicada es una copia corregida conforme al PDF oficial: A4, Garamond 12, interlineado 1.5, margen izquierdo de 3 cm, demás márgenes de 2.5 cm y títulos Garamond 13.</p></section>
@endsection
