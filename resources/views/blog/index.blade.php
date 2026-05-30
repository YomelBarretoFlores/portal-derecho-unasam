@extends('layouts.app')

@section('title', 'Blog — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Publicaciones" title="Blog"
        subtitle="Noticias, opiniones y eventos del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-7xl px-6 py-16" x-data="{ tipo: 'all' }">
        {{-- Filtros --}}
        <div class="flex flex-wrap gap-2">
            @foreach ([['all', 'Todos'], ['noticia', 'Noticias'], ['opinion', 'Opiniones'], ['evento', 'Eventos']] as [$val, $label])
                <button @click="tipo = @js($val)"
                        :class="tipo === @js($val) ? 'bg-navy-900 text-white' : 'border border-gray-300 text-gray-600 hover:border-navy-900'"
                        class="rounded-full px-5 py-2 font-sans text-sm font-medium transition">{{ $label }}</button>
            @endforeach
        </div>

        {{-- Grid --}}
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <div x-show="tipo === 'all' || tipo === @js($post->tipo)"
                     x-transition.opacity>
                    <x-blog-card :post="$post" />
                </div>
            @endforeach
        </div>
    </section>
@endsection
