@props(['label', 'items' => []])

@php
    $active = collect($items)->contains(fn (array $item): bool => url()->current() === $item[1]);
@endphp

{{-- Dropdown accesible: abre por hover (ratón) y por el botón (clic, toque, Enter
     o Espacio); cierra con Escape devolviendo el foco al disparador.

     Abrir también con @focusin lo dejaba inservible al tocarlo: en un dispositivo
     táctil el toque enfoca el botón —lo abre— y el clic del mismo gesto lo vuelve
     a cerrar. Con ratón no se notaba porque @mouseenter ya lo había abierto antes.
     El cierre por foco se conserva, pero solo cuando el foco sale del bloque. --}}
<div x-data="{ open: false }"
     @mouseenter="open = true" @mouseleave="open = false"
     @keydown.escape.stop="open = false; $refs.trigger.focus()"
     @focusout="if (! $el.contains($event.relatedTarget)) open = false"
     class="relative">
    <button x-ref="trigger" type="button" @click="open = !open"
            aria-haspopup="true" :aria-expanded="open"
            @if ($active) aria-current="page" @endif
            class="flex items-center gap-1 px-3.5 py-2 text-[15px] font-medium transition hover:bg-stone-100 hover:text-navy-900 {{ $active ? 'bg-stone-100 text-navy-950' : 'text-navy-800' }}">
        {{ $label }}
        <svg class="h-3.5 w-3.5 transition" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="absolute left-0 top-full z-50 min-w-56 pt-2">
        <div role="menu" aria-label="{{ $label }}" class="overflow-hidden border border-stone-200 bg-white py-2 shadow-card-lg">
            @foreach ($items as [$texto, $url])
                <a href="{{ $url }}" wire:navigate.hover role="menuitem" @click="open = false"
                   class="block px-4 py-2.5 text-sm text-stone-600 transition-colors duration-150 hover:bg-stone-50 hover:text-navy-900">
                    {{ $texto }}
                </a>
            @endforeach
        </div>
    </div>
</div>
