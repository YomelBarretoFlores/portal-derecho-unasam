@props(['revista'])

@php
    /**
     * Credenciales académicas de la revista.
     *
     * Es la idea aprovechable de la referencia PUCP —su portada dedica una banda
     * entera a rankings e indizaciones—, pero ejecutada como la propia Derecho
     * PUCP lo hace en su sitio de revista: datos verificables separados por
     * reglas finas, no medallones con volumen. Para un evaluador de indexación
     * esto es lo primero que busca; para el resto, es la prueba de que la
     * revista tiene proceso editorial de verdad.
     *
     * Solo se muestran los campos con contenido: un ISSN vacío no se rellena
     * con «en trámite» ni con un guion. La revista es nueva y aún no lo tiene.
     */
    $credenciales = collect([
        ['ISSN', $revista->issn ?? null],
        ['Arbitraje', $revista->sistema_arbitraje ?? null],
        ['Norma de citación', $revista->norma_citacion ?? null],
        ['Periodicidad', $revista->periodicidad ?? null],
        ['Modalidad', $revista->modalidad ?? null],
        ['Idiomas', is_array($revista->idiomas ?? null)
            ? collect($revista->idiomas)->map(fn (string $i): string => ['es' => 'Español', 'en' => 'Inglés', 'pt' => 'Portugués'][$i] ?? strtoupper($i))->implode(' · ')
            : ($revista->idiomas ?? null)],
    ])->filter(fn (array $par): bool => filled($par[1]));
@endphp

@if ($credenciales->count() >= 3)
    <div {{ $attributes->class(['reveal mt-12 border-t border-stone-300 pt-8']) }}>
        <p class="eyebrow">Credenciales académicas</p>
        <dl class="mt-6 grid gap-px border border-stone-200 bg-stone-200 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($credenciales as [$etiqueta, $valor])
                <div class="bg-white p-5">
                    <dt class="text-[11px] font-semibold uppercase tracking-[0.14em] text-navy-700">{{ $etiqueta }}</dt>
                    <dd class="mt-2 text-sm leading-relaxed text-stone-600">{{ $valor }}</dd>
                </div>
            @endforeach

            {{-- La rejilla usa gap-px sobre un fondo gris para dibujar las reglas
                 de 1px; sin este relleno, las celdas sobrantes de la última fila
                 mostrarían ese gris y parecerían casillas rotas. --}}
            @for ($i = 0; $i < (3 - $credenciales->count() % 3) % 3; $i++)
                <div class="hidden bg-white lg:block" aria-hidden="true"></div>
            @endfor
        </dl>
    </div>
@endif
