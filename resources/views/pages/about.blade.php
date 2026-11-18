@extends('layouts.app')
@section('title', 'About')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">About the institute</div>
        <h1>A training institute, a consultancy and a publisher under one roof.</h1>
    </div>
</section>
<section class="section">
    <div class="container narrow prose">
        <p>AfroISO Institute &amp; Consultants is based in Harare, Zimbabwe. We work with organisations and professionals who need to understand, implement or audit an ISO management system.</p>
        <p>Our courses are delivered through our e-learning platform and are open to anyone interested in ISO. Our consultants help organisations prepare for certification, and we publish and distribute books and magazines.</p>
        {{-- Add the institute's history, leadership, facilitators and recognition status here once supplied. --}}
        <p><a class="text-link" href="{{ route('contact') }}">Contact our office</a></p>
    </div>
</section>
@endsection
