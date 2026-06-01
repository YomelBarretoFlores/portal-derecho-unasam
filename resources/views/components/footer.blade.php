<footer class="bg-navy-900 text-white">
    <div class="mx-auto max-w-7xl px-6 pt-16 pb-8">
        <div class="grid gap-12 md:grid-cols-[320px_1fr_auto]">

            {{-- Marca --}}
            <div>
                <img src="{{ asset('img/logo-derecho.png') }}" alt="FDCCPP" class="h-12 w-auto">
                <h3 class="mt-5 text-lg font-semibold text-white">{{ $ajustes['footer_marca'] }}</h3>
                <p class="mt-2 max-w-xs text-sm leading-relaxed text-white/55">
                    {{ $ajustes['footer_descripcion'] }}
                </p>
                <div class="mt-5 space-y-2.5 text-sm text-white/55">
                    <p class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $ajustes['contacto_direccion'] }}
                    </p>
                    <p class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h2L9 7l-2 1.5a12 12 0 0 0 6.5 6.5L15 13l4 1.5v2a2.5 2.5 0 0 1-2.5 2.5A14 14 0 0 1 3 5.5z"/></svg>
                        {{ $ajustes['contacto_telefono'] }}
                    </p>
                    <p class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        {{ $ajustes['contacto_email'] }}
                    </p>
                </div>
            </div>

            {{-- Enlaces --}}
            <div class="grid grid-cols-2 gap-8 md:pl-8">
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.14em] text-white/40">Programa</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ route('presentacion') }}" wire:navigate.hover class="transition hover:text-white">Presentación</a></li>
                        <li><a href="{{ route('historia') }}" wire:navigate.hover class="transition hover:text-white">Historia</a></li>
                        <li><a href="{{ route('mision') }}" wire:navigate.hover class="transition hover:text-white">Misión y Visión</a></li>
                        <li><a href="{{ route('campo-laboral') }}" wire:navigate.hover class="transition hover:text-white">Campo Laboral</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-[0.14em] text-white/40">Académico</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ route('plan-2023') }}" wire:navigate.hover class="transition hover:text-white">Plan de Estudios 2023</a></li>
                        <li><a href="{{ route('competencias') }}" wire:navigate.hover class="transition hover:text-white">Competencias</a></li>
                        <li><a href="{{ route('revista') }}" wire:navigate.hover class="transition hover:text-white">Revista Jurídica</a></li>
                        <li><a href="{{ route('docentes') }}" wire:navigate.hover class="transition hover:text-white">Personal Docente</a></li>
                    </ul>
                </div>
            </div>

            {{-- CTA --}}
            <div>
                <a href="{{ $ajustes['footer_cta_url'] }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/20 px-5 py-3 text-sm font-medium text-white transition hover:bg-white hover:text-navy-900">
                    {{ $ajustes['footer_cta_texto'] }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M7 7h10v10"/></svg>
                </a>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-sm text-white/40 sm:flex-row">
            <p>© {{ date('Y') }} UNASAM — {{ $ajustes['footer_marca'] }}.</p>
            <p class="tracking-wide text-white/50">{{ $ajustes['lema'] }}</p>
        </div>
    </div>
</footer>
