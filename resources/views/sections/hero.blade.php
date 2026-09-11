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
        {{-- El velo oscurece SOLO donde hay texto.

             Antes eran dos capas superpuestas —un velo plano del 55 % más un
             degradado— que sumaban un 66 % incluso en el lado derecho, donde no
             hay nada que proteger. La fotografía quedaba apagada de punta a
             punta: se veía apenas un tercio de ella.

             Ahora es un solo degradado que aguanta el 83 % hasta donde termina
             la columna de texto y cae en picado después. Medido contra el píxel
             más claro que hay detrás del texto, el subtítulo —que es el color
             más débil, blanco al 75 %— queda en 6,0:1, por encima del 4,5:1 que
             exige la AA. A la derecha la foto se ve al 75-90 %.

             En pantallas pequeñas el texto ocupa el ancho entero, así que ahí
             el velo tiene que ser parejo: un degradado lateral dejaría el final
             de cada línea sin fondo que la sostenga. --}}
        <div class="absolute inset-0 bg-navy-950/80 lg:hidden"></div>
        <div class="absolute inset-0 hidden lg:block" style="background: linear-gradient(to right,
                 rgba(15,34,64,0.92) 0%,
                 rgba(15,34,64,0.82) 55%,
                 rgba(15,34,64,0.25) 80%,
                 rgba(15,34,64,0.05) 100%)"></div>
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

        @php
            // Lo que encabeza el hero es lo último que la facultad ha publicado.
            // No hay un campo «destacado» aparte: publicar ya es destacar, y un
            // interruptor más sería una cosa más que se olvida de mover.
            $principal = collect($destacados)->first();
        @endphp

        <div class="relative z-10 max-w-2xl">
            @if ($principal)
                {{-- Hero editorial: el sitio abre contando qué pasa, no repitiendo
                     cómo se llama. El nombre de la facultad ya está en la barra de
                     arriba y en el escudo; volver a escribirlo aquí gastaba el
                     espacio más visible del portal en información que el visitante
                     acaba de leer. --}}
                <p class="reveal flex flex-wrap items-baseline gap-x-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-gold-300 sm:text-xs">
                    {{ $principal->etiqueta }}
                    @if ($principal->fecha)
                        <span class="font-normal normal-case tracking-normal text-white/65">
                            {{ $principal->fecha->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    @endif
                </p>

                <h1 class="reveal hero-title mt-5 text-display text-white" data-reveal-delay="0.08">
                    {{ $principal->titulo }}
                </h1>

                @if (filled($principal->resumen ?? ''))
                    <p class="reveal mt-6 max-w-xl text-lg leading-relaxed text-white/80" data-reveal-delay="0.16">
                        {{ $principal->resumen }}
                    </p>
                @endif

                <div class="reveal mt-8" data-reveal-delay="0.24">
                    <x-button :href="$principal->url" variant="light">Leer más</x-button>
                </div>
            @else
                {{-- Respaldo para una instalación nueva, antes de que se publique
                     nada. Sin él la portada abriría con un hueco, que es peor que
                     abrir con la identidad. --}}
                <p class="reveal text-[11px] font-semibold uppercase tracking-[0.14em] text-gold-300 sm:text-xs">
                    UNASAM · Huaraz, Áncash
                </p>

                <h1 class="reveal hero-title mt-5 text-display text-white" data-reveal-delay="0.08">
                    {{ $home['home_hero_titulo'] }}
                </h1>

                <p class="reveal mt-6 max-w-lg text-lg leading-relaxed text-white/80" data-reveal-delay="0.16">
                    {{ $home['home_hero_subtitulo'] }}
                </p>

                <div class="reveal mt-8 flex flex-wrap gap-3" data-reveal-delay="0.24">
                    <x-button :href="route('presentacion')" variant="light">{{ $home['home_hero_cta1'] }}</x-button>
                    <x-button :href="route('plan-2023')" variant="ghost-light">{{ $home['home_hero_cta2'] }}</x-button>
                </div>
            @endif
        </div>
    </div>

    <x-hero-destacados :destacados="$destacados" />
</section>
