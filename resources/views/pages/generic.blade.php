@extends('layouts.app')

@section('title', $titulo . ' — Derecho UNASAM')
{{-- Página marcador "en preparación": no debe indexarse hasta tener contenido real --}}
@section('robots', 'noindex, follow')

@section('content')
    <x-page-hero :seccion="$seccion" :title="$titulo" />

    <section class="mx-auto max-w-3xl px-6 py-24 text-center">
        <x-reveal>
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-navy-50">
                <svg class="h-8 w-8 text-navy-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 4H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2z"/></svg>
            </div>
            <h2 class="mt-6 text-2xl font-semibold text-navy-900">Contenido en preparación</h2>
            <p class="mx-auto mt-3 max-w-md font-sans leading-relaxed text-stone-500">
                Esta sección («{{ $titulo }}») estará disponible próximamente. El contenido se
                administrará desde el panel del CMS una vez conectado.
            </p>
            <x-button :href="route('home')" variant="ghost" class="mt-8">Volver al inicio</x-button>
        </x-reveal>
    </section>
@endsection
