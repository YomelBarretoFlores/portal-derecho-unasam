<nav class="border-b border-stone-200 bg-white" aria-label="Navegación de la revista">
    <div class="mx-auto flex max-w-7xl items-start gap-1 overflow-x-auto px-6 py-3 text-sm [scrollbar-width:thin] lg:items-center lg:overflow-visible">
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
        @endphp
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
                 class="fixed left-6 right-6 top-32 z-40 mt-1 border border-stone-200 bg-white p-2 shadow-card-lg lg:absolute lg:left-auto lg:right-0 lg:top-auto lg:min-w-64">
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
