{{-- Hero "Panel dividido": navy sólido (texto) | foto del campus --}}
<section class="relative overflow-hidden bg-navy-900">

    {{-- Foto del campus: sangra al borde derecho (solo desktop) --}}
    <div class="absolute inset-y-0 right-0 hidden w-1/2 lg:block">
        <img src="{{ asset('img/campus-fdccpp.png') }}" alt="Campus de la Facultad de Derecho y Ciencias Políticas"
             class="absolute inset-0 h-full w-full object-cover" style="object-position: 50% 32%" data-parallax>
        <div class="absolute inset-0 bg-gradient-to-r from-navy-900 via-navy-900/55 to-navy-900/15"></div>
    </div>

    {{-- Contenido alineado con el contenedor del sitio --}}
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="flex max-w-2xl items-center py-20 lg:min-h-[580px] lg:py-28">
            <div class="w-full">
                <h1 class="reveal hero-title text-5xl text-white md:text-6xl lg:text-7xl">
                    {{ $home['home_hero_titulo'] }}
                </h1>

                <p class="reveal mt-6 max-w-lg text-lg leading-relaxed text-white/70" data-reveal-delay="0.12">
                    {{ $home['home_hero_subtitulo'] }}
                </p>

                <div class="reveal mt-8 flex flex-wrap gap-3" data-reveal-delay="0.24">
                    <x-button :href="route('presentacion')" variant="light">{{ $home['home_hero_cta1'] }}</x-button>
                    <x-button :href="route('plan-2023')" variant="ghost-light">{{ $home['home_hero_cta2'] }}</x-button>
                </div>

                {{-- Estadísticas: etiqueta arriba + cifra "hueca", separadas por divisores --}}
                <div class="reveal mt-14 grid max-w-xl grid-cols-3 divide-x divide-white/15 border-t border-white/10 pt-8" data-reveal-delay="0.4">
                    <div class="px-3 text-center first:pl-0 sm:px-6">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gold-400 sm:text-[11px]">Matriculados (2024)</div>
                        <div class="stat-outline mt-3 text-4xl font-bold tracking-tight sm:text-5xl" data-count="1049">0</div>
                    </div>
                    <div class="px-3 text-center sm:px-6">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gold-400 sm:text-[11px]">Titulados (2024)</div>
                        <div class="stat-outline mt-3 text-4xl font-bold tracking-tight sm:text-5xl" data-count="68">0</div>
                    </div>
                    <div class="px-3 text-center sm:px-6">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.12em] text-gold-400 sm:text-[11px]">Años de trayectoria</div>
                        <div class="stat-outline mt-3 text-4xl font-bold tracking-tight sm:text-5xl" data-count="40" data-count-suffix="+">0+</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
