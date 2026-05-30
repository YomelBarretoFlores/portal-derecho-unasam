@extends('layouts.app')

@section('title', $titulo . ' — Estadísticas Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="Estadísticas"
        subtitle="Indicadores académicos del programa, {{ \Illuminate\Support\Str::lower($titulo) }}." />

    <section class="mx-auto max-w-7xl px-6 py-16">
        {{-- Pestañas --}}
        <div class="flex flex-wrap gap-2">
            @foreach ($tipos as $key => $label)
                <a href="{{ route('estadisticas', $key) }}" wire:navigate
                   class="rounded-full px-5 py-2 font-sans text-sm font-medium transition {{ $key === $tipo ? 'bg-navy-900 text-white' : 'border border-gray-300 text-gray-600 hover:border-navy-900' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @php $max = $serie->max('total'); @endphp
        <div class="mt-12 grid gap-12 lg:grid-cols-2">
            {{-- Tabla --}}
            <div class="reveal">
                <h2 class="text-xl font-semibold text-navy-900">{{ $titulo }} por año</h2>
                <table class="mt-5 w-full font-sans text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200 text-left text-gray-400">
                            <th class="pb-3 font-semibold">Año</th>
                            <th class="pb-3 font-semibold">Total</th>
                            <th class="pb-3 text-right font-semibold">Variación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serie as $i => $punto)
                            @php $prev = $i > 0 ? $serie[$i - 1]->total : null; $var = $prev ? round(($punto->total - $prev) / $prev * 100, 1) : null; @endphp
                            <tr class="border-b border-gray-100">
                                <td class="py-3 font-medium text-navy-900">{{ $punto->anio }}</td>
                                <td class="py-3 text-gray-600">{{ $punto->total }}</td>
                                <td class="py-3 text-right">
                                    @if ($var !== null)
                                        <span class="font-medium {{ $var >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $var >= 0 ? '+' : '' }}{{ $var }}%</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Gráfico --}}
            <div class="reveal" data-reveal-delay="0.1">
                <h2 class="text-xl font-semibold text-navy-900">Evolución</h2>
                <div class="mt-6 flex items-end justify-between gap-3" style="height:260px">
                    @foreach ($serie as $punto)
                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="font-sans text-xs font-semibold text-navy-700">{{ $punto->total }}</span>
                            <div class="w-full rounded-t-md bg-gradient-to-t from-navy-900 to-navy-500"
                                 style="height: {{ round($punto->total / $max * 100) }}%"></div>
                            <span class="font-sans text-xs text-gray-400">{{ $punto->anio }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
