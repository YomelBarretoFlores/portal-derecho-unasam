{{-- Índice por audiencia ("¿quién eres?") — layout editorial de filas, NO rejilla de tarjetas.
     Se diferencia a propósito de la sección Accesos (que sí es grid temático). --}}
@php
    $audiencias = [
        [
            'titulo' => 'Postulantes',
            'descripcion' => 'Conoce el programa y cómo iniciar tu camino en el Derecho.',
            'enlaces' => [
                ['Presentación', route('presentacion')],
                ['Perfil de Ingreso', route('perfil-ingreso')],
                ['Plan de Estudios 2023', route('plan-2023')],
            ],
        ],
        [
            'titulo' => 'Estudiantes',
            'descripcion' => 'Malla, competencias y documentos para tu vida académica.',
            'enlaces' => [
                ['Plan de Estudios 2023', route('plan-2023')],
                ['Competencias', route('competencias')],
                ['Documentos Normativos', route('documentos')],
            ],
        ],
        [
            'titulo' => 'Docentes e investigadores',
            'descripcion' => 'Plana docente, publicaciones y comunicados del programa.',
            'enlaces' => [
                ['Personal Docente', route('docentes')],
                ['Revista Jurídica', route('revista')],
                ['Comunicados', route('comunicados')],
            ],
        ],
    ];
@endphp

@php $totalAccesos = $accesos->count(); @endphp

<section class="border-y border-stone-200 bg-paper py-20 md:py-24">
    <div class="mx-auto max-w-7xl px-6">
    <div class="grid gap-x-12 gap-y-10 lg:grid-cols-[19rem_1fr]">

        {{-- Encabezado a un lado (composición asimétrica) --}}
        <div class="reveal lg:pt-2">
            <p class="eyebrow">Orientación y recursos</p>
            <h2 class="mt-3 font-serif text-3xl text-navy-900 md:text-4xl">Explora el programa</h2>
            <p class="mt-3 max-w-xs leading-relaxed text-stone-500">
                Encuentra información según tu relación con la comunidad universitaria.
            </p>
        </div>

        {{-- Índice de filas con reglas finas --}}
        <div class="reveal rule-t" data-reveal-delay="0.08">
            @foreach ($audiencias as $aud)
                <div class="grid gap-x-8 gap-y-4 border-b border-stone-200 py-7 last:border-b-0 sm:grid-cols-[13rem_1fr]">
                    <div>
                        <h3 class="font-serif text-2xl font-medium text-navy-900">{{ $aud['titulo'] }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-stone-500">{{ $aud['descripcion'] }}</p>
                    </div>
                    <ul class="flex flex-wrap gap-x-7 gap-y-2.5 sm:self-center">
                        @foreach ($aud['enlaces'] as [$texto, $url])
                            <li>
                                <a href="{{ $url }}" wire:navigate.hover
                                   class="link-arrow inline-flex items-center gap-1.5 text-sm font-medium text-navy-700 hover:text-navy-900">
                                    <svg class="h-3.5 w-3.5 text-gold-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6" /></svg>
                                    {{ $texto }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    @if ($accesos->isNotEmpty())
        <div class="mt-12 border-t border-stone-300 pt-8">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="eyebrow">Recursos destacados</p>
                    <h3 class="mt-2 font-serif text-2xl font-semibold text-navy-900">Accesos directos</h3>
                </div>
                <p class="max-w-md text-sm leading-6 text-stone-500">Enlaces administrados desde el portal para consultas académicas frecuentes.</p>
            </div>
            <div @class([
                'mt-7 grid gap-px overflow-hidden border border-stone-200 bg-stone-200',
                'md:grid-cols-2' => $totalAccesos === 2,
                'md:grid-cols-2 lg:grid-cols-3' => $totalAccesos === 3,
                'sm:grid-cols-2 lg:grid-cols-4' => $totalAccesos >= 4,
            ])>
                @foreach ($accesos as $a)
                    <x-acceso-card :titulo="$a->titulo" :descripcion="$a->descripcion" :href="$a->url" />
                @endforeach
            </div>
        </div>
    @endif
    </div>
</section>
