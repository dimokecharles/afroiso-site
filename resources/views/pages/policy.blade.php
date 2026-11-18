@extends('layouts.app')
@section('title', $title)

@section('content')
<section class="page-head">
    <div class="container">
        <h1>{{ $title }}</h1>
    </div>
</section>
<section class="section">
    <div class="container narrow prose">
        {{-- Replace this paragraph with the approved policy text. --}}
        <p>This policy is being finalised and will be published here. For questions in the meantime, contact <a href="mailto:{{ config('afroiso.email') }}">{{ config('afroiso.email') }}</a>.</p>
    </div>
</section>
@endsection
