@extends('layouts.app')

@section('title', 'Página no encontrada — Derecho UNASAM')
@section('robots', 'noindex, nofollow')

@section('content')
    <section class="mx-auto flex min-h-[60vh] max-w-3xl flex-col justify-center px-6 py-20">
        <p class="text-sm font-semibold uppercase tracking-widest text-gold-600">Error 404</p>
        <h1 class="mt-4 text-5xl leading-tight md:text-7xl">La página que buscas no está disponible.</h1>
        <p class="mt-6 max-w-xl text-lg leading-relaxed text-stone-600">Puede tratarse de contenido todavía no publicado, un enlace anterior o una dirección incorrecta.</p>
        <div class="mt-8 flex flex-wrap gap-4">
            <a class="btn btn-primary" href="{{ route('home') }}">Volver al inicio</a>
            <a class="btn btn-ghost" href="{{ route('documentos') }}">Consultar documentos</a>
        </div>
    </section>
@endsection
