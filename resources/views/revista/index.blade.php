@extends('layouts.app')

@php
    $nombre = $revista?->nombre_corto ?: 'Derecho y Cultura';
    $descripcion = $revista?->presentacion
        ? \Illuminate\Support\Str::limit(\Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->presentacion)->toText(), 155)
        : 'Revista Científica de Derecho y Antropología Jurídica de la UNASAM.';
    $resolucionUrl = $revista?->_resolucion_url ?? '';
    if (! $resolucionUrl && $revista && method_exists($revista, 'getFirstMediaUrl')) {
        $resolucionUrl = $revista->getFirstMediaUrl('resolucion');
    }
    $idiomas = collect($revista?->idiomas ?? [])->map(fn (string $idioma) => match ($idioma) {
        'es' => 'Español',
        'en' => 'Inglés',
        default => strtoupper($idioma),
    })->join(' e ');
@endphp

@section('title', $nombre.' — UNASAM')
@section('description', $descripcion)

@if ($revista)
    @push('schema')
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Periodical',
                'name' => $revista->nombre,
                'alternateName' => $revista->nombre_corto,
                'description' => $descripcion,
                'url' => route('revista'),
                'issn' => $revista->issn,
                'inLanguage' => $revista->idiomas,
                'isAccessibleForFree' => true,
                'publisher' => [
                    '@type' => 'CollegeOrUniversity',
                    'name' => 'Universidad Nacional Santiago Antúnez de Mayolo',
                ],
            ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endpush
@endif

