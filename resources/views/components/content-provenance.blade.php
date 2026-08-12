@php
    $routeName = request()->route()?->getName() ?? '';
    $section = match (true) {
        str_starts_with($routeName, 'revista') => 'revista',
        str_starts_with($routeName, 'estadisticas') => 'estadisticas',
        default => $routeName,
    };
    $status = config("content.provenance.{$section}");
@endphp

@if (in_array($status, ['legacy', 'pending'], true))
    <aside class="border-b border-gold-200 bg-gold-100/45" aria-label="Procedencia del contenido">
        <div class="mx-auto flex max-w-7xl items-center gap-2.5 px-6 py-2 text-xs text-stone-600">
            <svg class="h-3.5 w-3.5 shrink-0 text-gold-600" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v6m0-9h.01"/></svg>
            <p class="leading-5">
                @if ($status === 'legacy')
                    <strong class="font-semibold text-navy-900">Contenido institucional heredado.</strong>
                    Pendiente de revisión o actualización.
                    <a class="font-semibold text-navy-800 underline underline-offset-2" href="{{ config('content.legacy_source') }}" target="_blank" rel="noopener">Ver fuente</a>
                @else
                    <strong class="font-semibold text-navy-900">Validación documental pendiente.</strong>
                    Esta sección puede no contener todavía información publicada.
                @endif
            </p>
        </div>
    </aside>
@endif
