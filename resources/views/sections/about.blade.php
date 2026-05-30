{{-- Sección About --}}
<section class="mx-auto max-w-7xl px-6 py-24">
    <div class="grid gap-16 lg:grid-cols-[1fr_420px]">

        {{-- Texto --}}
        <div class="reveal">
            <div class="gold-line"></div>
            <h2 class="mt-5 max-w-xl text-3xl font-semibold leading-tight text-navy-900 md:text-4xl">
                Formando profesionales del derecho desde 1986
            </h2>
            <div class="mt-6 space-y-4 font-sans text-[17px] leading-relaxed text-gray-600">
                <p>
                    El Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM forma abogados
                    con sólida base jurídica, sentido ético y compromiso con el desarrollo de la región
                    Áncash y del país.
                </p>
                <p>
                    Nuestro plan de estudios combina la formación teórica con la práctica profesional,
                    la investigación jurídica y la responsabilidad social, preparando a los estudiantes
                    para los desafíos del ejercicio del derecho en el siglo XXI.
                </p>
            </div>
            <div class="mt-8 flex flex-wrap gap-x-8 gap-y-3 font-sans">
                @foreach ([['Presentación', route('presentacion')], ['Historia', route('historia')], ['Campo Laboral', route('campo-laboral')]] as [$texto, $url])
                    <a href="{{ $url }}" wire:navigate class="group inline-flex items-center gap-1.5 text-sm font-semibold text-navy-700">
                        {{ $texto }}
                        <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="reveal space-y-4" data-reveal-delay="0.12">
            <div class="rounded-2xl bg-navy-900 p-7 text-white">
                <span class="font-serif text-5xl leading-none text-gold-400/50">“</span>
                <p class="-mt-2 font-serif text-xl italic leading-relaxed">
                    Del esfuerzo de sus hijos, depende el progreso de los pueblos.
                </p>
            </div>
            @foreach ([['Misión y Visión', 'Nuestro propósito y horizonte', route('mision')], ['Plan de Estudios 2023', 'Malla curricular vigente', route('plan-2023')], ['Objetivos Educacionales', 'Lo que buscamos lograr', route('objetivos')]] as [$titulo, $desc, $url])
                <a href="{{ $url }}" wire:navigate class="card-hover flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-5">
                    <div>
                        <h3 class="text-base font-semibold text-navy-900">{{ $titulo }}</h3>
                        <p class="font-sans text-sm text-gray-500">{{ $desc }}</p>
                    </div>
                    <svg class="h-5 w-5 text-gold-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    </div>
</section>
