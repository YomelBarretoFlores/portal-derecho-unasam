{{-- Sección Revista (preview) --}}
@php $featured = $revista->first(); $resto = $revista->slice(1)->take(3); @endphp
<section class="border-y border-stone-200 bg-paper py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="reveal flex flex-wrap items-end justify-between gap-4">
            <div>
                <span class="eyebrow">{{ $home['home_revista_eyebrow'] }}</span>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-navy-900 md:text-4xl">{{ $home['home_revista_titulo'] }}</h2>
            </div>
            <span class="rounded-full bg-navy-900 px-4 py-1.5 text-xs font-medium text-white">
                {{ $home['home_revista_badge'] }}
            </span>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-[1.2fr_1fr]">
            {{-- Featured --}}
            <article class="reveal card-hover flex flex-col rounded-2xl border border-stone-200 bg-white p-8">
                <span class="w-fit rounded-full bg-navy-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-navy-700">{{ $featured->categoria }}</span>
                <h3 class="mt-4 text-2xl font-semibold leading-snug text-navy-900">{{ $featured->titulo }}</h3>
                <p class="mt-3 grow text-[15px] leading-relaxed text-stone-500">{{ Str::limit($featured->resumen, 240) }}</p>
                <p class="mt-4 text-sm text-stone-600">{{ implode(' · ', $featured->autores) }} — pp. {{ $featured->paginas }}</p>
                <div class="mt-5 flex gap-2">
                    <a href="#" class="rounded-lg border border-navy-900 px-4 py-1.5 text-xs font-semibold text-navy-900 transition hover:bg-navy-900 hover:text-white">PDF</a>
                    <a href="#" class="rounded-lg border border-stone-300 px-4 py-1.5 text-xs font-semibold text-stone-600 transition hover:border-navy-900 hover:text-navy-900">HTML</a>
                </div>
            </article>

            {{-- Lista --}}
            <div class="reveal flex flex-col gap-4" data-reveal-delay="0.12">
                @foreach ($resto as $art)
                    <article class="card-hover rounded-2xl border border-stone-200 bg-white p-5">
                        <span class="text-[11px] font-semibold uppercase tracking-wide text-navy-600">{{ $art->categoria }}</span>
                        <h4 class="mt-1.5 font-semibold leading-snug text-navy-900">{{ $art->titulo }}</h4>
                        <p class="mt-1 text-xs text-stone-500">{{ implode(' · ', $art->autores) }}</p>
                    </article>
                @endforeach
                <a href="{{ route('revista') }}" wire:navigate class="group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
                    Ver todos los artículos
                    <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
