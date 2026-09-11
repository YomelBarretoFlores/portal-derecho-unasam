{{-- Accesos directos administrables desde el panel.

     Antes esta sección abría con un índice «¿quién eres?» escrito a mano en la
     plantilla —Postulantes / Estudiantes / Docentes, con sus enlaces—, que desde
     que el menú se reagrupó por audiencia repetía exactamente lo mismo que la
     barra superior. Se retira el índice; los accesos se quedan, porque esos sí
     los administra el equipo desde el panel. --}}
@if ($accesos->isNotEmpty())
    @php $totalAccesos = $accesos->count(); @endphp
    <section class="mx-auto max-w-7xl px-6 py-20">
        <div class="reveal flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow">{{ $home['home_accesos_eyebrow'] }}</p>
                <h2 class="mt-2 font-serif text-3xl text-navy-900 md:text-4xl">{{ $home['home_accesos_titulo'] }}</h2>
            </div>
            <p class="max-w-md text-sm leading-6 text-stone-500">
                Enlaces administrados desde el portal para consultas académicas frecuentes.
            </p>
        </div>

        <div @class([
            'mt-8 grid gap-px overflow-hidden border border-stone-200 bg-stone-200',
            'md:grid-cols-2' => $totalAccesos === 2,
            'md:grid-cols-2 lg:grid-cols-3' => $totalAccesos === 3,
            'sm:grid-cols-2 lg:grid-cols-4' => $totalAccesos >= 4,
        ])>
            @foreach ($accesos as $a)
                <x-acceso-card :titulo="$a->titulo" :descripcion="$a->descripcion" :href="$a->url" />
            @endforeach
        </div>
    </section>
@endif
