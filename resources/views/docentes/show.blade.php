@extends('layouts.app')

@section('title', $docente->name.' — Personal docente — Derecho UNASAM')
@section('description', \Illuminate\Support\Str::limit($docente->resena, 155))
@section('og_type', 'profile')
@php
    $fotoPublica = $docente->fotoPublicaUrl();
    $personSchema = array_filter([
        '@'.'context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $docente->name,
        'jobTitle' => $docente->categoria,
        'description' => $docente->resena,
        'email' => $docente->email_institucional,
        'image' => $fotoPublica ? url($fotoPublica) : null,
        'affiliation' => [
            '@type' => 'CollegeOrUniversity',
            'name' => 'Universidad Nacional Santiago Antúnez de Mayolo',
        ],
        'sameAs' => array_values(array_filter([
            $docente->orcid,
            $docente->google_scholar_url,
            $docente->cti_vitae_url,
            $docente->perfil_academico_url,
        ])),
    ]);
@endphp
@section('og_image', $fotoPublica ? url($fotoPublica) : asset('img/escudo-unasam.png'))

@push('schema')
<script type="application/ld+json">{!! json_encode($personSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<x-breadcrumb-schema :items="[
    ['name' => 'Inicio', 'url' => route('home')],
    ['name' => 'Personal docente', 'url' => route('docentes')],
    ['name' => $docente->name, 'url' => url()->current()],
]" />
@endpush

@section('content')
    <article class="mx-auto max-w-6xl px-6 py-16 lg:py-24">
        <a href="{{ route('docentes') }}" wire:navigate.hover class="inline-flex items-center gap-2 text-sm font-semibold text-navy-700"><x-ui-icon name="arrow-left" /> Volver al personal docente</a>

        <div class="mt-10 grid gap-10 lg:grid-cols-[18rem_1fr] lg:gap-16">
            <aside>
                <div class="aspect-[4/5] overflow-hidden bg-stone-50 p-2">
                    @if ($fotoPublica)
                        <img src="{{ $fotoPublica }}" alt="Retrato de {{ $docente->name }}" class="h-full w-full object-contain object-top" decoding="async">
                    @else
                        <div class="flex h-full items-center justify-center text-5xl font-semibold text-white/90">{{ $docente->iniciales }}</div>
                    @endif
                </div>

                @if ($docente->email_institucional || $docente->orcid || $docente->google_scholar_url || $docente->cti_vitae_url || $docente->perfil_academico_url)
                    <div class="border-x border-b border-stone-200 p-5 text-sm">
                        <h2 class="font-sans text-xs font-semibold uppercase tracking-widest text-stone-500">Enlaces académicos</h2>
                        <ul class="mt-4 space-y-3">
                            @if ($docente->email_institucional)
                                <li><a class="font-medium text-navy-700 underline decoration-stone-300 underline-offset-4 hover:text-gold-600" href="mailto:{{ $docente->email_institucional }}">Correo institucional</a></li>
                            @endif
                            @foreach ([
                                'ORCID' => $docente->orcid,
                                'Google Scholar' => $docente->google_scholar_url,
                                'CTI Vitae' => $docente->cti_vitae_url,
                                'Perfil académico' => $docente->perfil_academico_url,
                            ] as $label => $url)
                                @if ($url)
                                    <li><a class="font-medium text-navy-700 underline decoration-stone-300 underline-offset-4 hover:text-gold-600" href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }} <span class="sr-only">(abre en una pestaña nueva)</span></a></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold-600">Personal docente</p>
                <h1 class="mt-3 text-4xl leading-tight text-navy-900 md:text-6xl">{{ $docente->name }}</h1>

                <dl class="mt-8 grid gap-px border border-stone-200 bg-stone-200 sm:grid-cols-2">
                    @foreach ([
                        'Categoría' => $docente->categoria,
                        'Dedicación' => $docente->dedicacion,
                        'Grado académico' => $docente->grado,
                        'Área de especialidad' => $docente->area,
                    ] as $label => $value)
                        @if ($value)
                            <div class="bg-white p-5">
                                <dt class="font-sans text-xs font-semibold uppercase tracking-widest text-stone-500">{{ $label }}</dt>
                                <dd class="mt-2 text-base font-medium leading-relaxed text-navy-900">{{ $value }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>

                <section class="mt-12">
                    <h2 class="text-3xl text-navy-900">Reseña académica</h2>
                    <div class="mt-5 whitespace-pre-line text-lg leading-8 text-stone-600">{{ $docente->resena }}</div>
                </section>

                @if (filled($docente->publicaciones))
                    <section class="mt-12 border-t border-stone-200 pt-10">
                        <h2 class="text-3xl text-navy-900">Publicaciones destacadas</h2>
                        <ul class="mt-5 space-y-4">
                            @foreach ($docente->publicaciones as $publicacion)
                                <li class="border-l-2 border-gold-500 pl-5 text-base leading-relaxed text-stone-600">
                                    @if (! empty($publicacion['url']))
                                        <a href="{{ $publicacion['url'] }}" target="_blank" rel="noopener"
                                           class="font-medium text-navy-900 underline underline-offset-2 transition hover:text-navy-600">{{ $publicacion['titulo'] ?? '' }}</a>
                                    @else
                                        <span class="font-medium text-navy-900">{{ $publicacion['titulo'] ?? '' }}</span>
                                    @endif
                                    @if (! empty($publicacion['anio'])) <span>({{ $publicacion['anio'] }})</span> @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        </div>
    </article>
@endsection
