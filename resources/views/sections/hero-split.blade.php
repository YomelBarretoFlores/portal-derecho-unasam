{{-- Hero "Panel dividido": navy sólido (texto) | foto del campus --}}
<section class="relative overflow-hidden bg-navy-900">

    {{-- Foto real del campus: fondo ambiental en móvil y panel derecho en escritorio. --}}
    <div class="absolute inset-0 lg:left-1/2">
        <picture>
            <source srcset="{{ asset('img/campus-fdccpp.webp') }}" type="image/webp">
            <img src="{{ asset('img/campus-fdccpp.jpg') }}" alt="Campus de la Facultad de Derecho y Ciencias Políticas"
                 fetchpriority="high" decoding="async"
                 class="absolute inset-0 h-full w-full object-cover opacity-35 lg:opacity-100" style="object-position: 50% 32%" data-parallax>
        </picture>
        <div class="absolute inset-0 bg-navy-950/55 lg:hidden"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-navy-900 via-navy-900/60 to-navy-900/20"></div>
    </div>

    {{-- Contenido alineado con el contenedor del sitio --}}
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="flex max-w-2xl items-center py-16 sm:py-20 lg:min-h-[560px] lg:py-24">
            <div class="w-full">
                <h1 class="reveal hero-title text-display text-white">
                    {{ $home['home_hero_titulo'] }}
                </h1>

                <p class="reveal mt-6 max-w-lg text-lg leading-relaxed text-white/70" data-reveal-delay="0.12">
                    {{ $home['home_hero_subtitulo'] }}
                </p>

                <div class="reveal mt-8 flex flex-wrap gap-3" data-reveal-delay="0.24">
                    <x-button :href="route('presentacion')" variant="light">{{ $home['home_hero_cta1'] }}</x-button>
                    <x-button :href="route('plan-2023')" variant="ghost-light">{{ $home['home_hero_cta2'] }}</x-button>
                </div>

                {{-- Cifras de alcance/trayectoria (las de matrícula viven en la sección Estadísticas) --}}
                <div class="reveal mt-12 grid max-w-md grid-cols-2 divide-x divide-white/15 border-t border-white/15 pt-7" data-reveal-delay="0.4">
                    <div class="px-3 text-left first:pl-0 sm:px-6">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.1em] text-gold-300 sm:text-xs">{{ $home['home_hero_stat1_label'] }}</div>
                        <div class="stat-outline mt-3 text-3xl font-bold tracking-tight sm:text-5xl" data-count="{{ $home['home_hero_stat1_valor'] }}" data-count-suffix="{{ $home['home_hero_stat1_sufijo'] }}">{{ $home['home_hero_stat1_valor'] }}{{ $home['home_hero_stat1_sufijo'] }}</div>
                    </div>
                    <div class="px-3 text-left sm:px-6">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.1em] text-gold-300 sm:text-xs">{{ $home['home_hero_stat2_label'] }}</div>
                        <div class="stat-outline mt-3 text-3xl font-bold tracking-tight sm:text-5xl" data-count="{{ $home['home_hero_stat2_valor'] }}" data-count-suffix="{{ $home['home_hero_stat2_sufijo'] }}">{{ $home['home_hero_stat2_valor'] }}{{ $home['home_hero_stat2_sufijo'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
