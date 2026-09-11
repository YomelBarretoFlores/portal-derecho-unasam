@extends('layouts.app')
@section('title','Envíos — '.$revista->nombre_corto)
@section('content')
<x-page-hero seccion="Revista" title="Envíos" subtitle="Presentación pública de manuscritos sin crear una cuenta." />
<section class="mx-auto max-w-7xl px-6 py-16 md:py-20">
    @if($revista->introduccion_envios)<div class="prose-editorial max-w-3xl">{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($revista->introduccion_envios) }}</div>@endif
    <ol class="mt-12 grid gap-px border border-stone-200 bg-stone-200 sm:grid-cols-3 lg:grid-cols-6" aria-label="Flujo editorial">
        @foreach(['Envío','Verificación editorial','Respuesta por correo','Corrección','Revisión por pares','Decisión'] as $step)<li class="relative bg-white p-5"><span class="eyebrow">{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span><p class="mt-2 font-semibold text-navy-900">{{ $step }}</p>@unless($loop->last)<x-ui-icon name="arrow-right" class="absolute -right-3 top-1/2 z-10 hidden h-5 w-5 -translate-y-1/2 bg-white text-navy-500 lg:block" />@endunless</li>@endforeach
    </ol>
    <div class="mt-12 flex flex-wrap gap-3"><a class="btn btn-primary" href="{{ route('revista.normas') }}">Normas para autores</a><a class="btn btn-ghost" href="{{ route('revista.formatos') }}">Formatos y plantillas</a><a class="btn btn-ghost" href="{{ route('revista.envios.consulta') }}">Consultar mi envío</a></div>
    @if(session('submission_success'))
        <div class="mt-10 border-2 border-navy-900 bg-white" role="status" x-data="{ copiado: false }">
            <div class="border-b border-stone-200 bg-paper px-7 py-5">
                <p class="eyebrow">Envío recibido correctamente</p>
                <h2 class="mt-2 text-2xl">Guarda tu código de seguimiento</h2>
            </div>
            <div class="px-7 py-7">
                <p class="font-mono text-3xl font-bold tracking-wider text-navy-900 md:text-4xl" id="codigo-seguimiento">{{ session('submission_success') }}</p>
                <div class="mt-5 flex flex-wrap gap-3 print:hidden">
                    <button type="button" class="btn btn-primary"
                            x-on:click="navigator.clipboard.writeText('{{ session('submission_success') }}').then(() => { copiado = true; setTimeout(() => copiado = false, 2500) })">
                        <span x-show="! copiado">Copiar código</span>
                        <span x-show="copiado" x-cloak>Copiado</span>
                    </button>
                    <button type="button" class="btn btn-ghost" x-on:click="window.print()">Imprimir</button>
                    <a class="btn btn-ghost" href="{{ route('revista.envios.consulta') }}">Consultar estado</a>
                </div>
                <p class="mt-6 border-t border-stone-200 pt-5 leading-relaxed">
                    <strong>Sin este código no podrás enviar tu versión corregida</strong> ni consultar el estado de tu manuscrito. Anótalo o imprímelo antes de cerrar esta página.
                </p>
                <p class="mt-3 text-sm leading-relaxed text-stone-600">El equipo editorial revisará tu envío y se comunicará contigo por tu correo institucional.</p>
            </div>
        </div>
    @endif
    @if(session('correction_success'))<div class="mt-10 border-l-4 border-green-600 bg-green-50 p-6" role="status">La versión corregida fue recibida correctamente.</div>@endif
    @if(!$submissionsEnabled)
        <div class="editorial-empty mt-12"><p class="eyebrow">Recepción de manuscritos</p><h2 class="mt-3 text-3xl">Recepción en línea cerrada</h2><p class="mt-4 max-w-3xl leading-relaxed">Actualmente no se reciben manuscritos mediante el portal. Consulta las normas para autores y los formatos oficiales antes de preparar una propuesta editorial.</p></div>
    @else
        <div class="mt-14 grid gap-12 lg:grid-cols-[minmax(0,1fr)_24rem]">
            <form method="post" action="{{ route('revista.envios.store') }}" enctype="multipart/form-data" class="space-y-8">@csrf
                <input name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <fieldset class="grid gap-5 md:grid-cols-2"><legend class="mb-5 text-2xl font-semibold text-navy-900">Datos del autor</legend>
                    @foreach([['nombres','Nombres y apellidos','text'],['documento_identidad','DNI o pasaporte','text'],['afiliacion','Afiliación institucional','text'],['ciudad','Ciudad','text'],['pais','País','text'],['email_institucional','Correo institucional','email'],['whatsapp','WhatsApp con código de país','tel'],['orcid','ORCID (opcional)','text']] as [$name,$label,$type])<label class="block"><span class="text-sm font-semibold text-navy-900">{{ $label }}</span><input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}" @required($name!=='orcid') class="mt-2 w-full border border-stone-300 px-4 py-3"></label>@endforeach
                </fieldset>
                <fieldset class="grid gap-5 md:grid-cols-2"><legend class="mb-5 text-2xl font-semibold text-navy-900">Manuscrito</legend>
                    <label><span class="text-sm font-semibold text-navy-900">Tipo de contribución</span><select name="tipo_contribucion" required class="mt-2 w-full border border-stone-300 px-4 py-3"><option value="">Selecciona</option>@foreach(\App\Models\RevistaEnvio::TIPOS as $value=>$label)<option value="{{ $value }}" @selected(old('tipo_contribucion')===$value)>{{ $label }}</option>@endforeach</select></label>
                    <label><span class="text-sm font-semibold text-navy-900">Línea de investigación</span><select name="revista_linea_investigacion_id" required class="mt-2 w-full border border-stone-300 px-4 py-3"><option value="">Selecciona</option>@foreach($lineas as $linea)<option value="{{ $linea->id }}" @selected(old('revista_linea_investigacion_id')==$linea->id)>{{ $linea->nombre }}</option>@endforeach</select></label>
                    <label class="md:col-span-2"><span class="text-sm font-semibold text-navy-900">Título</span><input name="titulo" value="{{ old('titulo') }}" required maxlength="255" class="mt-2 w-full border border-stone-300 px-4 py-3"></label>
                    <label class="md:col-span-2"><span class="text-sm font-semibold text-navy-900">Resumen (máximo 200 palabras)</span><textarea name="resumen" required rows="7" class="mt-2 w-full border border-stone-300 px-4 py-3">{{ old('resumen') }}</textarea></label>
                    @for($i=0;$i<3;$i++)<label><span class="text-sm font-semibold text-navy-900">Coautor {{ $i+1 }} (opcional)</span><input name="coautores[]" value="{{ old('coautores.'.$i) }}" class="mt-2 w-full border border-stone-300 px-4 py-3"></label>@endfor
                </fieldset>
                <fieldset class="grid gap-5 md:grid-cols-2"><legend class="mb-5 text-2xl font-semibold text-navy-900">Archivos requeridos</legend>
                    @foreach([['manuscrito','Manuscrito DOCX','.docx'],['carta','Carta DOCX o PDF','.docx,.pdf'],['declaracion','Declaración DOCX o PDF','.docx,.pdf'],['constancia_estilo','Constancia de estilo PDF','.pdf']] as [$name,$label,$accept])<label><span class="text-sm font-semibold text-navy-900">{{ $label }}</span><input type="file" name="{{ $name }}" accept="{{ $accept }}" required class="mt-2 block w-full border border-stone-300 bg-white p-3 text-sm"></label>@endforeach
                </fieldset>
                <label class="flex gap-3"><input type="checkbox" name="consentimiento" value="1" required class="mt-1"><span class="text-sm leading-relaxed">Declaro que he leído las normas y autorizo el tratamiento de mis datos para gestionar este envío editorial.</span></label>
                @if($errors->getBag('default')->any())<div class="border-l-4 border-red-600 bg-red-50 p-5" role="alert" tabindex="-1" x-data x-init="$el.focus()"><p class="font-semibold text-red-900">Revisa los datos:</p><ul class="mt-2 list-disc pl-5 text-sm">@foreach($errors->getBag('default')->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <button class="btn btn-primary" type="submit">Enviar manuscrito</button>
            </form>
            <aside id="correccion" class="h-fit border-t-2 border-navy-900 bg-paper p-6"><h2 class="text-2xl">Enviar corrección</h2><p class="mt-3 text-sm leading-relaxed">Usa el código recibido y el mismo correo institucional.</p><form method="post" action="{{ route('revista.envios.correction') }}" enctype="multipart/form-data" class="mt-6 space-y-4">@csrf<input name="website_correction" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true"><label class="block text-sm font-semibold">Código<input name="codigo_seguimiento" required class="mt-2 w-full border border-stone-300 bg-white px-3 py-2"></label><label class="block text-sm font-semibold">Correo institucional<input type="email" name="email_institucional_correccion" required class="mt-2 w-full border border-stone-300 bg-white px-3 py-2"></label><label class="block text-sm font-semibold">DOCX corregido<input type="file" name="manuscrito_corregido" accept=".docx" required class="mt-2 block w-full border border-stone-300 bg-white p-2"></label><label class="block text-sm font-semibold">Nota opcional<textarea name="nota_autor" rows="3" class="mt-2 w-full border border-stone-300 bg-white p-2"></textarea></label>@if($errors->getBag('correccion')->any())<div class="border-l-4 border-red-600 bg-red-50 p-4" role="alert" tabindex="-1" x-data x-init="$el.focus()"><p class="text-sm font-semibold text-red-900">Revisa la corrección:</p><ul class="mt-2 list-disc pl-5 text-sm">@foreach($errors->getBag('correccion')->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif<button class="btn btn-ghost w-full" type="submit">Enviar corrección</button></form></aside>
        </div>
    @endif
</section>
@endsection
