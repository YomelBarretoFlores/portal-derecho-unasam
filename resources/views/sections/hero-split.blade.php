{{-- Hero "Panel dividido": navy sólido (texto) | foto del campus (tarjeta) --}}
<section class="relative overflow-hidden bg-navy-900">

    {{-- Foto del campus: sangra al borde derecho (solo desktop) --}}
    <div class="absolute inset-y-0 right-0 hidden w-1/2 lg:block">
        <img src="{{ asset('img/campus-fdccpp.png') }}" alt="Campus de la Facultad de Derecho y Ciencias Políticas"
             class="absolute inset-0 h-full w-full object-cover" style="object-position: 50% 32%">
        <div class="absolute inset-0 bg-navy-900/40"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-navy-900 via-navy-900/40 to-navy-900/10"></div>
    </div>

    {{-- Contenido alineado con el contenedor del sitio --}}
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid items-center lg:grid-cols-2">

            {{-- Izquierda: texto sobre navy sólido --}}
            <div class="flex items-center py-20 lg:min-h-[600px] lg:py-28 lg:pr-12">
                <div class="w-full max-w-xl">
                    <span class="reveal inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 font-sans text-xs font-medium text-white ring-1 ring-white/20">
                        <img src="{{ asset('img/escudo-unasam.png') }}" alt="" class="h-5 w-auto">
                        Programa de Estudios · UNASAM
                    </span>

                    <h1 class="reveal mt-6 text-5xl font-bold leading-[1.05] text-white md:text-6xl" data-reveal-delay="0.08">
                        Derecho y<br>Ciencias <span class="text-gold-400">Políticas</span>
                    </h1>

                    <p class="reveal mt-6 max-w-lg font-sans text-lg leading-relaxed text-white/75" data-reveal-delay="0.16">
                        Formación jurídica de excelencia con responsabilidad social, al servicio de Áncash
                        y el país desde 1986.
                    </p>

                    <div class="reveal mt-8 flex flex-wrap gap-3" data-reveal-delay="0.24">
                        <x-button :href="route('presentacion')" variant="gold">Conoce el programa</x-button>
                        <x-button :href="route('plan-2023')" variant="ghost-light">Plan de Estudios</x-button>
                    </div>

                    <div class="reveal mt-12 flex flex-wrap gap-x-10 gap-y-6 border-t border-white/10 pt-8" data-reveal-delay="0.32">
                        <div>
                            <div class="font-serif text-3xl font-bold text-gold-400" data-count="551">0</div>
                            <div class="font-sans text-sm text-white/60">Estudiantes</div>
                        </div>
                        <div>
                            <div class="font-serif text-3xl font-bold text-gold-400" data-count="94">0</div>
                            <div class="font-sans text-sm text-white/60">Egresados al año</div>
                        </div>
                        <div>
                            <div class="font-serif text-3xl font-bold text-gold-400" data-count="40" data-count-suffix="+">0+</div>
                            <div class="font-sans text-sm text-white/60">Años de trayectoria</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Derecha: tarjeta institucional (sobre la foto en desktop) --}}
            <div class="pb-20 lg:flex lg:min-h-[600px] lg:items-center lg:justify-end lg:py-28">
                <div class="reveal w-full max-w-sm rounded-3xl border border-white/15 bg-navy-950/80 p-8 shadow-2xl backdrop-blur-md" data-reveal-delay="0.2">
                    <div class="flex items-center justify-center gap-6">
                        <div class="flex h-20 items-center">
                            <img src="{{ asset('img/escudo-unasam.png') }}" alt="UNASAM" class="max-h-full w-auto">
                        </div>
                        <div class="h-16 w-px bg-white/20"></div>
                        <div class="flex h-20 items-center">
                            <img src="{{ asset('img/logo-derecho.png') }}" alt="FDCCPP" class="max-h-full w-auto">
                        </div>
                    </div>
                    <p class="mt-6 text-center font-serif text-lg italic text-gold-400">Orabunt Causas Melius</p>
                    <div class="mt-5 space-y-1 text-center font-sans text-sm text-white/75">
                        <p class="font-medium text-white">Facultad de Derecho y Ciencias Políticas</p>
                        <p>Universidad Nacional Santiago Antúnez de Mayolo</p>
                        <p class="text-white/50">Huaraz · Áncash · Perú</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
