@props(['articulo'])

<article class="card-hover flex h-full flex-col rounded-2xl border border-stone-200 bg-white p-7">
    <span class="w-fit rounded-full bg-navy-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-navy-700">
        {{ $articulo->categoria }}
    </span>
    <h3 class="mt-4 text-xl font-semibold leading-snug text-navy-900">{{ $articulo->titulo }}</h3>
    <p class="mt-3 grow text-sm leading-relaxed text-stone-500">{{ Str::limit($articulo->resumen, 180) }}</p>

    <div class="mt-5 text-xs text-stone-400">
        <p class="font-medium text-stone-600">{{ implode(' · ', $articulo->autores) }}</p>
        <p class="mt-1">pp. {{ $articulo->paginas }} · {{ $articulo->descargas }} descargas · DOI: {{ $articulo->doi }}</p>
    </div>

    @php $pdfUrl = $articulo->_pdf_url ?: ($articulo->relationLoaded('media') ? $articulo->getFirstMediaUrl('pdf') : ''); @endphp
    @if ($pdfUrl)
        <div class="mt-5 flex gap-2">
            <a href="{{ $pdfUrl }}" target="_blank" rel="noopener"
               class="rounded-lg border border-navy-900 px-4 py-1.5 text-xs font-semibold text-navy-900 transition hover:bg-navy-900 hover:text-white">PDF</a>
        </div>
    @endif
</article>
