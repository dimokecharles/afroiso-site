@extends('layouts.app')
@section('title', 'Books and Magazines')
@section('description', 'Books and magazines on ISO standards published and distributed by AfroISO Institute & Consultants, Harare.')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">Publications</div>
        <h1>Books and magazines</h1>
        <p>AfroISO publishes and distributes books and magazines.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        @if ($publications->isEmpty())
            <p>Our publications will be listed here soon.</p>
        @else
            @include('pages._publications', ['publications' => $publications])
        @endif
    </div>
</section>
@endsection
