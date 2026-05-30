@extends('layouts.app')

@section('title', 'Derecho y Ciencias Políticas — UNASAM')

@section('content')
    @include('sections.hero-split')
    @include('sections.about')
    @include('sections.accesos')
    @include('sections.revista-preview')
    @include('sections.blog-preview')
    @include('sections.stats')
@endsection
