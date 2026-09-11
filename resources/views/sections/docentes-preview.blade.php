{{-- Plana docente en portada.

     Ocupa el lugar del índice «¿quién eres?» que había aquí (Postulantes /
     Estudiantes / Docentes), que desde el reagrupamiento del menú repetía los
     mismos enlaces que la navegación: tres columnas para decir lo que la barra
     superior ya decía.

     A cambio entra contenido real y verificable. PRODUCT.md sitúa como audiencia
     prioritaria a juristas e investigadores que evalúan al programa, y para esa
     lectura la plana docente —con grado y especialidad— es la prueba más directa
     de sustancia académica. Los retratos ya estaban publicados y solo se llegaba
     a ellos entrando a una página interior. --}}
@if ($docentesPortada->isNotEmpty())
    <section class="border-y border-stone-200 bg-paper py-20 md:py-24">
        <div class="mx-auto max-w-7xl px-6">
            <div class="reveal flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Plana docente</p>
                    <h2 class="mt-2 font-serif text-3xl text-navy-900 md:text-4xl">Quiénes enseñan Derecho en la UNASAM</h2>
                </div>
                <a href="{{ route('docentes') }}" wire:navigate.hover class="link-arrow group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
                    Ver los {{ $docentesTotal }} docentes
                    <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            <div class="stagger-children mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($docentesPortada as $docente)
                    <div class="reveal">
                        <x-docente-card :docente="$docente" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
