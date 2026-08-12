@extends('layouts.app')

@section('title', $titulo . ' — Estadísticas Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="Estadísticas"
        subtitle="Indicadores académicos históricos del Programa de Derecho y Ciencias Políticas." />

    <section class="mx-auto max-w-7xl px-6 py-16"
             x-data="{ active: @js($tipo), change(key, url) { this.active = key; window.history.replaceState({}, '', url); } }">
        <div class="flex flex-wrap gap-2" role="tablist" aria-label="Indicador estadístico">
            @foreach ($tipos as $key => $label)
                <a href="{{ route('estadisticas', $key) }}"
                   role="tab"
                   :aria-selected="(active === @js($key)).toString()"
                   @click.prevent="change(@js($key), @js(route('estadisticas', $key)))"
                   class="filter-chip"
                   :class="active === @js($key) ? 'bg-navy-900 border-navy-900 text-white' : 'text-stone-600 hover:border-navy-900'">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <p class="mt-4 text-sm text-stone-500">Los indicadores se cargan una sola vez. Cambiar de pestaña no realiza nuevas consultas a Neon.</p>

        @foreach ($tipos as $key => $label)
            @php
                $serie = $series[$key];
                $max = max(1, (int) $serie->max('total'));
            @endphp
            <div x-show="active === @js($key)" x-cloak role="tabpanel" class="mt-12">
                <h2 class="sr-only">{{ $label }}</h2>
                <div class="grid gap-12 lg:grid-cols-[minmax(20rem,0.85fr)_minmax(0,1.15fr)]">
                    <div>
                        <h3 class="text-xl font-semibold text-navy-900">{{ $label }} por año</h3>
                        <div class="mt-5 overflow-x-auto border-t-2 border-navy-900">
                            <table class="w-full min-w-[20rem] font-sans text-sm">
                                <caption class="sr-only">{{ $label }} por año.</caption>
                                <thead><tr class="border-b-2 border-stone-200 text-left text-stone-400"><th class="pb-3 font-semibold">Año</th><th class="pb-3 font-semibold">Total</th><th class="pb-3 text-right font-semibold">Variación</th></tr></thead>
                                <tbody>
                                    @foreach ($serie as $i => $punto)
                                        @php $prev = $i > 0 ? $serie[$i - 1]->total : null; $var = $prev ? round(($punto->total - $prev) / $prev * 100, 1) : null; @endphp
                                        <tr class="border-b border-stone-100">
                                            <td class="py-3 font-medium text-navy-900">{{ $punto->anio }}</td>
                                            <td class="py-3 text-stone-600">{{ $punto->total }}</td>
                                            <td class="py-3 text-right">@if ($var !== null)<span class="font-medium {{ $var >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $var >= 0 ? '+' : '' }}{{ $var }}%</span>@else<span class="text-stone-300">—</span>@endif</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-navy-900">Evolución</h3>
                        <div class="mt-6 overflow-x-auto pb-2">
                        <div class="flex min-w-[28rem] items-end justify-between gap-3 border-b border-stone-200" style="height:260px">
                            @foreach ($serie as $punto)
                                <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                                    <span class="font-sans text-xs font-semibold text-navy-700">{{ $punto->total }}</span>
                                    <div class="w-full border-t-2 border-gold-400 bg-navy-800" style="height: {{ round($punto->total / $max * 100) }}%"></div>
                                    <span class="font-sans text-xs text-stone-400">{{ $punto->anio }}</span>
                                </div>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>
@endsection
