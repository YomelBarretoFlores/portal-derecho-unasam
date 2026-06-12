@php
    // Mapa de sitio completo: el footer funciona como ayuda de navegación (toda la nav alcanzable).
    $mapa = [
        'Programa' => [
            ['Presentación', route('presentacion')],
            ['Resumen', route('resumen')],
            ['Historia', route('historia')],
            ['Misión y Visión', route('mision')],
            ['Objetivos', route('objetivos')],
            ['Campo Laboral', route('campo-laboral')],
        ],
        'Académico' => [
            ['Plan de Estudios 2023', route('plan-2023')],
            ['Competencias', route('competencias')],
            ['Perfil de Ingreso', route('perfil-ingreso')],
            ['Perfil de Egreso', route('perfil-egreso')],
        ],
        'Publicaciones' => [
            ['Revista Jurídica', route('revista')],
            ['Blog', route('blog')],
        ],
        'Institucional' => [
            ['Personal Docente', route('docentes')],
            ['Estadísticas', route('estadisticas', 'matriculados')],
            ['Comunicados', route('comunicados')],
            ['Documentos', route('documentos')],
            ['Organigrama', route('organigrama')],
        ],
    ];
@endphp

<footer class="bg-navy-900 text-white">
    <div class="mx-auto max-w-7xl px-6 pt-16 pb-8">
        <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-6">

            {{-- Marca + contacto + CTA --}}
            <div class="lg:col-span-2">
                <picture>
                    <source srcset="{{ asset('img/logo-derecho.webp') }}" type="image/webp">
                    <img src="{{ asset('img/logo-derecho.png') }}" alt="FDCCPP" loading="lazy" decoding="async" class="h-12 w-auto">
                </picture>
                <h3 class="mt-5 text-lg font-semibold text-white">{{ $ajustes['footer_marca'] }}</h3>
                <p class="mt-2 max-w-xs text-sm leading-relaxed text-white/55">
                    {{ $ajustes['footer_descripcion'] }}
                </p>
                <address class="mt-5 space-y-2.5 text-sm not-italic text-white/55">
                    <p class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $ajustes['contacto_direccion'] }}
                    </p>
                    @if (! empty($ajustes['contacto_telefono']))
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $ajustes['contacto_telefono']) }}" class="flex items-center gap-2.5 transition hover:text-white">
                            <svg class="h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h2L9 7l-2 1.5a12 12 0 0 0 6.5 6.5L15 13l4 1.5v2a2.5 2.5 0 0 1-2.5 2.5A14 14 0 0 1 3 5.5z"/></svg>
                            {{ $ajustes['contacto_telefono'] }}
                        </a>
                    @endif
                    @if (! empty($ajustes['contacto_email']))
                        <a href="mailto:{{ $ajustes['contacto_email'] }}" class="flex items-center gap-2.5 transition hover:text-white">
                            <svg class="h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            {{ $ajustes['contacto_email'] }}
                        </a>
                    @endif
                </address>
                <a href="{{ $ajustes['footer_cta_url'] }}" target="_blank" rel="noopener"
                   class="mt-6 inline-flex items-center gap-2 rounded-none border border-white/20 px-5 py-3 text-sm font-medium text-white transition hover:bg-white hover:text-navy-900">
                    {{ $ajustes['footer_cta_texto'] }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17 17 7M7 7h10v10"/></svg>
                </a>
            </div>

            {{-- Mapa de sitio --}}
            @foreach ($mapa as $titulo => $enlaces)
                <nav class="lg:col-span-1" aria-label="{{ $titulo }}">
                    <div class="accent-line mb-3"></div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.14em] text-white/40">{{ $titulo }}</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        @foreach ($enlaces as [$texto, $url])
                            <li><a href="{{ $url }}" wire:navigate.hover class="transition hover:text-white">{{ $texto }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach
        </div>

        <div class="mt-16 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-sm text-white/40 sm:flex-row">
            <p>© {{ date('Y') }} UNASAM — {{ $ajustes['footer_marca'] }}.</p>
            <p class="tracking-wide text-white/50">{{ $ajustes['lema'] }}</p>
        </div>
    </div>
</footer>
