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
                    <label class="{{ $label }}">Título</label>
                    <input type="text" wire:model="resumen_titulo" class="{{ $field }}">
                </div>
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

        {{-- Inicio --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Inicio</h2>
            <p class="mt-1 text-xs text-gray-500">Las cifras del hero y los gráficos salen de «Estadísticas».</p>

            <h3 class="mt-4 text-sm font-semibold text-gray-700 dark:text-gray-200">Hero</h3>
            <div class="mt-2 space-y-4">
                <div>
                    <label class="{{ $label }}">Título</label>
                    <input type="text" wire:model="home_hero_titulo" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Subtítulo</label>
                    <textarea wire:model="home_hero_subtitulo" rows="2" class="{{ $field }}"></textarea>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">Botón 1</label>
                        <input type="text" wire:model="home_hero_cta1" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">Botón 2</label>
                        <input type="text" wire:model="home_hero_cta2" class="{{ $field }}">
                    </div>
                </div>
            </div>

            <h3 class="mt-6 text-sm font-semibold text-gray-700 dark:text-gray-200">Sección «El programa»</h3>
            <div class="mt-2 space-y-4">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="{{ $label }}">Antetítulo</label>
                        <input type="text" wire:model="home_about_eyebrow" class="{{ $field }}">
                    </div>
                    <div>
                        <label class="{{ $label }}">Título</label>
                        <input type="text" wire:model="home_about_titulo" class="{{ $field }}">
                    </div>
                </div>
                <div>
                    <label class="{{ $label }}">Cuerpo (varios párrafos)</label>
                    <textarea wire:model="home_about_cuerpo" rows="5" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Cita / frase destacada</label>
                    <textarea wire:model="home_about_cita" rows="2" class="{{ $field }}"></textarea>
                </div>
            </div>

            <h3 class="mt-6 text-sm font-semibold text-gray-700 dark:text-gray-200">Encabezados de secciones</h3>
            <div class="mt-2 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Accesos — antetítulo</label>
                    <input type="text" wire:model="home_accesos_eyebrow" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Accesos — título</label>
                    <input type="text" wire:model="home_accesos_titulo" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Cifras — antetítulo</label>
                    <input type="text" wire:model="home_stats_eyebrow" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Cifras — título</label>
                    <input type="text" wire:model="home_stats_titulo" class="{{ $field }}">
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Cifras — narrativa</label>
                    <textarea wire:model="home_stats_narrativa" rows="2" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Revista — antetítulo</label>
                    <input type="text" wire:model="home_revista_eyebrow" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Revista — título</label>
                    <input type="text" wire:model="home_revista_titulo" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Revista — etiqueta (volumen)</label>
                    <input type="text" wire:model="home_revista_badge" class="{{ $field }}">
                </div>
                <div></div>
                <div>
                    <label class="{{ $label }}">Blog — antetítulo</label>
                    <input type="text" wire:model="home_blog_eyebrow" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Blog — título</label>
                    <input type="text" wire:model="home_blog_titulo" class="{{ $field }}">
                </div>
            </div>
        </section>

        {{-- Plan de Estudios --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Plan de Estudios</h2>
            <p class="mt-1 text-xs text-gray-500">Los cursos por ciclo (malla) se editan en «Plan de Estudios (cursos)».</p>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div>
                    <label class="{{ $label }}">Grado académico</label>
                    <input type="text" wire:model="plan_grado" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Título profesional</label>
                    <input type="text" wire:model="plan_titulo_prof" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Modalidad</label>
                    <input type="text" wire:model="plan_modalidad" class="{{ $field }}">
                </div>
                <div class="md:col-span-3">
                    <label class="{{ $label }}">Introducción de la malla (opcional)</label>
                    <textarea wire:model="plan_intro" rows="2" class="{{ $field }}"></textarea>
                </div>
                <div class="md:col-span-3">
                    <label class="{{ $label }}">Enlace al PDF de la malla</label>
                    <input type="text" wire:model="plan_pdf_url" class="{{ $field }}">
                </div>
                <div class="md:col-span-3">
                    <label class="{{ $label }}">Enlace al plan en línea (SGA)</label>
                    <input type="text" wire:model="plan_sga_url" class="{{ $field }}">
                </div>
            </div>
        </section>

        {{-- Footer, contacto y SEO --}}
        <section class="rounded-xl border border-gray-200 bg-white p-6 dark:border-white/10 dark:bg-gray-900">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Footer, contacto y SEO</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="{{ $label }}">Marca (nombre)</label>
                    <input type="text" wire:model="footer_marca" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Lema</label>
                    <input type="text" wire:model="lema" class="{{ $field }}">
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Descripción</label>
                    <textarea wire:model="footer_descripcion" rows="2" class="{{ $field }}"></textarea>
                </div>
                <div>
                    <label class="{{ $label }}">Dirección</label>
                    <input type="text" wire:model="contacto_direccion" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Teléfono</label>
                    <input type="text" wire:model="contacto_telefono" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Email</label>
                    <input type="text" wire:model="contacto_email" class="{{ $field }}">
                </div>
                <div></div>
                <div>
                    <label class="{{ $label }}">Botón CTA — texto</label>
                    <input type="text" wire:model="footer_cta_texto" class="{{ $field }}">
                </div>
                <div>
                    <label class="{{ $label }}">Botón CTA — URL</label>
                    <input type="text" wire:model="footer_cta_url" class="{{ $field }}">
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $label }}">SEO — título de la pestaña</label>
                    <input type="text" wire:model="seo_title" class="{{ $field }}">
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $label }}">SEO — descripción (meta)</label>
                    <textarea wire:model="seo_description" rows="2" class="{{ $field }}"></textarea>
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
