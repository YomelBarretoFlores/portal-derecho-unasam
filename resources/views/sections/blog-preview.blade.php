{{-- Sección Blog (preview, variante Featured) --}}
@php $main = $posts->first(); $lista = $posts->slice(1)->take(3); $totalPosts = $posts->count(); @endphp
<section class="mx-auto max-w-7xl px-6 py-20">
    <div class="reveal flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-semibold tracking-tight text-navy-900 md:text-4xl">{{ $home['home_blog_titulo'] }}</h2>
        </div>
        <a href="{{ route('blog') }}" wire:navigate.hover class="link-arrow group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
            Ver todo el blog
            <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
    </div>

    @if ($main)
    @if ($totalPosts === 1)
        <div class="mt-10">
            <x-reveal><x-blog-card :post="$main" :horizontal="true" /></x-reveal>
        </div>
    @else
    <div class="mt-12 grid gap-8 {{ $totalPosts === 2 ? 'lg:grid-cols-[1.2fr_0.8fr]' : 'lg:grid-cols-2' }} lg:items-start">
        {{-- Destacado --}}
        <x-reveal>
            <x-blog-card :post="$main" :featured="true" />
        </x-reveal>

        {{-- Lista --}}
        <div class="stagger-children flex flex-col gap-5">
            @foreach ($lista as $post)
                <div class="reveal">
                    <x-blog-card :post="$post" />
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @else
        <x-empty-state class="mt-10" title="Actualidad en preparación" description="Las noticias, opiniones y eventos aparecerán aquí después de su revisión editorial." action="Consultar comunicados" :href="route('comunicados')" />
    @endif
</section>
