@props(['ajustes'])

{{-- Franja de utilidad compacta sobre la navegación principal. --}}
<div class="hidden border-b border-white/5 bg-navy-950 sm:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-1.5 text-[11px] text-white/65">
        <p class="tracking-[0.04em]">
            Facultad de Derecho y Ciencias Políticas · UNASAM
        </p>
        <div class="flex items-center gap-5">
            @if (! empty($ajustes['contacto_telefono']))
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $ajustes['contacto_telefono']) }}"
                   class="flex items-center gap-1.5 transition hover:text-white">
                    <svg class="h-3.5 w-3.5 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 5.5A2.5 2.5 0 0 1 5.5 3h2L9 7l-2 1.5a12 12 0 0 0 6.5 6.5L15 13l4 1.5v2a2.5 2.5 0 0 1-2.5 2.5A14 14 0 0 1 3 5.5z"/></svg>
                    {{ $ajustes['contacto_telefono'] }}
                </a>
            @endif
            @if (! empty($ajustes['contacto_email']))
                <a href="mailto:{{ $ajustes['contacto_email'] }}"
                   class="flex items-center gap-1.5 transition hover:text-white">
                    <svg class="h-3.5 w-3.5 text-white/40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    {{ $ajustes['contacto_email'] }}
                </a>
            @endif
        </div>
    </div>
</div>
