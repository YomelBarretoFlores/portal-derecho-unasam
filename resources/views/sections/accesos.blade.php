{{-- Sección Accesos directos --}}
<section class="border-y border-stone-200 bg-paper py-24">
    <div class="mx-auto max-w-7xl px-6">
        <x-section-header :title="$home['home_accesos_titulo']" class="mb-12" />

        <div class="stagger-children grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Plan de Estudios', 'Malla curricular 2023 y cursos por ciclo.', route('plan-2023')],
                ['Perfil de Egreso', 'Competencias del abogado que formamos.', route('perfil-egreso')],
                ['Competencias', 'Generales y específicas del programa.', route('competencias')],
                ['Personal Docente', 'Plana docente por especialidad.', route('docentes')],
            ] as [$titulo, $desc, $url])
                <div class="reveal">
                    <x-acceso-card :titulo="$titulo" :descripcion="$desc" :href="$url" />
                </div>
            @endforeach
        </div>
    </div>
</section>
