@extends('layouts.app')

@section('content')
    @include('sections.hero-split')
    @include('sections.about')
    <div class="section-divider"></div>
    @include('sections.audiencias')
    @include('sections.accesos')
    @include('sections.revista-preview')
    @include('sections.blog-preview')
    @include('sections.stats')
@endsection
