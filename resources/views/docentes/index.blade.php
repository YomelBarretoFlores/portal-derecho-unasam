@extends('layouts.app')

@section('title', 'Personal Docente — Derecho UNASAM')

@section('content')
    <x-page-hero seccion="Institucional" title="Personal Docente" subtitle="Plana docente publicada por el Programa de Derecho y Ciencias Políticas." />
    <section class="mx-auto max-w-7xl px-6 py-16">
        @if ($docentes->isEmpty())
            <x-empty-state title="Nómina docente en revisión" description="Los perfiles se publican individualmente después de validar sus datos académicos y su documentación de respaldo." />
        @else
            <div @class([
                'grid gap-6',
                'max-w-sm' => $docentes->count() === 1,
                'max-w-4xl sm:grid-cols-2' => $docentes->count() === 2,
                'sm:grid-cols-2 lg:grid-cols-3' => $docentes->count() >= 3,
            ])>@foreach ($docentes as $docente)<x-docente-card :docente="$docente" />@endforeach</div>
            <div class="mt-10">{{ $docentes->links() }}</div>
        @endif
    </section>
@endsection
