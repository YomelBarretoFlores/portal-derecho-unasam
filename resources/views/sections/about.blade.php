{{-- Sección About --}}
<section class="mx-auto max-w-7xl px-6 py-20">
    <div class="grid gap-16 lg:grid-cols-[1fr_420px]">

        {{-- Texto --}}
        <div class="reveal">
            <span class="eyebrow">{{ $home['home_about_eyebrow'] }}</span>
            <h2 class="mt-3 max-w-xl text-3xl font-semibold tracking-tight text-navy-900 md:text-4xl">
                {{ $home['home_about_titulo'] }}
            </h2>
            <div class="mt-6 space-y-4 text-[17px] leading-relaxed text-stone-600">
                @foreach (preg_split('/\R{2,}/', trim((string) $home['home_about_cuerpo'])) as $parrafo)
                    <p>{{ $parrafo }}</p>
                @endforeach
            </div>
            {{-- Las cifras vivían en el hero, encima de la fotografía, como dos
                 contadores grandes. Ahí eran decoración: nadie llega a la
                 portada a saber cuántas áreas del derecho hay. Aquí sostienen el
                 párrafo que acaban de leer, que es lo que un dato debe hacer. --}}
            <dl class="mt-10 flex flex-wrap gap-x-14 gap-y-6 border-t border-stone-200 pt-7">
                @foreach ([['home_hero_stat1_label', 'home_hero_stat1_valor', 'home_hero_stat1_sufijo'],
                           ['home_hero_stat2_label', 'home_hero_stat2_valor', 'home_hero_stat2_sufijo']] as [$etiqueta, $valor, $sufijo])
                    @if (filled($home[$valor] ?? ''))
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.12em] text-navy-700">{{ $home[$etiqueta] }}</dt>
                            <dd class="num-editorial mt-2 text-4xl font-semibold tracking-tight"
                                data-count="{{ $home[$valor] }}" data-count-suffix="{{ $home[$sufijo] }}">{{ $home[$valor] }}{{ $home[$sufijo] }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>

            <div class="mt-8 flex flex-wrap gap-x-8 gap-y-3">
                @foreach ([['Presentación', route('presentacion')], ['Historia', route('historia')], ['Campo Laboral', route('campo-laboral')]] as [$texto, $url])
                    <a href="{{ $url }}" wire:navigate.hover class="link-arrow group inline-flex items-center gap-1.5 text-sm font-medium text-navy-700">
                        {{ $texto }}
                        <svg class="h-4 w-4 transition group-hover:translate-x-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="stagger-children space-y-4">
            <div class="reveal bg-navy-900 p-7 text-white">
                <div class="accent-line"></div>
                <p class="mt-4 text-xl italic leading-relaxed text-white/90">
                    {{ $home['home_about_cita'] }}
                </p>
            </div>
            @foreach ([['Misión y Visión', 'Nuestro propósito y horizonte', route('mision')], ['Plan de Estudios 2023', 'Malla curricular vigente', route('plan-2023')], ['Objetivos Educacionales', 'Lo que buscamos lograr', route('objetivos')]] as [$titulo, $desc, $url])
                <a href="{{ $url }}" wire:navigate.hover class="reveal card-hover flex items-center justify-between border border-stone-200 bg-white p-5">
                    <div>
                        <h3 class="text-base font-semibold text-navy-900">{{ $titulo }}</h3>
                        <p class="text-sm text-stone-500">{{ $desc }}</p>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-stone-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            @endforeach
        </div>
    </div>
</section>
