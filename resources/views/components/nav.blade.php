@php
    $programa = [
        ['Presentación', route('presentacion')],
        ['Resumen', route('resumen')],
        ['Historia', route('historia')],
        ['Misión y Visión', route('mision')],
        ['Objetivos', route('objetivos')],
        ['Campo Laboral', route('campo-laboral')],
    ];
    $academico = [
        ['Plan de Estudios 2023', route('plan-2023')],
        ['Competencias', route('competencias')],
        ['Perfil de Ingreso', route('perfil-ingreso')],
        ['Perfil de Egreso', route('perfil-egreso')],
    ];
    $mas = [
        ['Estadísticas', route('estadisticas', 'matriculados')],
        ['Personal Docente', route('docentes')],
        ['Comunicados', route('comunicados')],
        ['Documentos', route('documentos')],
    ];
@endphp

<header
    x-data="{ scrolled: false, mobile: false }"
    @scroll.window="scrolled = window.scrollY > 20"
    :class="scrolled ? 'bg-white/80 shadow-card backdrop-blur-md' : 'bg-white'"
    class="sticky top-0 z-50 border-b border-stone-200 transition-all duration-300"
>
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 transition-all duration-300"
         :class="scrolled ? 'py-2' : 'py-4'">

        {{-- Branding --}}
        <a href="{{ route('home') }}" wire:navigate.hover class="flex shrink-0 items-center gap-3">
            <picture>
                <source srcset="{{ asset('img/escudo-unasam.webp') }}" type="image/webp">
                <img src="{{ asset('img/escudo-unasam.png') }}" alt="Escudo UNASAM" width="44" height="54" class="h-11 w-auto">
            </picture>
            <span class="text-sm font-semibold leading-tight text-navy-900">
                Derecho y Ciencias Políticas
                <span class="block font-sans text-xs font-normal text-navy-500">UNASAM · Huaraz</span>
            </span>
        </a>

        {{-- Menú desktop --}}
        <div class="hidden items-center gap-1 lg:flex">
            <x-nav-link :href="route('home')">Inicio</x-nav-link>

            <x-nav-dropdown label="Programa" :items="$programa" />
            <x-nav-dropdown label="Académico" :items="$academico" />

            <x-nav-link :href="route('revista')">Revista</x-nav-link>
            <x-nav-link :href="route('blog')">Blog</x-nav-link>

            <x-nav-dropdown label="Más" :items="$mas" />
        </div>

        {{-- CTA + toggle móvil --}}
        <div class="flex items-center gap-3">
            <a href="https://unasam.edu.pe" target="_blank" rel="noopener"
               class="btn btn-sm btn-primary hidden sm:inline-flex">
                Portal UNASAM
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M7 7h10v10"/></svg>
            </a>
            <button @click="mobile = !mobile" class="lg:hidden text-navy-900"
                    :aria-expanded="mobile" aria-controls="mobile-menu"
                    :aria-label="mobile ? 'Cerrar menú' : 'Abrir menú'">
                <svg x-show="!mobile" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobile" x-cloak class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 6l12 12M6 18L18 6"/></svg>
            </button>
        </div>
    </nav>

    {{-- Menú móvil --}}
    <div id="mobile-menu" x-show="mobile" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="border-t border-stone-200 bg-white lg:hidden">
        <div class="space-y-1 px-6 py-4">
            <a href="{{ route('home') }}" wire:navigate.hover @click="mobile = false" class="block py-2 font-medium text-navy-900">Inicio</a>

            @foreach (['Programa' => $programa, 'Académico' => $academico, 'Más' => $mas] as $label => $items)
                <div x-data="{ open: false }" class="border-t border-stone-100 pt-1">
                    <button @click="open = !open" :aria-expanded="open" class="flex w-full items-center justify-between py-2 font-medium text-navy-900">
                        {{ $label }}
                        <svg class="h-4 w-4 transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-show="open" x-cloak class="pl-3">
                        @foreach ($items as [$texto, $url])
                            <a href="{{ $url }}" wire:navigate.hover @click="mobile = false" class="block py-1.5 text-sm text-stone-600 hover:text-navy-900">{{ $texto }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <a href="https://unasam.edu.pe" target="_blank" rel="noopener" @click="mobile = false" class="btn btn-sm btn-primary mt-3 w-full">Portal UNASAM ↗</a>
        </div>
    </div>
</header>
