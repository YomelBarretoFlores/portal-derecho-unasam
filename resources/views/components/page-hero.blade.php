@props(['title', 'seccion' => 'Programa', 'subtitle' => null])

{{-- Cabecera de página interna: clara y ligera, con un único acento dorado fino --}}
<section class="border-b border-stone-200 bg-paper">
    <div class="mx-auto max-w-7xl px-6 py-14 md:py-20">
        <nav class="flex items-center gap-2 text-sm text-stone-500">
            <a href="{{ route('home') }}" wire:navigate class="transition hover:text-navy-900">Inicio</a>
            <span class="text-stone-300">/</span>
            <span>{{ $seccion }}</span>
        </nav>
        <div class="accent-line mt-6"></div>
        <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-navy-900 md:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-lg leading-relaxed text-stone-600">{{ $subtitle }}</p>
        @endif
    </div>
</section>
