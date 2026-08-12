@extends('layouts.app')

@section('title', 'Equipo editorial — '.$revista->nombre_corto)
@section('description', 'Dirección, editores, comité editorial, consejo científico y revisores de la revista '.$revista->nombre_corto.'.')

@section('content')
    <x-page-hero seccion="Revista" title="Equipo editorial" :subtitle="$revista->nombre" />

    <section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
        <div class="flex flex-wrap gap-3 border-b border-stone-200 pb-8">
            <a href="{{ route('revista') }}" wire:navigate.hover class="btn btn-ghost">Volver a la revista</a>
            <a href="{{ route('revista.normas') }}" wire:navigate.hover class="btn btn-primary">Normas para autores</a>
        </div>

        @foreach (\App\Models\RevistaMiembro::GRUPOS as $clave => $titulo)
            @php $integrantes = $grupos->get($clave, collect()); @endphp
            @if ($integrantes->isNotEmpty())
                <section class="grid gap-8 border-b border-stone-200 py-12 lg:grid-cols-[16rem_minmax(0,1fr)] lg:gap-20" aria-labelledby="grupo-{{ $clave }}">
                    <div>
                        <p class="eyebrow">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h2 id="grupo-{{ $clave }}" class="mt-3 text-3xl">{{ $titulo }}</h2>
                    </div>
                    <div class="grid gap-x-10 gap-y-8 md:grid-cols-2">
                        @foreach ($integrantes as $integrante)
                            <article class="border-t border-stone-300 pt-4">
                                <h3 class="text-lg">{{ trim($integrante->grado.' '.$integrante->nombre) }}</h3>
                                @if ($integrante->afiliacion)
                                    <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $integrante->afiliacion }}</p>
                                @endif
                                @if ($integrante->pais)
                                    <p class="mt-1 text-xs uppercase tracking-wider text-stone-400">{{ $integrante->pais }}</p>
                                @endif
                                @if ($integrante->orcid || $integrante->email)
                                    <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                        @if ($integrante->orcid)<span>ORCID: {{ $integrante->orcid }}</span>@endif
                                        @if ($integrante->email)<a class="text-navy-700 underline" href="mailto:{{ $integrante->email }}">Correo</a>@endif
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <p class="mt-8 text-sm leading-relaxed text-stone-500">Equipo aprobado como parte del proyecto de creación incorporado en la Resolución {{ rtrim($revista->resolucion_numero, '.') }}. Las afiliaciones se muestran conforme al documento institucional.</p>
    </section>
@endsection
