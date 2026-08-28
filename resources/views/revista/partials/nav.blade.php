<nav class="border-b border-stone-200 bg-white" aria-label="Navegación de la revista">
    <div class="mx-auto flex max-w-7xl items-start gap-1 overflow-x-auto px-6 py-3 text-sm [scrollbar-width:thin] lg:items-center lg:overflow-visible">
        @php
            $links = [
                ['revista.actual', 'Actual'], ['revista.archivos', 'Archivos'], ['revista.politicas', 'Políticas editoriales'],
                ['revista.comite-editorial', 'Comité editorial'], ['revista.comite-cientifico', 'Comité científico'],
                ['revista.avisos', 'Avisos'], ['revista.envios', 'Envíos'],
            ];
        @endphp
        @foreach ($links as [$routeName, $label])
            <a href="{{ route($routeName) }}" wire:navigate.hover @class([
                'shrink-0 border px-3 py-2 font-medium transition-colors',
                'border-navy-900 bg-navy-900 text-white' => request()->routeIs($routeName),
                'border-transparent text-stone-600 hover:border-stone-300 hover:text-navy-900' => ! request()->routeIs($routeName),
            ])>{{ $label }}</a>
        @endforeach
        <details class="group relative shrink-0">
            <summary class="flex cursor-pointer list-none items-center gap-1.5 border border-transparent px-3 py-2 font-medium text-stone-600 hover:border-stone-300 hover:text-navy-900">Acerca de <x-ui-icon name="chevron-down" class="h-3.5 w-3.5 transition group-open:rotate-180" /></summary>
            <div class="fixed left-6 right-6 top-32 z-40 mt-1 border border-stone-200 bg-white p-2 shadow-card-lg lg:absolute lg:left-auto lg:right-0 lg:top-auto lg:min-w-64">
                @foreach ([['revista.sobre','Sobre la revista'],['revista.indexacion','Indexación'],['revista.contacto','Contacto'],['revista.privacidad','Declaración de privacidad'],['revista.preservacion','Preservación digital']] as [$routeName,$label])
                    <a class="block px-3 py-2 text-stone-600 hover:bg-paper hover:text-navy-900" href="{{ route($routeName) }}" wire:navigate.hover>{{ $label }}</a>
                @endforeach
            </div>
        </details>
    </div>
</nav>
