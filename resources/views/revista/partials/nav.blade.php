@php
    $links = [
        ['revista.actual', 'Actual'], ['revista.archivos', 'Archivos'], ['revista.politicas', 'Políticas editoriales'],
        ['revista.comite-editorial', 'Comité editorial'], ['revista.comite-cientifico', 'Comité científico'],
        ['revista.avisos', 'Avisos'], ['revista.envios', 'Envíos'],
    ];
    $acercaDe = [
        ['revista.normas', 'Normas para autores'], ['revista.formatos', 'Formatos y plantillas'],
        ['revista.sobre', 'Sobre la revista'], ['revista.indexacion', 'Indexación'],
        ['revista.contacto', 'Contacto'], ['revista.privacidad', 'Declaración de privacidad'],
        ['revista.preservacion', 'Preservación digital'], ['revista.envios.consulta', 'Consultar mi envío'],
    ];
    $acercaDeActivo = collect($acercaDe)->contains(fn (array $item): bool => request()->routeIs($item[0]));

    // Rótulo de la sección en curso: en móvil el disparador tiene que decir dónde
    // está el lector, porque la lista completa va plegada.
    $seccionActual = collect($links)->merge($acercaDe)
        ->first(fn (array $item): bool => request()->routeIs($item[0]))[1]
        ?? 'Presentación';
@endphp

<nav class="border-b border-stone-200 bg-white" aria-label="Navegación de la revista">

    {{-- Móvil: lista plegable.

         Antes esto era la misma tira horizontal del escritorio con overflow-x-auto.
         A 380px solo cabían dos secciones y media: el resto quedaba fuera de la
         pantalla sin ningún indicio de que hubiera más, la sección en curso podía
         no verse —en «Envíos», por ejemplo, la tira empezaba en «Actual»— y el
         botón «Acerca de», que guarda ocho destinos (normas para autores, formatos,
         consulta de envío…), era directamente inalcanzable sin arrastrar de lado. --}}
    <div x-data="{ abierto: false }" class="lg:hidden"
         @keydown.escape.stop="abierto = false; $refs.disparador.focus()">
        <button x-ref="disparador" type="button" @click="abierto = ! abierto"
                :aria-expanded="abierto" aria-controls="revista-secciones"
                class="flex w-full items-center justify-between gap-3 px-6 py-3 text-left text-sm font-medium text-navy-900">
            <span class="min-w-0">
                <span class="block text-[11px] font-semibold uppercase tracking-[0.12em] text-stone-500">Secciones de la revista</span>
                <span class="block truncate">{{ $seccionActual }}</span>
            </span>
            <x-ui-icon name="chevron-down" class="h-4 w-4 shrink-0 text-stone-500 transition" ::class="abierto && 'rotate-180'" />
        </button>

        <div id="revista-secciones" x-show="abierto" x-cloak
             class="border-t border-stone-200 bg-paper px-6 py-3">
            @foreach ($links as [$routeName, $label])
                <a href="{{ route($routeName) }}" wire:navigate.hover @click="abierto = false"
                   @if (request()->routeIs($routeName)) aria-current="page" @endif
                   @class([
                       'block border-l-2 py-2 pl-3 text-sm transition-colors',
                       'border-navy-900 font-medium text-navy-900' => request()->routeIs($routeName),
                       'border-transparent text-stone-600 hover:text-navy-900' => ! request()->routeIs($routeName),
                   ])>{{ $label }}</a>
            @endforeach

            <p class="mt-3 border-t border-stone-200 pt-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-stone-500">
                Acerca de
            </p>
            @foreach ($acercaDe as [$routeName, $label])
                <a href="{{ route($routeName) }}" wire:navigate.hover @click="abierto = false"
                   @if (request()->routeIs($routeName)) aria-current="page" @endif
                   @class([
                       'block border-l-2 py-2 pl-3 text-sm transition-colors',
                       'border-navy-900 font-medium text-navy-900' => request()->routeIs($routeName),
                       'border-transparent text-stone-600 hover:text-navy-900' => ! request()->routeIs($routeName),
                   ])>{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Escritorio: la tira de siempre. --}}
    <div class="mx-auto hidden max-w-7xl items-center gap-1 px-6 py-3 text-sm lg:flex">
        @foreach ($links as [$routeName, $label])
            <a href="{{ route($routeName) }}" wire:navigate.hover
               @if (request()->routeIs($routeName)) aria-current="page" @endif
               @class([
                'shrink-0 border px-3 py-2 font-medium transition-colors',
                'border-navy-900 bg-navy-900 text-white' => request()->routeIs($routeName),
                'border-transparent text-stone-600 hover:border-stone-300 hover:text-navy-900' => ! request()->routeIs($routeName),
            ])>{{ $label }}</a>
        @endforeach

        {{-- Desplegable accesible: mismo patrón que x-nav-dropdown (foco y Escape). --}}
        {{-- El botón es el único que abre y cierra. Abrir también con @focusin
             lo dejaba inservible con ratón: el foco lo abría y el clic siguiente
             lo cerraba en el mismo gesto. Con Enter o Espacio se dispara igual
             el evento de clic, así que el teclado no pierde nada. --}}
        <div x-data="{ open: false }"
             @keydown.escape.stop="open = false; $refs.trigger.focus()"
             @click.outside="open = false"
             @focusout="if (! $el.contains($event.relatedTarget)) open = false"
             class="relative shrink-0">
            <button x-ref="trigger" type="button" @click="open = ! open"
                    aria-haspopup="true" :aria-expanded="open"
                    @if ($acercaDeActivo) aria-current="page" @endif
                    @class([
                        'flex items-center gap-1.5 border px-3 py-2 font-medium transition-colors',
                        'border-navy-900 bg-navy-900 text-white' => $acercaDeActivo,
                        'border-transparent text-stone-600 hover:border-stone-300 hover:text-navy-900' => ! $acercaDeActivo,
                    ])>
                Acerca de
                <x-ui-icon name="chevron-down" class="h-3.5 w-3.5 transition" ::class="open && 'rotate-180'" />
            </button>

            <div x-show="open" x-cloak
                 class="absolute right-0 top-full z-40 mt-1 min-w-64 border border-stone-200 bg-white p-2 shadow-card-lg">
                <div role="menu" aria-label="Acerca de la revista">
                    @foreach ($acercaDe as [$routeName, $label])
                        <a class="block px-3 py-2 text-stone-600 hover:bg-paper hover:text-navy-900"
                           href="{{ route($routeName) }}" wire:navigate.hover role="menuitem" @click="open = false"
                           @if (request()->routeIs($routeName)) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</nav>
