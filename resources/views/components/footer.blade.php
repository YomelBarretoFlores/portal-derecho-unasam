<footer class="mt-24 bg-gray-900 text-white">
    <div class="mx-auto max-w-7xl px-6 pt-16 pb-8">
        <div class="grid gap-12 md:grid-cols-[300px_1fr_auto]">

            {{-- Brand --}}
            <div>
                <img src="{{ asset('img/logo-derecho.png') }}" alt="FDCCPP" class="h-14 w-auto">
                <h3 class="mt-4 text-xl text-white">Derecho y CC.PP.</h3>
                <p class="mt-2 max-w-xs font-sans text-sm leading-relaxed text-white/60">
                    Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional
                    Santiago Antúnez de Mayolo.
                </p>
                <div class="mt-4 h-0.5 w-8 bg-gold-500"></div>
                <div class="mt-4 space-y-1.5 font-sans text-sm text-white/60">
                    <p>📍 Ciudad Universitaria, Huaraz, Áncash</p>
                    <p>📞 (043) 640-020</p>
                    <p>✉ mesadepartesdigital@unasam.edu.pe</p>
                </div>
            </div>

            {{-- Links --}}
            <div class="grid grid-cols-2 gap-8 font-sans md:pl-8">
                <div>
                    <h4 class="font-sans text-xs font-bold uppercase tracking-wider text-gold-400">Programa</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ route('presentacion') }}" wire:navigate class="transition hover:text-white">Presentación</a></li>
                        <li><a href="{{ route('historia') }}" wire:navigate class="transition hover:text-white">Historia</a></li>
                        <li><a href="{{ route('mision') }}" wire:navigate class="transition hover:text-white">Misión y Visión</a></li>
                        <li><a href="{{ route('campo-laboral') }}" wire:navigate class="transition hover:text-white">Campo Laboral</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-sans text-xs font-bold uppercase tracking-wider text-gold-400">Académico</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/60">
                        <li><a href="{{ route('plan-2023') }}" wire:navigate class="transition hover:text-white">Plan de Estudios 2023</a></li>
                        <li><a href="{{ route('competencias') }}" wire:navigate class="transition hover:text-white">Competencias</a></li>
                        <li><a href="{{ route('revista') }}" wire:navigate class="transition hover:text-white">Revista Jurídica</a></li>
                        <li><a href="{{ route('docentes') }}" wire:navigate class="transition hover:text-white">Personal Docente</a></li>
                    </ul>
                </div>
            </div>

            {{-- CTA --}}
            <div>
                <a href="https://unasam.edu.pe" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-gold-400 px-5 py-3 font-sans text-sm font-medium text-gold-400 transition hover:bg-gold-400 hover:text-navy-900">
                    Portal UNASAM
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M7 7h10v10"/></svg>
                </a>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 font-sans text-sm text-white/40 sm:flex-row">
            <p>© {{ date('Y') }} UNASAM — Programa de Estudios de Derecho y Ciencias Políticas.</p>
            <p class="font-serif text-base italic text-gold-500">Orabunt Causas Melius</p>
        </div>
    </div>
</footer>
