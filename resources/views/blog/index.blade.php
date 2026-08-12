@extends('layouts.app')

@section('title', 'Blog — Derecho UNASAM')
@section('description', 'Noticias, opiniones y eventos del Programa de Derecho y Ciencias Políticas de la UNASAM.')

@section('content')
    <x-page-hero seccion="Publicaciones" title="Blog" subtitle="Noticias, opiniones y eventos del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-7xl px-6 py-16">
        <nav class="flex flex-wrap gap-2" aria-label="Filtrar publicaciones por tipo">
            @foreach ([['', 'Todos'], ['noticia', 'Noticias'], ['opinion', 'Opiniones'], ['evento', 'Eventos']] as [$value, $label])
                <a href="{{ route('blog', array_filter(['tipo' => $value])) }}" wire:navigate.hover
                   @if ($tipo === $value || ($value === '' && ! in_array($tipo, ['noticia', 'opinion', 'evento'], true))) aria-current="page" @endif
                   class="filter-chip {{ ($tipo === $value || ($value === '' && ! in_array($tipo, ['noticia', 'opinion', 'evento'], true))) ? 'border-navy-900 bg-navy-900 text-white' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        @if ($posts->isEmpty())
            <x-empty-state class="mt-10" title="No hay publicaciones para este filtro" description="Prueba otra categoría o vuelve al listado completo de noticias, opiniones y eventos." action="Ver todas las publicaciones" :href="route('blog')" />
        @else
            @if ($posts->count() === 1)
                <div class="mt-10"><x-blog-card :post="$posts->first()" :horizontal="true" /></div>
            @else
                <div class="mt-10 grid gap-6 sm:grid-cols-2 {{ $posts->count() >= 3 ? 'lg:grid-cols-3' : '' }}">
                    @foreach ($posts as $post)<x-blog-card :post="$post" />@endforeach
                </div>
            @endif
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
