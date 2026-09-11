@props(['actual' => null])

@php
    /**
     * Cierre de las páginas de texto de la revista.
     *
     * Varias son muy cortas —indexación, preservación y privacidad no llegan a
     * doscientos caracteres— y terminaban en medio metro de vacío antes del pie:
     * el contenedor reserva la altura de la ventana y no había nada que la
     * ocupara. Redactar esos textos es trabajo del equipo editorial, pero una
     * página corta no tiene por qué parecer abandonada.
     *
     * Se ofrecen las tres secciones siguientes, saltando la que se está leyendo.
     */
    $siguientes = collect([
        ['revista.normas', 'Normas para autores', 'Requisitos de presentación y evaluación.'],
        ['revista.politicas', 'Políticas editoriales', 'Criterios de aceptación y proceso editorial.'],
        ['revista.formatos', 'Formatos y plantillas', 'Documentos editables para presentar un manuscrito.'],
        ['revista.comite-editorial', 'Comité editorial', 'Quién decide sobre los manuscritos.'],
        ['revista.indexacion', 'Indexación', 'Bases y catálogos donde figura la revista.'],
        ['revista.envios', 'Envíos', 'Presentar un manuscrito sin crear una cuenta.'],
    ])->reject(fn (array $s): bool => request()->routeIs($s[0]))->take(3);
@endphp

<section class="border-t border-stone-200 bg-paper">
    <div class="mx-auto max-w-7xl px-6 py-14">
        <p class="eyebrow">Continuar en la revista</p>
        <div class="mt-6 grid gap-px border border-stone-200 bg-stone-200 md:grid-cols-3">
            @foreach ($siguientes as [$ruta, $titulo, $descripcion])
                <a href="{{ route($ruta) }}" wire:navigate.hover
                   class="group flex flex-col bg-white p-6 transition-colors hover:bg-paper">
                    <h2 class="font-serif text-xl text-navy-900">{{ $titulo }}</h2>
                    <p class="mt-2 grow text-sm leading-relaxed text-stone-600">{{ $descripcion }}</p>
                    <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
                        Consultar
                        <svg class="h-4 w-4 transition group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
