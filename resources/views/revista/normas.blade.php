@extends('layouts.app')
@section('title','Normas para autores — '.$revista->nombre_corto)
@section('description','Requisitos oficiales de presentación, evaluación y publicación de manuscritos.')
@section('content')
<x-page-hero seccion="Revista" title="Normas para autores" subtitle="El PDF aprobado es la fuente normativa autoritativa." />
<section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
    <div class="grid gap-10 lg:grid-cols-[1fr_20rem]">
        <div>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach([['Formato','A4 · Garamond 12 · interlineado 1.5'],['Títulos','Garamond 13 · máximo 15 palabras'],['Márgenes','Izquierdo 3 cm · restantes 2.5 cm'],['Resumen','Máximo 200 palabras · 3 a 5 palabras clave'],['Autoría','Máximo cuatro autores'],['Evaluación','Revisión por pares doble ciego'],['Citación','APA, 7.ª edición'],['Periodicidad','Semestral']] as [$label,$value])
                    <div class="border-t border-stone-300 pt-4"><p class="eyebrow">{{ $label }}</p><p class="mt-2 font-semibold text-navy-900">{{ $value }}</p></div>
                @endforeach
            </div>
            <div class="prose-editorial mt-12">
                <h2>Extensión por tipo de contribución</h2>
                <ul><li>Artículo original: 5,000 a 6,000 palabras.</li><li>Artículo de revisión: 4,000 a 5,000 palabras y al menos 30 referencias.</li><li>Reseña: 1,000 a 1,500 palabras.</li></ul>
                <h2>Documentos requeridos</h2>
                <ol><li>Carta de presentación.</li><li>Manuscrito en Word usando la plantilla editorial.</li><li>Declaración de originalidad y cesión.</li><li>Constancia de corrección de estilo.</li></ol>
                @if($revista->normas_publicacion){{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->normas_publicacion) }}@endif
            </div>
        </div>
        <aside class="h-fit border-t-2 border-navy-900 bg-paper p-6 lg:sticky lg:top-6"><p class="eyebrow">Documento completo</p><p class="mt-4 text-sm leading-relaxed">Consulta el texto oficial aprobado. Ante cualquier diferencia con otro archivo, prevalece este PDF.</p>@if($documento?->download_url)<a class="btn btn-primary mt-6 w-full" href="{{ $documento->download_url }}" target="_blank" rel="noopener">Abrir PDF</a><a class="btn btn-ghost mt-3 w-full" href="{{ $documento->download_url }}" download>Descargar PDF</a>@endif<a class="mt-5 block text-center text-sm font-semibold text-navy-700 underline" href="{{ route('revista.formatos') }}">Formatos y plantillas</a></aside>
    </div>
    @if($documento?->download_url)<div class="mt-14 border border-stone-200"><iframe title="Normas de publicación completas" src="{{ $documento->download_url }}#view=FitH" class="h-[70vh] min-h-[34rem] w-full"></iframe></div>@endif
</section>
@endsection
