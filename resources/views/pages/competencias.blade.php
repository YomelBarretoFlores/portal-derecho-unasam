@extends('layouts.app')

@section('title', 'Competencias — Derecho UNASAM')
@section('description', 'Competencias generales y específicas que desarrolla el Programa de Estudios de Derecho y Ciencias Políticas de la UNASAM.')

@section('content')
    <x-page-hero seccion="Académico" title="Competencias"
        subtitle="Capacidades generales y específicas que desarrolla el Programa de Estudios." />

    <section class="mx-auto max-w-4xl px-6 py-20">
        @if (collect($grupos)->isEmpty())
            <x-empty-state title="Competencias" description="Las competencias generales y específicas se publicarán conforme se aprueben." />
        @else
        <div class="space-y-16">
            @foreach ($grupos as $grupo)
                <div>
                    <div class="reveal">
                        <div class="accent-line"></div>
                        <h2 class="mt-4 text-2xl font-semibold text-navy-900">{{ $grupo['grupo'] }}</h2>
                    </div>

                    <div class="mt-8 space-y-12">
                        @foreach ($grupo['planes'] as $plan)
                            <div class="reveal">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold text-navy-700">{{ $plan['titulo'] }}</h3>
                                    @if ($plan['destacado'])
                                        <span class="rounded-none bg-navy-50 px-3 py-1 font-sans text-[11px] font-bold uppercase tracking-wide text-navy-700">Vigente</span>
                                    @endif
                                </div>

                                <div class="mt-5 space-y-4">
                                    @foreach ($plan['items'] as $i => $item)
                                        <div class="flex gap-5 rounded-2xl border border-stone-200 bg-white p-6">
                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-navy-900 font-sans text-xs font-semibold text-white">{{ $grupo['prefijo'] }}{{ $i + 1 }}</span>
                                            <div class="pt-0.5">
                                                @if (! empty($item['nombre']))
                                                    <p class="font-semibold text-navy-900">{{ $item['nombre'] }}</p>
                                                @endif
                                                <p class="@if (! empty($item['nombre'])) mt-1 @endif font-sans leading-relaxed text-stone-600">{{ $item['texto'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </section>
@endsection
