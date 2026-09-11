@extends('layouts.app')

@section('content')
    @include('sections.hero')
    @include('sections.about')

    {{-- La producción académica y los datos verificables van antes que la
         actualidad: PRODUCT.md fija como audiencia prioritaria a quien evalúa al
         programa, y esa lectura busca sustancia, no novedades. --}}
    @include('sections.revista-preview')
    @include('sections.stats')
    @include('sections.docentes-preview')

    @include('sections.blog-preview')
    @include('sections.accesos')
@endsection
