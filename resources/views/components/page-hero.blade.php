@props(['title', 'seccion' => 'Programa', 'subtitle' => null])

@php
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $seccion],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => url()->current()],
        ],
    ];
@endphp
@push('schema')
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

{{-- Cabecera de página interna: clara y ligera, con un único acento dorado fino --}}
<section class="border-b border-stone-200 bg-paper">
    <div class="mx-auto max-w-7xl px-6 py-16 md:py-24">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" wire:navigate.hover class="transition hover:text-navy-900">Inicio</a>
            <span class="text-stone-300">/</span>
            <span>{{ $seccion }}</span>
        </nav>
        <div class="accent-line mt-6"></div>
        <h1 class="hero-title mt-4 max-w-3xl text-4xl text-navy-900 md:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-lg leading-relaxed text-stone-600">{{ $subtitle }}</p>
        @endif
    </div>
</section>
