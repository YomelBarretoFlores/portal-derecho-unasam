@extends('layouts.app')
@section('title',$title.' — '.$revista->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" :title="$title" :subtitle="$revista->nombre" />
<section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
@foreach(\App\Models\RevistaMiembro::GRUPOS as $clave=>$tituloGrupo)
 @php($integrantes=$grupos->get($clave,collect()))
 @if($integrantes->isNotEmpty())<section class="grid gap-8 border-b border-stone-200 py-10 lg:grid-cols-[16rem_1fr]"><h2 class="text-3xl">{{ $tituloGrupo }}</h2><div class="grid gap-6 md:grid-cols-2">@foreach($integrantes as $item)<article class="border-t border-stone-300 pt-4"><h3>{{ trim($item->grado.' '.$item->nombre) }}</h3>@if($item->afiliacion)<p class="mt-2 text-sm leading-relaxed">{{ $item->afiliacion }}</p>@endif @if($item->email)<a class="mt-2 inline-block text-sm text-navy-700 underline" href="mailto:{{ $item->email }}">Correo institucional</a>@endif</article>@endforeach</div></section>@endif
@endforeach
</section>
@endsection
