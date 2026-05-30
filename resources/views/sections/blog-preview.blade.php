{{-- Sección Blog (preview, variante Featured) --}}
@php $main = $posts->first(); $lista = $posts->slice(1)->take(3); @endphp
<section class="mx-auto max-w-7xl px-6 py-24">
    <div class="reveal flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="eyebrow">Actualidad</span>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-navy-900 md:text-4xl">Noticias, opiniones y eventos</h2>
        </div>
        <a href="{{ route('blog') }}" wire:navigate class="group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
            Ver todo el blog
            <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>

    <div class="mt-12 grid gap-8 lg:grid-cols-2">
        {{-- Destacado --}}
        <x-reveal>
            <x-blog-card :post="$main" class="!p-8" />
        </x-reveal>

        {{-- Lista --}}
        <div class="reveal flex flex-col gap-5" data-reveal-delay="0.12">
            @foreach ($lista as $post)
                <x-blog-card :post="$post" />
            @endforeach
        </div>
    </div>
</section>
