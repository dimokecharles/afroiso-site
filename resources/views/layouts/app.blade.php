<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@hasSection('title')@yield('title') | @endif{{ config('afroiso.name') }}</title>
    <meta name="description" content="@yield('description', 'AfroISO Institute & Consultants, Harare: e-learning courses in ISO 9001, ISO 14001, ISO 45001, ISO/IEC 27001 and food safety, plus consulting and publications.')">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container topbar__inner">
        <span class="topbar__item"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ config('afroiso.address') }}</span>
        <div class="topbar__links">
            <a class="topbar__item" href="tel:{{ config('afroiso.phone_link') }}"><i class="fa-solid fa-phone" aria-hidden="true"></i>{{ config('afroiso.phone') }}</a>
            <a class="topbar__item" href="mailto:{{ config('afroiso.email') }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i>{{ config('afroiso.email') }}</a>
        </div>
    </div>
</div>

<header class="header">
    <div class="container header__inner">
        <a href="{{ route('home') }}" class="header__logo">
            <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('afroiso.name') }}">
        </a>
        <nav class="nav" aria-label="Main">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Home</a>
            <a href="{{ route('about') }}" @class(['is-active' => request()->routeIs('about')])>About</a>
            <a href="{{ route('courses.index') }}" @class(['is-active' => request()->routeIs('courses.*')])>Training courses</a>
            <a href="{{ route('consulting') }}" @class(['is-active' => request()->routeIs('consulting')])>Consulting</a>
            <a href="{{ route('publications') }}" @class(['is-active' => request()->routeIs('publications')])>Publications</a>
            <a href="{{ route('verify') }}" @class(['is-active' => request()->routeIs('verify')])>Verify a certificate</a>
            <a href="{{ route('contact') }}" class="nav__cta">Contact us</a>
        </nav>
    </div>
</header>

<main id="main">
    @yield('content')
</main>

<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__col">
                <div class="footer__brand">Afro<span>ISO</span></div>
                <div>Institute &amp; Consultants</div>
                <div>Harare, Zimbabwe</div>
            </div>
            <div class="footer__col">
                <div class="footer__heading">Training</div>
                @foreach (\App\Models\Course::published()->whereNotNull('code')->get() as $footerCourse)
                    <a href="{{ route('courses.show', $footerCourse) }}">{{ $footerCourse->code }}</a>
                @endforeach
            </div>
            <div class="footer__col">
                <div class="footer__heading">Quick links</div>
                <a href="{{ route('about') }}">About us</a>
                <a href="{{ route('consulting') }}">Consulting</a>
                <a href="{{ route('courses.index') }}">Training calendar</a>
                <a href="{{ route('publications') }}">Publications</a>
                <a href="{{ route('verify') }}">Verify a certificate</a>
            </div>
            <div class="footer__col">
                <div class="footer__heading">Policies</div>
                <a href="{{ route('policy', 'privacy-policy') }}">Privacy policy</a>
                <a href="{{ route('policy', 'terms') }}">Terms and conditions</a>
                <a href="{{ route('policy', 'refund-policy') }}">Refund policy</a>
            </div>
            <div class="footer__col">
                <div class="footer__heading">Contact</div>
                <div>{{ config('afroiso.address') }}</div>
                <a href="tel:{{ config('afroiso.phone_link') }}">{{ config('afroiso.phone') }}</a>
                <a href="mailto:{{ config('afroiso.email') }}">{{ config('afroiso.email') }}</a>
            </div>
        </div>

        @php
            $social = array_filter(config('afroiso.social', []));
        @endphp
        @if ($social)
            <div class="footer__social">
                <div class="footer__heading">Follow us</div>
                <div class="footer__icons">
                    @foreach ($social as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst(str_replace('-twitter', '', $network)) }}"><i class="fa-brands fa-{{ $network }}" aria-hidden="true"></i></a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="footer__bottom">
            <span>&copy; {{ date('Y') }} {{ config('afroiso.name') }}. All rights reserved.</span>
            <span>AfroISO is an independent training provider and is not affiliated with the International Organization for Standardization.</span>
        </div>
    </div>
</footer>
</body>
</html>
