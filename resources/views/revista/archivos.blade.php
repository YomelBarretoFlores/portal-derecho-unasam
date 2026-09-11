@extends('layouts.app')
@section('title','Archivos — '.$ficha->nombre_corto)
@section('content')
<x-page-hero title="Archivos" subtitle="Ediciones anteriores y sus artículos." />
<section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
@if($numeros->isEmpty())
 <x-empty-state title="Aún no hay ediciones anteriores" description="Las ediciones pasarán a este archivo cuando se publique un nuevo número actual." action="Ver número actual" :href="route('revista.actual')" />
@else
 <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">@foreach($numeros as $numero)<article class="border border-stone-200 bg-white">@if($numero->portada_url)<img src="{{ $numero->portada_url }}" alt="Portada de {{ $numero->titulo }}" class="aspect-[4/5] w-full object-cover">@endif<div class="p-6"><p class="eyebrow">Vol. {{ $numero->volumen }} · Núm. {{ $numero->numero }}</p><h2 class="mt-3 text-2xl"><a href="{{ route('revista.numero',$numero) }}">{{ $numero->titulo }}</a></h2>@if($numero->descripcion)<p class="mt-3 text-sm leading-relaxed">{{ $numero->descripcion }}</p>@endif<div class="mt-5 flex flex-wrap gap-3"><a href="{{ route('revista.numero',$numero) }}" class="text-sm font-semibold text-navy-800 underline">Ver artículos</a>@if($numero->pdf_url)<a href="{{ $numero->pdf_url }}" class="text-sm font-semibold text-navy-800 underline">PDF completo</a>@endif</div></div></article>@endforeach</div>
 <div class="mt-10">{{ $numeros->links() }}</div>
@endif
</section>
@endsection
