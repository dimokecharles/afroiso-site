@extends('layouts.app')
@section('title', 'Books and Magazines')
@section('description', 'Books and magazines on ISO standards published and distributed by AfroISO Institute & Consultants, Harare.')

@php
    $books = $publications->where('type', 'book')->values();
    $magazines = $publications->where('type', 'magazine')->values();
    $featured = $books->first();
    $otherBooks = $books->slice(1);
    $orderLink = fn ($publication) => $publication->price !== null
        ? route('buy.create', $publication)
        : route('contact', ['subject' => 'Order: ' . $publication->title, 'interest' => 'Ordering a book or magazine']);
@endphp

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">Publications</div>
        <h1>Books and magazines on standards.</h1>
        <p>AfroISO publishes and distributes books and magazines for people who work with ISO standards.</p>
    </div>
</section>

@if ($publications->isEmpty())
    <section class="section">
        <div class="container"><p>Our publications will be listed here soon.</p></div>
    </section>
@endif

{{-- Featured book --}}
@if ($featured)
<section class="spotlight">
    <div class="container spotlight__inner">
        <div class="spotlight__cover">
            @if ($featured->cover_path)
                <img src="{{ asset($featured->cover_path) }}" alt="Cover of {{ $featured->title }}">
            @else
                <div class="shelf__empty">Cover to follow</div>
            @endif
        </div>
        <div class="spotlight__body">
            <div class="eyebrow eyebrow--light">Featured book</div>
            <h2>{{ $featured->title }}</h2>
            @if ($featured->description)
                <p>{!! nl2br(e($featured->description)) !!}</p>
            @endif
            <div class="spotlight__buy">
                <div class="spotlight__price">{{ $featured->priceLabel() }}</div>
                <a class="btn btn--accent btn--cut" href="{{ $orderLink($featured) }}">Buy this book<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Magazine issues on a shelf --}}
@if ($magazines->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section__head">
            <div class="eyebrow">Magazine</div>
            <h2>Magazine issues</h2>
            <p>Each issue covers what is new in standards and the people behind them.</p>
        </div>
        <div class="shelf">
            @foreach ($magazines as $publication)
                <article class="shelf__item">
                    <div class="shelf__cover">
                        @if ($publication->cover_path)
                            <img src="{{ asset($publication->cover_path) }}" alt="Cover of {{ $publication->title }}" loading="lazy">
                        @else
                            <div class="shelf__empty">Cover to follow</div>
                        @endif
                    </div>
                    <h3>{{ $publication->title }}</h3>
                    @if ($publication->description)
                        <p>{!! nl2br(e($publication->description)) !!}</p>
                    @endif
                    <div class="shelf__buy">
                        <span class="shelf__price">{{ $publication->priceLabel() }}</span>
                        <a class="btn btn--primary btn--cut" href="{{ $orderLink($publication) }}">Buy<span class="sr-only"> {{ $publication->title }}</span></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Any further books --}}
@if ($otherBooks->isNotEmpty())
<section class="section section--tint">
    <div class="container">
        <div class="section__head">
            <div class="eyebrow">Books</div>
            <h2>More books</h2>
        </div>
        <div class="shelf">
            @foreach ($otherBooks as $publication)
                <article class="shelf__item">
                    <div class="shelf__cover">
                        @if ($publication->cover_path)
                            <img src="{{ asset($publication->cover_path) }}" alt="Cover of {{ $publication->title }}" loading="lazy">
                        @else
                            <div class="shelf__empty">Cover to follow</div>
                        @endif
                    </div>
                    <h3>{{ $publication->title }}</h3>
                    @if ($publication->description)
                        <p>{!! nl2br(e($publication->description)) !!}</p>
                    @endif
                    <div class="shelf__buy">
                        <span class="shelf__price">{{ $publication->priceLabel() }}</span>
                        <a class="btn btn--primary btn--cut" href="{{ $orderLink($publication) }}">Buy<span class="sr-only"> {{ $publication->title }}</span></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- How ordering works --}}
<section class="band">
    <div class="container">
        <div class="section__head section__head--center">
            <div class="eyebrow eyebrow--light">Ordering</div>
            <h2>How to buy</h2>
        </div>
        <ol class="order-steps">
            <li><span>1</span><strong>Choose a publication</strong><p>Press Buy on the book or magazine issue you want.</p></li>
            <li><span>2</span><strong>Enter your details</strong><p>Give us your name and the email address the publication should go to.</p></li>
            <li><span>3</span><strong>Pay and receive it</strong><p>Pay on the secure payment page. Our office then sends your publication.</p></li>
        </ol>
    </div>
</section>
@endsection
