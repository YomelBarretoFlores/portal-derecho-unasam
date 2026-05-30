{{-- Sección Accesos directos --}}
<section class="border-y border-stone-200 bg-paper py-24">
    <div class="mx-auto max-w-7xl px-6">
        <x-section-header eyebrow="Accesos directos" title="Explora el programa" class="mb-12" />

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['01', 'Plan de Estudios', 'Malla curricular 2023 y cursos por ciclo.', route('plan-2023')],
                ['02', 'Perfil de Egreso', 'Competencias del abogado que formamos.', route('perfil-egreso')],
                ['03', 'Competencias', 'Generales y específicas del programa.', route('competencias')],
                ['04', 'Personal Docente', 'Plana docente por especialidad.', route('docentes')],
            ] as $i => [$num, $titulo, $desc, $url])
                <x-reveal :delay="$i * 0.08">
                    <x-acceso-card :numero="$num" :titulo="$titulo" :descripcion="$desc" :href="$url" />
                </x-reveal>
            @endforeach
        </div>
    </div>
</section>
