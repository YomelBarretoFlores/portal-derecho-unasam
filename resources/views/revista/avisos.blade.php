@extends('layouts.app')
@section('title','Avisos — '.$ficha->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" title="Avisos" subtitle="Convocatorias, recepción de artículos y fechas editoriales." />
<section class="mx-auto max-w-5xl px-6 py-16 md:py-20">
@if($avisos->isEmpty())<x-empty-state title="Próximamente publicaremos avisos" description="Esta sección difundirá exclusivamente convocatorias, aperturas de recepción de artículos y fechas relevantes de Derecho y Cultura." action="Consultar envíos" :href="route('revista.envios')" />
@else<div class="space-y-8">@foreach($avisos as $aviso)<article class="border-t border-stone-300 pt-6"><p class="eyebrow">{{ $aviso->fecha_publicacion?->translatedFormat('d \d\e F \d\e Y') }}</p><h2 class="mt-3 text-3xl">{{ $aviso->titulo }}</h2>@if($aviso->resumen)<p class="mt-4 leading-relaxed">{{ $aviso->resumen }}</p>@endif @if($aviso->contenido)<div class="prose-editorial mt-5">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($aviso->contenido) }}</div>@endif @if($aviso->enlace || $aviso->getFirstMediaUrl('adjunto'))<div class="mt-5 flex flex-wrap gap-3">@if($aviso->enlace)<a class="btn btn-primary" href="{{ $aviso->enlace }}" target="_blank" rel="noopener">Más información</a>@endif @if($aviso->getFirstMediaUrl('adjunto'))<a class="btn btn-ghost" href="{{ $aviso->getFirstMediaUrl('adjunto') }}" target="_blank" rel="noopener">Ver adjunto</a>@endif</div>@endif</article>@endforeach</div><div class="mt-10">{{ $avisos->links() }}</div>@endif
</section>
@endsection
