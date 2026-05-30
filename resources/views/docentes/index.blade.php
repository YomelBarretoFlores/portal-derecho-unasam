@extends('layouts.app')

@section('title', 'Personal Docente — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="Personal Docente"
        subtitle="Nuestra plana docente, especialistas en las distintas ramas del derecho." />

    <section class="mx-auto max-w-7xl px-6 py-16">
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($docentes as $i => $docente)
                <x-reveal :delay="($i % 2) * 0.06">
                    <x-docente-card :docente="$docente" />
                </x-reveal>
            @endforeach
        </div>
    </section>
@endsection
