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
        @if (filled($home['home_hero_foto_url'] ?? ''))
            {{-- Fotografía puesta desde el panel. Se sirve tal cual: de una
                 imagen que no controlamos no podemos generar las versiones
                 optimizadas, así que el aviso de tamaño está en el propio
                 campo del formulario. --}}
            <img src="{{ $home['home_hero_foto_url'] }}"
                 alt="Fotografía de la Facultad de Derecho y Ciencias Políticas de la UNASAM"
                 fetchpriority="high" decoding="async"
                 class="h-full w-full object-cover" style="object-position: 50% 42%">
        @else
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
        @endif
        {{-- El velo oscurece SOLO donde hay texto.

             Antes eran dos capas superpuestas —un velo plano del 55 % más un
             degradado— que sumaban un 66 % incluso en el lado derecho, donde no
             hay nada que proteger. La fotografía quedaba apagada de punta a
             punta: se veía apenas un tercio de ella.

             Ahora es un solo degradado que aguanta el 84 % hasta donde termina
             el texto y cae en picado después. Medido contra el píxel más claro
             que hay detrás, el subtítulo —que es el color más débil, blanco al
             75 %— queda en 6,3:1, por encima del 4,5:1 que exige la AA. En la
             franja donde está la mascota la fotografía se ve al 61-83 %.

             En pantallas pequeñas el texto ocupa el ancho entero, así que ahí
             el velo tiene que ser parejo: un degradado lateral dejaría el final
             de cada línea sin fondo que la sostenga. --}}
        <div class="absolute inset-0 bg-navy-950/80 lg:hidden"></div>
        <div class="absolute inset-0 hidden lg:block" style="background: linear-gradient(to right,
                 rgba(15,34,64,0.92) 0%,
                 rgba(15,34,64,0.84) 70%,
                 rgba(15,34,64,0.30) 88%,
                 rgba(15,34,64,0.08) 100%)"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-6 pb-14 pt-16 sm:pt-20 lg:pb-20 lg:pt-28">

        @if (filled($home['home_hero_mascota_url'] ?? ''))
            {{-- Mascota de la facultad, de pie sobre el patio.

                 Solo desde lg. Por debajo, el titular ya ocupa el ancho entero y
                 la mascota tendría que encogerse tanto que no se reconocería, o
                 taparía el texto. Antes que una versión diminuta, ninguna.

                 Va detrás del texto en el orden de apilado y con pointer-events
                 desactivados: es un elemento de ambiente, y no debe interceptar
                 un clic dirigido a los botones si alguna vez se solapan.
            
                 Sin «alt» administrado se trata como decorativa: un lector de
                 pantalla no gana nada anunciando una ilustración, y obligarle a
                 escucharla antes del titular sería peor que omitirla. --}}
            <img src="{{ $home['home_hero_mascota_url'] }}"
                 @if (filled($home['home_hero_mascota_alt'] ?? ''))
                     alt="{{ $home['home_hero_mascota_alt'] }}"
                 @else
                     alt="" aria-hidden="true"
                 @endif
                 loading="lazy" decoding="async"
                 class="pointer-events-none absolute bottom-0 right-4 z-0 hidden w-auto select-none object-contain object-bottom drop-shadow-2xl lg:block xl:right-10"
                 style="height: clamp(19rem, 34vw, 30rem)">
        @endif

        {{-- El relleno derecho reserva el sitio de la mascota; dentro, el texto
             se centra en el espacio que queda. Sin ese relleno, centrar en el
             ancho completo metería el titular debajo de la ilustración.

             Solo se centra desde lg. En un móvil el texto ocupa el ancho entero
             y centrarlo dejaría los renglones con los bordes dentados, que se
             lee peor; alineado a la izquierda cada línea empieza donde la vista
             ya está esperando. --}}
        <div class="relative z-10 lg:pr-[22rem]">
        <div class="max-w-2xl lg:mx-auto lg:max-w-3xl lg:text-center">
            <p class="reveal text-[11px] font-semibold uppercase tracking-[0.14em] text-gold-300 sm:text-xs">
                UNASAM · Huaraz, Áncash
            </p>

            <h1 class="reveal hero-title mt-5 text-display text-white" data-reveal-delay="0.08">
                {{ $home['home_hero_titulo'] }}
            </h1>

            <p class="reveal mt-6 max-w-lg text-lg leading-relaxed text-white/80 lg:mx-auto" data-reveal-delay="0.16">
                {{ $home['home_hero_subtitulo'] }}
            </p>

            <div class="reveal mt-8 flex flex-wrap gap-3 lg:justify-center" data-reveal-delay="0.24">
                <x-button :href="route('presentacion')" variant="light">{{ $home['home_hero_cta1'] }}</x-button>
                <x-button :href="route('plan-2023')" variant="ghost-light">{{ $home['home_hero_cta2'] }}</x-button>
            </div>

            <div class="reveal mt-12 grid max-w-md grid-cols-2 divide-x divide-white/15 border-t border-white/15 pt-7 lg:mx-auto" data-reveal-delay="0.32">
                <div class="px-3 text-left first:pl-0 sm:px-6 lg:text-center">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.1em] text-gold-300 sm:text-xs">{{ $home['home_hero_stat1_label'] }}</div>
                    <div class="stat-outline mt-3 text-3xl font-bold tracking-tight sm:text-5xl" data-count="{{ $home['home_hero_stat1_valor'] }}" data-count-suffix="{{ $home['home_hero_stat1_sufijo'] }}">{{ $home['home_hero_stat1_valor'] }}{{ $home['home_hero_stat1_sufijo'] }}</div>
                </div>
                <div class="px-3 text-left sm:px-6 lg:text-center">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.1em] text-gold-300 sm:text-xs">{{ $home['home_hero_stat2_label'] }}</div>
                    <div class="stat-outline mt-3 text-3xl font-bold tracking-tight sm:text-5xl" data-count="{{ $home['home_hero_stat2_valor'] }}" data-count-suffix="{{ $home['home_hero_stat2_sufijo'] }}">{{ $home['home_hero_stat2_valor'] }}{{ $home['home_hero_stat2_sufijo'] }}</div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <x-hero-destacados :destacados="$destacados" />
</section>
