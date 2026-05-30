@props(['title', 'seccion' => 'Programa', 'subtitle' => null])

<section class="relative isolate overflow-hidden bg-navy-900">
    <img src="{{ asset('img/campus-fdccpp.png') }}" alt=""
         class="absolute inset-0 -z-10 h-full w-full object-cover opacity-25">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950/95 to-navy-900/80"></div>

    <div class="mx-auto max-w-7xl px-6 py-20 md:py-24">
        <nav class="flex items-center gap-2 font-sans text-sm text-white/60">
            <a href="{{ route('home') }}" class="transition hover:text-gold-400">Inicio</a>
            <span>/</span>
            <span class="text-white/40">{{ $seccion }}</span>
        </nav>
        <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight text-white md:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl font-sans text-lg text-white/70">{{ $subtitle }}</p>
        @endif
    </div>
</section>