@section('content')
    <x-page-hero
        seccion="Publicaciones"
        :title="$nombre"
        :subtitle="$revista?->nombre ?: 'Revista Científica de Derecho y Antropología Jurídica'"
    />

    <section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
        @if ($revista)
            <div class="grid gap-12 border-b border-stone-200 pb-16 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-20">
                <div>
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center border border-navy-900 bg-navy-950 font-serif text-xl font-bold text-gold-300" aria-label="Monograma tipográfico Derecho y Cultura">D&amp;C</div>
                        <div>
                            <p class="eyebrow">Presentación</p>
                            <p class="mt-1 text-sm text-stone-500">Revista científica institucional · UNASAM</p>
                        </div>
                    </div>
                    @if ($revista->presentacion)
                        <div class="prose-editorial mt-7 max-w-3xl">
                            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->presentacion) }}
                        </div>
                    @else
                        <p class="mt-5 text-stone-500">La presentación todavía está pendiente de completar.</p>
                    @endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('revista.normas') }}" wire:navigate.hover class="btn btn-primary">Normas para autores</a>
                        <a href="{{ route('revista.equipo') }}" wire:navigate.hover class="btn btn-ghost">Equipo editorial</a>
                    </div>
                </div>

                <aside class="border-t-2 border-navy-900 bg-paper p-6" aria-label="Ficha institucional de la revista">
                    <p class="eyebrow">Ficha institucional</p>
                    <dl class="mt-5 space-y-5 text-sm">
                        <div>
                            <dt class="font-semibold text-navy-900">Unidad responsable</dt>
                            <dd class="mt-1 leading-relaxed text-stone-600">{{ $revista->unidad_responsable }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-navy-900">Resolución de creación</dt>
                            <dd class="mt-1 text-stone-600">{{ $revista->resolucion_numero }}</dd>
                            <dd class="text-stone-500">{{ $revista->resolucion_fecha?->translatedFormat('d \d\e F \d\e Y') ?: 'Fecha pendiente' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-navy-900">Contacto editorial</dt>
                            <dd class="mt-1"><a class="text-navy-700 underline underline-offset-4" href="mailto:{{ $revista->contacto_email }}">{{ $revista->contacto_email }}</a></dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-navy-900">ISSN en línea</dt>
                            <dd class="mt-1 text-stone-600">{{ $revista->issn ?: 'Pendiente de asignación' }}</dd>
                        </div>
                    </dl>
                    @if ($resolucionUrl)
                        <a href="{{ $resolucionUrl }}" target="_blank" rel="noopener" class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-navy-800 underline underline-offset-4">
                            Consultar resolución oficial <span aria-hidden="true">↗</span>
                        </a>
                    @endif
                </aside>
            </div>

            <dl class="grid border-b border-stone-200 sm:grid-cols-2 lg:grid-cols-4">
                <div class="border-stone-200 py-7 sm:border-r sm:pr-6">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Periodicidad</dt>
                    <dd class="mt-2 font-semibold text-navy-900">{{ $revista->periodicidad ?: 'Pendiente' }}</dd>
                </div>
                <div class="border-t border-stone-200 py-7 sm:border-t-0 sm:px-6 lg:border-r">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Modalidad</dt>
                    <dd class="mt-2 font-semibold text-navy-900">{{ $revista->modalidad }}</dd>
                </div>
                <div class="border-t border-stone-200 py-7 sm:border-r sm:px-6 lg:border-t-0">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Evaluación</dt>
                    <dd class="mt-2 font-semibold text-navy-900">Doble ciego</dd>
                </div>
                <div class="border-t border-stone-200 py-7 sm:px-6 lg:border-t-0 lg:pr-0">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Idiomas</dt>
                    <dd class="mt-2 font-semibold text-navy-900">{{ $idiomas }}</dd>
                </div>
            </dl>

            @if ($revista->enfoque_alcance)
                <div class="grid gap-10 py-16 lg:grid-cols-[16rem_minmax(0,1fr)] lg:gap-20">
                    <div>
                        <p class="eyebrow">Identidad académica</p>
                        <h2 class="mt-3 text-3xl md:text-4xl">Enfoque y alcance</h2>
                    </div>
                    <div class="prose-editorial max-w-3xl">
                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->enfoque_alcance) }}
                    </div>
                </div>
            @endif
        @endif

        <div class="border-t border-stone-200 pt-14">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="eyebrow">Archivo editorial</p>
                    <h2 class="mt-3 text-3xl md:text-4xl">Números publicados</h2>
                </div>
                <form method="get" class="flex w-full max-w-xl gap-3" role="search">
                    <label class="sr-only" for="q-revista">Buscar número</label>
                    <input id="q-revista" name="q" value="{{ $q }}" type="search" placeholder="Buscar por título…" class="min-w-0 flex-1 border border-stone-300 px-4 py-3 text-sm focus:border-navy-700">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </form>
            </div>

            @if ($numeros->isEmpty())
                <x-empty-state class="mt-10" title="El primer número está pendiente" description="La revista cuenta con ficha institucional y normas editoriales verificadas. Los números aparecerán aquí al concluir su proceso de evaluación y publicación." action="Consultar normas para autores" :href="route('revista.normas')" />
            @else
                <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($numeros as $numero)
                        @php
                            $portadaUrl = $numero->_portada_url ?? '';
                            if (! $portadaUrl && method_exists($numero, 'getFirstMediaUrl')) {
                                $portadaUrl = $numero->getFirstMediaUrl('portada');
                            }
                        @endphp
                        <article class="card-hover border border-stone-200 bg-white">
                            @if ($portadaUrl)<img src="{{ $portadaUrl }}" alt="Portada de {{ $numero->titulo }}" class="aspect-[4/5] w-full object-cover">@endif
                            <div class="p-6">
                                <p class="text-xs font-semibold uppercase tracking-wider text-navy-600">Vol. {{ $numero->volumen }} · Núm. {{ $numero->numero }}</p>
                                <h3 class="mt-3 text-2xl leading-tight"><a href="{{ route('revista.numero', $numero) }}" wire:navigate.hover>{{ $numero->titulo }}</a></h3>
                                @if ($numero->descripcion)<p class="mt-3 text-sm leading-relaxed text-stone-500">{{ \Illuminate\Support\Str::limit($numero->descripcion, 170) }}</p>@endif
                                <time class="mt-5 block text-xs text-stone-400">{{ $numero->fecha_publicacion?->translatedFormat('F Y') ?: 'Fecha pendiente' }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="mt-10">{{ $numeros->links() }}</div>
            @endif
        </div>
    </section>
@endsection
