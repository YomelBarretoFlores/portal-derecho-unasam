<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Derecho y Ciencias Políticas — UNASAM')</title>
    <meta name="description" content="@yield('description', 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.')">
    <link rel="icon" href="{{ asset('img/escudo-unasam.png') }}">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-700 antialiased">

    <x-nav />

    <main>
        @yield('content')
    </main>

    <x-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>
