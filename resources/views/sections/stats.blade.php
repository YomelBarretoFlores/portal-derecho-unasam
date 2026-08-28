{{-- Estadísticas: bloque de tinta institucional con cifras y serie histórica. --}}
@php $max = max(1, (int) $matriculados->max('total')); $matriculadoActual = $matriculados->last(); @endphp
<section class="relative overflow-hidden bg-navy-950 py-20 text-white md:py-24">
    <div class="absolute inset-y-0 left-1/2 hidden w-px bg-white/10 lg:block" aria-hidden="true"></div>
    <div class="absolute -right-16 -top-24 font-serif text-[18rem] font-bold leading-none text-white/[0.025]" aria-hidden="true">§</div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-6 lg:grid-cols-2 lg:gap-20">

        {{-- Narrativa --}}
        <div class="reveal">
            <span class="text-xs font-semibold uppercase tracking-[0.14em] text-white/60">{{ $home['home_stats_eyebrow'] }}</span>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white md:text-4xl">
                {{ $home['home_stats_titulo'] }}
            </h2>
            <p class="mt-6 text-lg leading-relaxed text-white/80">
                {{ $home['home_stats_narrativa'] }}
            </p>

            <div class="mt-9 grid max-w-lg grid-cols-2 divide-x divide-white/15 border-y border-white/10 py-6">
                <x-stat class="pr-5 sm:pr-10" :value="$matriculadoActual?->total ?? 0" :label="'Matriculados ('.($matriculadoActual?->anio ?? 'sin datos').')'" />
                <x-stat class="pl-5 sm:pl-10" :value="$tituladosActual?->total ?? 0" :label="'Titulados ('.($tituladosActual?->anio ?? 'sin datos').')'" />
            </div>
            <a href="{{ route('estadisticas', 'matriculados') }}" wire:navigate.hover class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-gold-300 underline decoration-gold-300/35 underline-offset-4 hover:decoration-gold-300">Consultar todas las estadísticas <x-ui-icon name="arrow-right" /></a>
        </div>

        {{-- Bar chart matriculados --}}
        <div class="reveal" data-reveal-delay="0.12">
            <p class="mb-6 text-sm text-white/55">Estudiantes matriculados por año</p>
            <div class="overflow-x-auto pb-2">
            <div class="flex min-w-[30rem] items-end justify-between gap-3" style="height:240px">
                @foreach ($matriculados as $i => $punto)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-white/80 tabular-nums" data-count="{{ $punto->total }}">{{ $punto->total }}</span>
                        <div class="bar-grow w-full border-t-2 border-gold-200 bg-gold-300/80"
                             style="height: {{ round($punto->total / $max * 100) }}%; animation-delay: {{ $i * 0.06 }}s"></div>
                        <span class="text-xs text-white/60">{{ $punto->anio }}</span>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </div>
</section>
