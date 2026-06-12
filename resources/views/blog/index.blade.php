@extends('layouts.app')

@section('title', 'Blog — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Publicaciones" title="Blog"
        subtitle="Noticias, opiniones y eventos del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-7xl px-6 py-16"
             x-data="{ tipo: 'all', tipos: @js($posts->pluck('tipo')->unique()->values()) }">
        @if ($posts->isEmpty())
            <div class="mx-auto max-w-2xl rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-16 text-center">
                <p class="font-sans text-stone-500">Aún no hay publicaciones. Vuelve pronto para noticias, opiniones y eventos del programa.</p>
            </div>
        @else
            {{-- Filtros --}}
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filtrar publicaciones por tipo">
                @foreach ([['all', 'Todos'], ['noticia', 'Noticias'], ['opinion', 'Opiniones'], ['evento', 'Eventos']] as [$val, $label])
                    <button @click="tipo = @js($val)" :aria-pressed="tipo === @js($val)"
                            :class="tipo === @js($val) ? 'bg-navy-900 text-white' : 'border border-stone-300 text-stone-600 hover:border-navy-900'"
                            class="rounded-none px-5 py-2.5 font-sans text-sm font-medium transition">{{ $label }}</button>
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

            {{-- Sin resultados para el filtro activo --}}
            <p x-show="tipo !== 'all' && ! tipos.includes(tipo)" x-cloak
               role="status" aria-live="polite"
               class="mt-10 rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-12 text-center font-sans text-sm text-stone-500">
                No hay publicaciones de este tipo por ahora.
            </p>
        @endif
    </section>
@endsection
