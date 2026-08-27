<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="view-transition" content="same-origin">
    <title>@yield('title', $ajustes['seo_title'] ?? 'Derecho y Ciencias Políticas — UNASAM')</title>
    <meta name="description" content="@yield('description', $ajustes['seo_description'] ?? 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.')">
    <meta name="robots" content="{{ ($previewMode ?? false) ? 'noindex, nofollow' : trim($__env->yieldContent('robots', 'index, follow')) }}">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Derecho y Ciencias Políticas — UNASAM">
    <meta property="og:locale" content="es_PE">
    <meta property="og:title" content="@yield('title', $ajustes['seo_title'] ?? 'Derecho y Ciencias Políticas — UNASAM')">
    <meta property="og:description" content="@yield('description', $ajustes['seo_description'] ?? 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('img/escudo-unasam.png'))">
    <link rel="icon" href="{{ asset('img/escudo-unasam.png') }}">

    {{-- Tipografía: Outfit (cuerpo/UI) + EB Garamond (títulos) — Bunny Fonts, sin rastreo --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=eb-garamond:500,600,700&family=outfit:300,400,500,600,700,800&display=swap">

    {{-- Datos estructurados de la organización (schema.org) --}}
    @php
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollegeOrUniversity',
            'name' => 'Programa de Estudios de Derecho y Ciencias Políticas — UNASAM',
            'url' => url('/'),
            'logo' => asset('img/escudo-unasam.png'),
            'parentOrganization' => [
                '@type' => 'CollegeOrUniversity',
                'name' => 'Universidad Nacional Santiago Antúnez de Mayolo',
                'url' => 'https://unasam.edu.pe',
            ],
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'streetAddress' => $ajustes['contacto_direccion'] ?? null,
                'addressLocality' => 'Huaraz',
                'addressRegion' => 'Áncash',
                'addressCountry' => 'PE',
            ]),
            'telephone' => $ajustes['contacto_telefono'] ?? null,
            'email' => $ajustes['contacto_email'] ?? null,
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('schema')

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white text-stone-600 antialiased">

    @if ($previewMode ?? false)
        <x-preview-banner />
    @endif

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-navy-900 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white focus:shadow-card-lg">
        Saltar al contenido principal
    </a>

    <x-contact-topbar :ajustes="$ajustes" />
    <x-nav />

    @if (request()->routeIs('revista*'))
        @include('revista.partials.nav')
    @endif

    <main id="main-content" class="flex-1">
        <x-content-provenance />
        @yield('content')
    </main>

    @include('sections.marquee')
    <x-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>
