@extends('layouts.app')

@section('title', 'Personal Docente — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="Personal Docente"
        subtitle="Nuestra plana docente, especialistas en las distintas ramas del derecho." />

    <section class="mx-auto max-w-7xl px-6 py-16">
        @if ($docentes->isEmpty())
            <div class="mx-auto max-w-2xl rounded-2xl border border-dashed border-stone-200 bg-paper px-6 py-16 text-center">
                <p class="font-sans text-stone-500">Aún no hay docentes publicados.</p>
            </div>
        @else
            <div class="stagger-children grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($docentes as $docente)
                    <div class="reveal">
                        <x-docente-card :docente="$docente" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
