{{-- Hero: fotografía a sangre + tira de lo último publicado.

     Sustituye al panel dividido anterior (navy a la izquierda, foto encajonada a
     la derecha). La única fotografía institucional que existe —el patio de la
     facultad, con el escudo en mosaico y la Cordillera Blanca al fondo— es
     panorámica y se desperdiciaba recortada a media pantalla.

     La tira inferior NO rota sola. En escritorio caben las tres entradas a la
     vez, así que un carrusel solo escondería contenido que ya está visible; por
     debajo de lg se muestra una y se pasa con las flechas. Sin temporizadores:
     DESIGN.md prohíbe el movimiento gratuito, y un temporizador que sobrevive a
     wire:navigate es además una fuga. --}}
<section class="relative isolate overflow-hidden bg-navy-950">

    <div class="absolute inset-0 -z-10">
        {{-- Dos anchos: el móvil no tiene por qué descargar una panorámica de
             2400 px para pintarla en 400. El respaldo JPEG existe para
             navegadores sin WebP, que a estas alturas son casi ninguno, así que
             va comprimido más corto. --}}
        <picture>
            <source type="image/webp"
                    srcset="{{ asset('img/campus-derecho-800.webp') }} 1200w, {{ asset('img/campus-derecho.webp') }} 2400w"
                    sizes="100vw">
            <img src="{{ asset('img/campus-derecho.jpg') }}"
                 alt="Patio de la Facultad de Derecho y Ciencias Políticas de la UNASAM: los tres niveles del claustro alrededor de la fuente, con la Cordillera Blanca al fondo"
                 fetchpriority="high" decoding="async"
                 class="h-full w-full object-cover" style="object-position: 50% 42%">
        </picture>
        {{-- Dos velos: uno parejo que garantiza el contraste del texto blanco
             sobre cualquier zona de la foto, y otro direccional que oscurece el
             lado del titular sin apagar la imagen entera. --}}
        <div class="absolute inset-0 bg-navy-950/55"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950 via-navy-950/75 to-navy-950/25"></div>
    </div>

    <div class="mx-auto max-w-7xl px-6 pb-14 pt-16 sm:pt-20 lg:pb-20 lg:pt-28">
        <div class="max-w-2xl">
            <p class="reveal text-[11px] font-semibold uppercase tracking-[0.14em] text-gold-300 sm:text-xs">
                UNASAM · Huaraz, Áncash
            </p>

            <h1 class="reveal hero-title mt-5 text-display text-white" data-reveal-delay="0.08">
                {{ $home['home_hero_titulo'] }}
            </h1>

            <p class="reveal mt-6 max-w-lg text-lg leading-relaxed text-white/75" data-reveal-delay="0.16">
                {{ $home['home_hero_subtitulo'] }}
            </p>

            <div class="reveal mt-8 flex flex-wrap gap-3" data-reveal-delay="0.24">
                <x-button :href="route('presentacion')" variant="light">{{ $home['home_hero_cta1'] }}</x-button>
                <x-button :href="route('plan-2023')" variant="ghost-light">{{ $home['home_hero_cta2'] }}</x-button>
            </div>

            <div class="reveal mt-12 grid max-w-md grid-cols-2 divide-x divide-white/15 border-t border-white/15 pt-7" data-reveal-delay="0.32">
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

    <x-hero-destacados :destacados="$destacados" />
</section>
