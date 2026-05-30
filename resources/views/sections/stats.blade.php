{{-- Sección Estadísticas (variante Narrative) --}}
@php $max = $matriculados->max('total'); @endphp
<section class="bg-navy-900 py-24 text-white">
    <div class="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2">

        {{-- Narrativa --}}
        <div class="reveal">
            <span class="font-sans text-xs font-bold uppercase tracking-[0.12em] text-gold-400">El programa en cifras</span>
            <h2 class="mt-3 text-3xl font-semibold leading-tight text-white md:text-4xl">
                Una comunidad académica en crecimiento
            </h2>
            <p class="mt-6 font-sans text-lg leading-relaxed text-white/70">
                Hoy somos <span class="font-semibold text-gold-400">551 estudiantes</span> matriculados,
                con <span class="font-semibold text-gold-400">94 egresados</span> al año y más de
                <span class="font-semibold text-gold-400">40 años</span> formando abogados en
                <span class="font-semibold text-gold-400">8 áreas</span> del derecho.
            </p>

            <div class="mt-10 grid grid-cols-2 gap-8 sm:grid-cols-4">
                <x-stat value="551" label="Estudiantes" />
                <x-stat value="94" label="Egresados/año" />
                <x-stat value="40" suffix="+" label="Años" />
                <x-stat value="8" label="Áreas" />
            </div>
        </div>

        {{-- Bar chart matriculados --}}
        <div class="reveal" data-reveal-delay="0.12">
            <p class="mb-6 font-sans text-sm text-white/60">Estudiantes matriculados por año</p>
            <div class="flex items-end justify-between gap-3" style="height:240px">
                @foreach ($matriculados as $punto)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <span class="font-sans text-xs font-semibold text-gold-400">{{ $punto->total }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-gold-600 to-gold-400"
                             style="height: {{ round($punto->total / $max * 100) }}%"></div>
                        <span class="font-sans text-xs text-white/50">{{ $punto->anio }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
