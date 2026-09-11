@extends('layouts.app')
@section('title','Consultar envío — '.$revista->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" title="Consultar mi envío" subtitle="Revisa en qué fase editorial se encuentra tu manuscrito." />

<section class="mx-auto max-w-5xl px-6 py-16 md:py-20">
    <div class="grid gap-12 lg:grid-cols-[22rem_minmax(0,1fr)] lg:items-start">

        <form method="post" action="{{ route('revista.envios.consulta.buscar') }}" class="border-t-2 border-navy-900 bg-paper p-6">
            @csrf
            <h2 class="text-2xl">Datos de seguimiento</h2>
            <p class="mt-3 text-sm leading-relaxed text-stone-600">Introduce el código que recibiste al enviar tu manuscrito y el mismo correo institucional con el que lo presentaste.</p>

            <label class="mt-6 block text-sm font-semibold text-navy-900" for="consulta-codigo">Código de seguimiento</label>
            <input id="consulta-codigo" name="codigo_seguimiento" value="{{ old('codigo_seguimiento') }}" required
                   placeholder="DYC-XXXXXXXXXXXX" autocomplete="off"
                   @if($errors->getBag('consulta')->has('codigo_seguimiento')) aria-describedby="consulta-errores" aria-invalid="true" @endif
                   class="mt-2 w-full border border-stone-300 bg-white px-3 py-2 font-mono uppercase">

            <label class="mt-5 block text-sm font-semibold text-navy-900" for="consulta-email">Correo institucional</label>
            <input id="consulta-email" type="email" name="email_consulta" value="{{ old('email_consulta') }}" required
                   @if($errors->getBag('consulta')->has('codigo_seguimiento')) aria-describedby="consulta-errores" @endif
                   class="mt-2 w-full border border-stone-300 bg-white px-3 py-2">

            @if($errors->getBag('consulta')->any())
                <div id="consulta-errores" class="mt-5 border-l-4 border-red-600 bg-red-50 p-4" role="alert" tabindex="-1" x-data x-init="$el.focus()">
                    <ul class="list-disc pl-5 text-sm text-red-900">
                        @foreach($errors->getBag('consulta')->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <button class="btn btn-primary mt-6 w-full" type="submit">Consultar estado</button>

            <p class="mt-6 border-t border-stone-300 pt-5 text-sm leading-relaxed text-stone-600">
                ¿Perdiste tu código? Escribe a
                <a class="font-semibold text-navy-700 underline" href="mailto:{{ $revista->contacto_email }}">{{ $revista->contacto_email }}</a>
                desde el mismo correo con el que hiciste el envío y el equipo editorial te lo recuperará.
            </p>
        </form>

        <div>
            @if($resultado)
                <article class="border border-stone-200 bg-white">
                    <div class="border-b border-stone-200 bg-paper px-7 py-6">
                        <p class="eyebrow">Envío {{ $resultado['codigo'] }}</p>
                        <h2 class="mt-3 text-3xl leading-tight">{{ $resultado['titulo'] }}</h2>
                    </div>

                    <dl class="grid gap-px bg-stone-200 sm:grid-cols-2">
                        <div class="bg-white px-7 py-5">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Fase editorial</dt>
                            <dd class="mt-2 text-lg font-semibold text-navy-900">{{ $resultado['estado'] }}</dd>
                        </div>
                        <div class="bg-white px-7 py-5">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Tipo de contribución</dt>
                            <dd class="mt-2">{{ $resultado['tipo'] }}</dd>
                        </div>
                        <div class="bg-white px-7 py-5">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Recibido el</dt>
                            <dd class="mt-2">{{ \Illuminate\Support\Carbon::parse($resultado['recibido_en'])->translatedFormat('d \d\e F \d\e Y, H:i') }}</dd>
                        </div>
                        <div class="bg-white px-7 py-5">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Correcciones recibidas</dt>
                            <dd class="mt-2">{{ $resultado['versiones'] }}</dd>
                        </div>
                        @if($resultado['linea'])
                            <div class="bg-white px-7 py-5 sm:col-span-2">
                                <dt class="text-xs font-semibold uppercase tracking-wider text-stone-500">Línea de investigación</dt>
                                <dd class="mt-2">{{ $resultado['linea'] }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="border-t border-stone-200 px-7 py-6">
                        @if($resultado['admite_correccion'])
                            <p class="leading-relaxed">El equipo editorial ha observado tu manuscrito: <strong>puedes enviar tu versión corregida ahora</strong>.</p>
                            <a class="btn btn-primary mt-5" href="{{ route('revista.envios') }}#correccion">Enviar corrección</a>
                        @else
                            <p class="leading-relaxed text-stone-600">Tu manuscrito no está en fase de corrección en este momento. El equipo editorial se comunicará contigo por correo cuando haya novedades.</p>
                        @endif
                    </div>
                </article>
            @else
                <div class="editorial-empty">
                    <p class="eyebrow">Seguimiento editorial</p>
                    <h2 class="mt-3 text-3xl">Consulta el estado de tu manuscrito</h2>
                    <p class="mt-4 max-w-xl leading-relaxed">Completa el formulario con tu código de seguimiento y tu correo institucional. Verás la fase editorial en la que se encuentra tu envío y si puedes presentar una versión corregida.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
