@props(['title', 'seccion' => null, 'subtitle' => null, 'variant' => null])

@php
    // Si no se indica sección, se deduce de la ruta actual. Así la miga de pan
    // nombra el mismo grupo por el que el visitante llegó desde el menú.
    $seccion ??= \App\Support\Navegacion::grupoDe(request()->route()?->getName()) ?? 'La Facultad';

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $seccion],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $title, 'item' => url()->current()],
        ],
    ];
    // La revista lleva cabecera oscura en todas sus secciones: es una
    // publicación con identidad propia dentro del sitio, como hacen Harvard Law
    // Review o la propia Derecho PUCP con su cabecera de color.
    $variant ??= match ($seccion) {
        'Revista' => 'publication',
        'Estudiantes' => 'academic',
        default => 'institutional',
    };
@endphp
@push('schema')
    <script type="application/ld+json">
        {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

<section @class([
    'relative overflow-hidden border-b border-stone-200',
    'bg-paper' => $variant !== 'publication',
    'bg-navy-950' => $variant === 'publication',
])>
    @if ($variant === 'institutional')
        <div class="absolute inset-y-0 right-0 hidden w-[38%] lg:block">
            <picture>
                {{-- Aquí va al 20 % de opacidad como textura de fondo, así que
                     basta con la versión pequeña en cualquier pantalla. --}}
                <source srcset="{{ asset('img/campus-derecho-800.webp') }}" type="image/webp">
                <img src="{{ asset('img/campus-derecho.jpg') }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover opacity-20" aria-hidden="true">
            </picture>
            <div class="absolute inset-0 bg-gradient-to-r from-paper via-paper/80 to-paper/20"></div>
        </div>
    @elseif ($variant === 'academic')
        <div class="paper-grid absolute inset-0 opacity-70" aria-hidden="true"></div>
    @else
        <div class="absolute right-6 top-1/2 hidden -translate-y-1/2 font-serif text-[9rem] font-bold leading-none text-white/[0.035] md:block" aria-hidden="true">D&amp;C</div>
    @endif

    <div class="relative mx-auto max-w-7xl px-6 py-14 md:py-20">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" wire:navigate.hover class="transition {{ $variant === 'publication' ? 'text-white/55 hover:text-white' : 'hover:text-navy-900' }}">Inicio</a>
            <span aria-hidden="true" @class(['text-white/25' => $variant === 'publication', 'text-stone-300' => $variant !== 'publication'])>/</span>
            <span @class(['text-white/55' => $variant === 'publication'])>{{ $seccion }}</span>
            <span aria-hidden="true" @class(['text-white/25' => $variant === 'publication', 'text-stone-300' => $variant !== 'publication'])>/</span>
            <span class="truncate {{ $variant === 'publication' ? 'text-white/80' : 'text-navy-800' }}">{{ $title }}</span>
        </nav>
        <div class="accent-line mt-6"></div>
        <h1 class="hero-title mt-4 max-w-3xl text-4xl md:text-5xl {{ $variant === 'publication' ? 'text-white' : 'text-navy-900' }}">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-lg leading-relaxed {{ $variant === 'publication' ? 'text-white/70' : 'text-stone-600' }}">{{ $subtitle }}</p>
        @endif
    </div>
</section>
