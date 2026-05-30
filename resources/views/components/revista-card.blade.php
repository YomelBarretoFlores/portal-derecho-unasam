@props(['articulo'])

<article class="card-hover flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-7">
    <span class="w-fit rounded-full bg-gold-100 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-gold-600">
        {{ $articulo->categoria }}
    </span>
    <h3 class="mt-4 text-xl font-semibold leading-snug text-navy-900">{{ $articulo->titulo }}</h3>
    <p class="mt-3 grow font-sans text-sm leading-relaxed text-gray-500">{{ Str::limit($articulo->resumen, 180) }}</p>

    <div class="mt-5 font-sans text-xs text-gray-400">
        <p class="font-medium text-gray-600">{{ implode(' · ', $articulo->autores) }}</p>
        <p class="mt-1">pp. {{ $articulo->paginas }} · {{ $articulo->descargas }} descargas · DOI: {{ $articulo->doi }}</p>
    </div>

    <div class="mt-5 flex gap-2 font-sans">
        <a href="#" class="rounded-lg border border-navy-900 px-4 py-1.5 text-xs font-semibold text-navy-900 transition hover:bg-navy-900 hover:text-white">PDF</a>
        <a href="#" class="rounded-lg border border-gray-300 px-4 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-navy-900 hover:text-navy-900">HTML</a>
        <a href="#" class="rounded-lg border border-gray-300 px-4 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-navy-900 hover:text-navy-900">EPUB</a>
    </div>
</article>
