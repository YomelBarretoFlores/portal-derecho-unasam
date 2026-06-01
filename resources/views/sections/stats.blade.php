{{-- Sección Estadísticas (única sección oscura: "el programa en cifras") --}}
@php $max = $matriculados->max('total'); @endphp
<section class="bg-navy-900 py-24 text-white">
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2">

        {{-- Narrativa --}}
        <div class="reveal">
            <span class="text-xs font-semibold uppercase tracking-[0.14em] text-white/45">{{ $home['home_stats_eyebrow'] }}</span>
            <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white md:text-4xl">
                {{ $home['home_stats_titulo'] }}
            </h2>
            <p class="mt-6 text-lg leading-relaxed text-white/65">
                {{ $home['home_stats_narrativa'] }}
            </p>

            <div class="mt-10 grid grid-cols-2 gap-8 sm:grid-cols-4">
                <x-stat value="1049" label="Matriculados (2024)" />
                <x-stat value="68" label="Titulados (2024)" />
                <x-stat value="40" suffix="+" label="Años" />
                <x-stat value="8" label="Áreas" />
            </div>
        </div>

        {{-- Bar chart matriculados --}}
        <div class="reveal" data-reveal-delay="0.12">
            <p class="mb-6 text-sm text-white/55">Estudiantes matriculados por año</p>
            <div class="flex items-end justify-between gap-3" style="height:240px">
                @foreach ($matriculados as $i => $punto)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs font-semibold text-white/80" data-count="{{ $punto->total }}">0</span>
                        <div class="bar-grow w-full rounded-t-md bg-gold-400/90"
                             style="height: {{ round($punto->total / $max * 100) }}%; animation-delay: {{ $i * 0.06 }}s"></div>
                        <span class="text-xs text-white/45">{{ $punto->anio }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
