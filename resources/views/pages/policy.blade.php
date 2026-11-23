@extends('layouts.app')
@section('title', $title)

@php
    // The policy text is shown on the UAT site for review, and on the live site once POLICIES_PUBLISHED=true is set in .env.
    // It can also be published from the admin panel, under Website text. Wording edited there replaces the built-in draft.
    $published = config('afroiso.policies_published') || \App\Support\SiteText::custom('policies.published') === '1';
    $showPolicy = config('afroiso.uat') || $published;
    $custom = \App\Support\SiteText::custom('policy.' . $policy);
@endphp

@unless ($published)
    @section('robots', 'noindex, follow')
@endunless

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">Policies</div>
        <h1>{{ $title }}</h1>
        @if ($showPolicy)
            <p>Last updated: {{ $custom !== null ? \App\Support\SiteText::updatedAt('policy.' . $policy)?->format('j F Y') : '20 November 2026' }}</p>
        @endif
    </div>
</section>
<section class="section">
    <div class="container narrow prose policy">
        @if ($showPolicy)
            @unless ($published)
                <p class="policy__draft"><strong>Draft for review.</strong> This text has not been approved yet. Points marked <span class="confirm">like this</span> need AfroISO's decision, and the whole policy should be checked by a legal adviser before it is published.</p>
            @endunless
            @if ($custom !== null)
                {!! \App\Support\SimpleText::toHtml($custom) !!}
            @else
                @include('pages.policies.' . $policy)
            @endif
            <nav class="policy__links" aria-label="Other policies">
                <a href="{{ route('policy', 'privacy-policy') }}">Privacy policy</a>
                <a href="{{ route('policy', 'refund-policy') }}">Refund policy</a>
                <a href="{{ route('policy', 'terms') }}">Terms and conditions</a>
            </nav>
        @else
            <p>This policy is being finalised and will be published here. For questions in the meantime, contact <a href="mailto:{{ config('afroiso.email') }}">{{ config('afroiso.email') }}</a>.</p>
        @endif
    </div>
</section>
@endsection
