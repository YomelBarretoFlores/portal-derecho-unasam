{{-- Sección Accesos directos --}}
<section class="border-y border-stone-200 bg-paper py-24">
    <div class="mx-auto max-w-7xl px-6">
        <x-section-header :title="$home['home_accesos_titulo']" class="mb-12" />

        <div class="stagger-children grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($accesos as $a)
                <div class="reveal">
                    <x-acceso-card :titulo="$a->titulo" :descripcion="$a->descripcion" :href="$a->url" />
                </div>
            @endforeach
        </div>
    </div>
</section>
