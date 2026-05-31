<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $ajustes['seo_title'] ?? 'Derecho y Ciencias Políticas — UNASAM')</title>
    <meta name="description" content="@yield('description', $ajustes['seo_description'] ?? 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.')">
    <link rel="icon" href="{{ asset('img/escudo-unasam.png') }}">

    {{-- Tipografía Outfit (Bunny Fonts — sin rastreo, amigable con privacidad) --}}
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800">

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white text-stone-600 antialiased">

    <x-nav />

    <main class="flex-1">
        @yield('content')
    </main>

    <x-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>
