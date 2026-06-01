{{-- Banda decorativa: ramas del derecho en scroll horizontal infinito --}}
@php
    $terminos = collect(preg_split('/\s*·\s*/', trim((string) ($ajustes['home_marquee'] ?? ''))))
        ->filter()
        ->values();
@endphp

@if ($terminos->isNotEmpty())
    <section class="marquee border-y border-stone-200 bg-paper py-10" aria-hidden="true">
        <div class="marquee-track">
            {{-- Se repite la lista dos veces para el bucle sin costuras (translateX -50%) --}}
            @foreach (range(1, 2) as $vuelta)
                @foreach ($terminos as $i => $termino)
                    <span class="flex items-center gap-12 whitespace-nowrap text-3xl font-bold tracking-tight md:text-4xl {{ $i % 2 === 0 ? 'text-navy-900' : 'text-outline-navy' }}">
                        {{ $termino }}
                        <span class="text-gold-400">·</span>
                    </span>
                @endforeach
            @endforeach
        </div>
    </section>
@endif
