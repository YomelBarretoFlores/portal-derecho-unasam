@extends('layouts.app')

@section('title', 'Revista Jurídica — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Publicaciones" title="Revista Jurídica UNASAM"
        subtitle="Vol. 1 · Núm. 1 — Marzo 2026 · Investigación jurídica original de la facultad." />

    <section class="mx-auto max-w-7xl px-6 py-16"
             x-data="{
                q: '',
                cat: 'all',
                items: @js($articulos->map(fn ($a) => ['cat' => $a->categoria, 'text' => Str::lower($a->titulo.' '.implode(' ', $a->autores))])->values()),
                get visibles() {
                    return this.items.filter(i => (this.cat === 'all' || i.cat === this.cat) && i.text.includes(this.q.toLowerCase())).length;
                }
             }">
        <div class="grid gap-10 lg:grid-cols-[280px_1fr]">

            {{-- Sidebar filtros --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <label class="block">
                    <span class="font-sans text-xs font-bold uppercase tracking-wide text-stone-400">Buscar</span>
                    <input x-model="q" type="search" placeholder="Título o autor…"
                           class="mt-2 w-full rounded-xl border border-stone-300 px-4 py-2.5 font-sans text-sm focus:border-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-700/10">
                </label>

                <div class="mt-6">
                    <span class="font-sans text-xs font-bold uppercase tracking-wide text-stone-400">Categorías</span>
                    <div class="mt-3 flex flex-col gap-1">
                        <button @click="cat = 'all'"
                                :class="cat === 'all' ? 'bg-navy-900 text-white' : 'text-stone-600 hover:bg-stone-100'"
                                class="rounded-lg px-3 py-2 text-left font-sans text-sm transition">Todas</button>
                        @foreach ($categorias as $c)
                            <button @click="cat = @js($c)"
                                    :class="cat === @js($c) ? 'bg-navy-900 text-white' : 'text-stone-600 hover:bg-stone-100'"
                                    class="rounded-lg px-3 py-2 text-left font-sans text-sm transition">{{ $c }}</button>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- Grid de artículos --}}
            <div>
                <p class="mb-6 font-sans text-sm text-stone-400" x-text="visibles + ' artículo(s)'"></p>
                <div x-ref="grid" class="grid gap-6 sm:grid-cols-2">
                    @foreach ($articulos as $art)
                        <div data-art
                             data-cat="{{ $art->categoria }}"
                             data-text="{{ Str::lower($art->titulo . ' ' . implode(' ', $art->autores)) }}"
                             x-show="(cat === 'all' || @js($art->categoria) === cat) && $el.dataset.text.includes(q.toLowerCase())">
                            <x-revista-card :articulo="$art" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
