@extends('layouts.app')

@section('title', 'Página no encontrada — Derecho UNASAM')
@section('robots', 'noindex, nofollow')

@php
    // Clave propia, no la del hero: aquí la mascota cae desde arriba y la
    // versión volando encaja; la de pie, cayendo del cielo, no. Si el campo
    // está vacío —o la facultad decide retirarla— esta página vuelve sola a
    // ser solo texto, que es como estaba y como debe seguir funcionando.
    // El try/catch no es adorno: esta plantilla también se dibuja cuando algo
    // va mal. Si la base de datos no responde, una consulta aquí convertiría
    // un 404 honesto en un 500, y el visitante dejaría de ver siquiera el
    // enlace para volver al inicio.
    try {
        $mascota = (string) \App\Models\Setting::get('error404_mascota_url', '');
    } catch (\Throwable) {
        $mascota = '';
    }
@endphp

@section('content')
    <section @class([
        'mx-auto flex min-h-[60vh] px-6 py-20',
        'max-w-5xl flex-col items-center gap-12 lg:flex-row lg:justify-between lg:gap-16' => filled($mascota),
        'max-w-3xl flex-col justify-center' => blank($mascota),
    ])>
        <div class="{{ filled($mascota) ? 'lg:max-w-2xl' : '' }}">
            <p class="text-sm font-semibold uppercase tracking-widest text-gold-600">Error 404</p>
            <h1 class="mt-4 text-5xl leading-tight md:text-7xl">La página que buscas no está disponible.</h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-stone-600">Puede tratarse de contenido todavía no publicado, un enlace anterior o una dirección incorrecta.</p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a class="btn btn-primary" href="{{ route('home') }}">Volver al inicio</a>
                <a class="btn btn-ghost" href="{{ route('documentos') }}">Consultar documentos</a>
            </div>
        </div>

        @if (filled($mascota))
            {{-- Decorativa: el mensaje ya está completo en el texto de al lado, así
                 que anunciarla a un lector de pantalla solo estorbaría. --}}
            <div class="mascota-cae w-48 shrink-0 sm:w-56 lg:w-72" aria-hidden="true">
                <img src="{{ $mascota }}" alt="" width="288" height="288" class="w-full" decoding="async">
            </div>
        @endif
    </section>
@endsection
