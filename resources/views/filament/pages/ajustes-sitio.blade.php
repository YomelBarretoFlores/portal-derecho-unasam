<x-filament-panels::page>
    @php
        $field = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white';
        $label = 'text-sm font-medium text-gray-700 dark:text-gray-200';
    @endphp

    <form wire:submit="guardar" class="space-y-6">

        {{-- Presentación --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Presentación</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="{{ $label }}">Título</label>
                    <input type="text" wire:model="presentacion_titulo" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Cuerpo (puedes usar varios párrafos)</label>
                    <textarea wire:model="presentacion_cuerpo" rows="6" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Datos del programa</label>
                    <textarea wire:model="datos_programa" rows="6" class="{{ $field }}"
                              placeholder="Una línea por dato, formato «Etiqueta | Valor». Ej.: Duración | 5 años (10 ciclos)"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Una línea por dato, con el formato <code>Etiqueta | Valor</code>.</p>
                </div>
            </div>
        </section>

        {{-- Misión y Visión --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Misión y Visión</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Misión</label>
                    <textarea wire:model="mision" rows="6" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Visión</label>
                    <textarea wire:model="vision" rows="6" class="{{ $field }}"></textarea>
                </div>
            </div>
        </section>

        {{-- Historia (reseña) --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Historia — reseña «Nuestra trayectoria»</h2>
            <p class="mt-1 text-xs text-gray-500">Los hitos de la línea de tiempo se editan en «Historia (hitos)».</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="{{ $label }}">Título</label>
                    <input type="text" wire:model="historia_trayectoria_titulo" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Cuerpo</label>
                    <textarea wire:model="historia_trayectoria_cuerpo" rows="8" class="{{ $field }}"></textarea>
                </div>
            </div>
        </section>

        {{-- Resumen --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Resumen del Programa</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="{{ $label }}">Cuerpo principal</label>
                    <textarea wire:model="resumen_cuerpo" rows="5" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Cita legal 1 (Art. 40 — Ley 30220)</label>
                    <textarea wire:model="resumen_cita1" rows="4" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Cita legal 2 (Art. 79 — 2015)</label>
                    <textarea wire:model="resumen_cita2" rows="4" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Párrafo de cierre</label>
                    <textarea wire:model="resumen_cierre" rows="3" class="{{ $field }}"></textarea>
                </div>
            </div>
        </section>

        {{-- Perfiles --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Perfiles</h2>
            <p class="mt-1 text-xs text-gray-500">Las áreas del perfil de ingreso se editan en «Perfil de ingreso».</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="{{ $label }}">Perfil de ingreso específico</label>
                    <textarea wire:model="perfil_ingreso_especifico" rows="3" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Perfil de egreso 2023 (vigente)</label>
                    <textarea wire:model="perfil_egreso_2023" rows="6" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Perfil de egreso 2019</label>
                    <textarea wire:model="perfil_egreso_2019" rows="6" class="{{ $field }}"></textarea>
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <x-filament::button type="submit" size="lg">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
