{{-- Sección Revista (preview) --}}
@php $featured = $revista->first(); $resto = $revista->slice(1)->take(3); @endphp
<section class="border-y border-stone-200 bg-paper py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="reveal flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">{{ $home['home_revista_eyebrow'] }}</p>
                <h2 class="text-3xl font-semibold tracking-tight text-navy-900 md:text-4xl">{{ $home['home_revista_titulo'] }}</h2>
            </div>
            @if ($revistaNumero)
                <a href="{{ route('revista.numero', $revistaNumero->slug) }}" wire:navigate.hover class="bg-navy-900 px-4 py-1.5 text-xs font-medium text-white">
                    Vol. {{ $revistaNumero->volumen }} · Núm. {{ $revistaNumero->numero }} — {{ $revistaNumero->fecha_publicacion->translatedFormat('M Y') }}
                </a>
            @endif
        </div>

        @if ($featured && $revistaNumero)
        <div class="mt-12 grid gap-8 lg:grid-cols-[1.2fr_1fr] lg:items-start">
            {{-- Featured --}}
            <article class="reveal card-hover flex flex-col rounded-2xl border border-stone-200 bg-white p-8">
                <span class="w-fit rounded-none bg-navy-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-navy-700">{{ $featured->categoria }}</span>
                <h3 class="mt-4 text-2xl font-semibold leading-snug text-navy-900">{{ $featured->titulo }}</h3>
                <p class="mt-3 grow text-[15px] leading-relaxed text-stone-500">{{ Str::limit($featured->resumen, 240) }}</p>
                <p class="mt-4 text-sm text-stone-600">{{ implode(' · ', $featured->autores) }} — pp. {{ $featured->paginas }}</p>
                @php $pdfUrl = $featured->_pdf_url ?? ''; @endphp
                @if ($pdfUrl)
                    <div class="mt-5 flex gap-2">
                        <a href="{{ $pdfUrl }}" target="_blank" rel="noopener" class="rounded-lg border border-navy-900 px-4 py-1.5 text-xs font-semibold text-navy-900 transition hover:bg-navy-900 hover:text-white">PDF</a>
                    </div>
                @endif
                <a href="{{ route('revista.articulo', [$revistaNumero->slug, $featured->slug]) }}" wire:navigate.hover class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-navy-700">Leer artículo <x-ui-icon name="arrow-right" /></a>
            </article>

            {{-- Lista --}}
            <div class="stagger-children flex flex-col gap-4">
                @foreach ($resto as $art)
                    <article class="reveal card-hover rounded-2xl border border-stone-200 bg-white p-5">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-navy-600">{{ $art->categoria }}</span>
                        <h4 class="mt-1.5 font-semibold leading-snug text-navy-900"><a href="{{ route('revista.articulo', [$revistaNumero->slug, $art->slug]) }}" wire:navigate.hover>{{ $art->titulo }}</a></h4>
                        <p class="mt-1 text-xs text-stone-500">{{ implode(' · ', $art->autores) }}</p>
                    </article>
                @endforeach
                <a href="{{ route('revista') }}" wire:navigate.hover class="link-arrow group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
                    Ver todos los artículos
                    <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </div>
        @elseif ($revistaInstitucional)
            @php
                $presentacionRevista = $revistaInstitucional->presentacion
                    ? \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revistaInstitucional->presentacion)->toText()
                    : null;
            @endphp
            <div class="mt-12 grid overflow-hidden border border-stone-200 bg-white lg:grid-cols-[minmax(0,1.35fr)_minmax(19rem,0.65fr)]">
                <article class="reveal p-8 md:p-10 lg:p-12">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-navy-600">Revista científica institucional</p>
                    <h3 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight text-navy-900 md:text-4xl">
                        {{ $revistaInstitucional->nombre_corto }}
                    </h3>
                    @if ($presentacionRevista)
                        <p class="mt-5 max-w-3xl text-[15px] leading-7 text-stone-600">
                            {{ \Illuminate\Support\Str::limit($presentacionRevista, 390) }}
                        </p>
                    @endif
                    {{-- Periodicidad y modalidad se publicaban aquí como píldoras y
                         además en la banda de credenciales de abajo; se quedan solo
                         en la banda, que es donde el lector las busca junto al resto. --}}
                    <a href="{{ route('revista') }}" wire:navigate.hover class="link-arrow group mt-8 inline-flex items-center gap-2 text-sm font-semibold text-navy-800">
                        Conocer la revista
                        <x-ui-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" />
                    </a>
                </article>

                <aside class="reveal border-t border-stone-200 bg-navy-950 p-8 text-white md:p-10 lg:border-l lg:border-t-0" aria-label="Creación oficial de la revista">
                    <span class="inline-flex border border-white/25 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/80">Información institucional</span>
                    <dl class="mt-8 space-y-6 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-white/55">Resolución</dt>
                            <dd class="mt-2 font-semibold leading-relaxed">{{ $revistaInstitucional->resolucion_numero }}</dd>
                            @if ($revistaInstitucional->resolucion_fecha)
                                <dd class="mt-1 text-white/65">{{ $revistaInstitucional->resolucion_fecha->translatedFormat('d \d\e F \d\e Y') }}</dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-white/55">Unidad responsable</dt>
                            <dd class="mt-2 leading-relaxed text-white/80">{{ $revistaInstitucional->unidad_responsable }}</dd>
                        </div>
                    </dl>
                    @if ($revistaInstitucional->_resolucion_url)
                        <a href="{{ $revistaInstitucional->_resolucion_url }}" target="_blank" rel="noopener" class="mt-8 inline-flex text-sm font-semibold underline decoration-white/40 underline-offset-4 hover:decoration-white">
                            Consultar resolución oficial <x-ui-icon name="external-link" class="ml-2 h-4 w-4" />
                        </a>
                    @endif
                </aside>
            </div>
        @else
            <x-empty-state class="mt-10 bg-white" title="Derecho y Cultura" description="Revista científica de Derecho y Antropología Jurídica de la UNASAM." />
        @endif

        @if ($revistaInstitucional)
            <x-revista-credenciales :revista="$revistaInstitucional" />
        @endif
    </div>
</section>
