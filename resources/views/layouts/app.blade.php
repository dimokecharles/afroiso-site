<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0A1530">
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
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
        <nav class="nav" id="site-nav" aria-label="Main">
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
            <div class="footer__col footer__col--brand">
                <a class="footer__logo" href="{{ route('home') }}"><img src="{{ asset('images/logo.jpg') }}" alt="{{ config('afroiso.name') }}"></a>
                <p>AfroISO Institute &amp; Consultants is a Harare-based training institute, consultancy and publisher for ISO management system standards.</p>
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
                <div class="footer__heading">Training</div>
                @foreach (\App\Models\Course::published()->whereNotNull('code')->get() as $footerCourse)
                    <a href="{{ route('courses.show', $footerCourse) }}">{{ $footerCourse->code }}</a>
                @endforeach
            </div>
            <div class="footer__col">
                <div class="footer__heading">Policies</div>
                <a href="{{ route('policy', 'privacy-policy') }}">Privacy policy</a>
                <a href="{{ route('policy', 'refund-policy') }}">Refund policy</a>
                <a href="{{ route('policy', 'terms') }}">Terms and conditions</a>
            </div>
            <div class="footer__col footer__col--contact">
                <div class="footer__heading">Contact us</div>
                <a class="footer__contact" href="tel:{{ config('afroiso.phone_link') }}"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>{{ config('afroiso.phone') }}</span></a>
                <a class="footer__contact" href="mailto:{{ config('afroiso.email') }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>{{ config('afroiso.email') }}</span></a>
                <div class="footer__contact"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ config('afroiso.address') }}</span></div>
            </div>
        </div>

        @php
            // Facebook, LinkedIn, Instagram and WhatsApp always show; the others show once they have a link.
            // Add the links in .env (SOCIAL_FACEBOOK and so on). An icon without a link stays on the page.
            $always = ['facebook', 'linkedin', 'instagram', 'whatsapp'];
            $social = [];
            foreach (config('afroiso.social', []) as $network => $url) {
                if ($url || in_array($network, $always, true)) {
                    $social[$network] = $url;
                }
            }
        @endphp
        @if ($social)
            <div class="footer__social">
                <div class="footer__heading">Follow us</div>
                <div class="footer__icons">
                    @foreach ($social as $network => $url)
                        <a href="{{ $url ?: '#' }}" @if ($url) target="_blank" rel="noopener" @endif aria-label="{{ ucfirst(str_replace('-twitter', '', $network)) }}"><i class="fa-brands fa-{{ $network }}" aria-hidden="true"></i></a>
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

{{-- App-style bar at the bottom of the screen on phones --}}
<nav class="tabbar" aria-label="Quick navigation">
    <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])><i class="fa-solid fa-house" aria-hidden="true"></i><span>Home</span></a>
    <a href="{{ route('courses.index') }}" @class(['is-active' => request()->routeIs('courses.*')])><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i><span>Courses</span></a>
    <a href="{{ route('publications') }}" @class(['is-active' => request()->routeIs('publications')])><i class="fa-solid fa-book-open" aria-hidden="true"></i><span>Books</span></a>
    <a href="{{ route('verify') }}" @class(['is-active' => request()->routeIs('verify')])><i class="fa-solid fa-certificate" aria-hidden="true"></i><span>Verify</span></a>
    <a href="{{ route('contact') }}" @class(['is-active' => request()->routeIs('contact')])><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>Contact</span></a>
</nav>

{{-- WhatsApp chat box, shown on every page --}}
@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('afroiso.whatsapp_number'));
    $chatReplies = [
        'I want to enrol on a course' => 'Hello AfroISO, I would like to enrol on a course.',
        'I need consulting for my organisation' => 'Hello AfroISO, I would like to ask about consulting for my organisation.',
        'I want to order a book or magazine' => 'Hello AfroISO, I would like to order a book or magazine.',
        'I have another question' => 'Hello AfroISO, I have a question.',
    ];
@endphp
@if ($whatsapp)
<div class="chat" data-number="{{ $whatsapp }}">
    <div class="chat__panel" id="chat-panel" role="dialog" aria-label="Chat with AfroISO on WhatsApp" hidden>
        <div class="chat__head">
            <div class="chat__avatar"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></div>
            <div class="chat__who">
                <strong>AfroISO</strong>
                <span>Chat with our office on WhatsApp</span>
            </div>
            <button class="chat__close" type="button" aria-label="Close chat"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="chat__body">
            <div class="chat__bubble">Hello! How can we help you today? Choose a topic or type your message.</div>
            <div class="chat__replies">
                @foreach ($chatReplies as $label => $message)
                    <a href="https://wa.me/{{ $whatsapp }}?text={{ rawurlencode($message) }}" target="_blank" rel="noopener">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <form class="chat__form">
            <label class="sr-only" for="chat-input">Your message</label>
            <input class="chat__input" id="chat-input" type="text" placeholder="Type a message" autocomplete="off">
            <button type="submit" aria-label="Send on WhatsApp"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
        </form>
        <div class="chat__note">Your message opens in WhatsApp.</div>
    </div>
    <button class="chat__launcher" type="button" aria-expanded="false" aria-controls="chat-panel" aria-label="Chat with us on WhatsApp">
        <i class="fa-brands fa-whatsapp chat__icon-open" aria-hidden="true"></i>
        <i class="fa-solid fa-xmark chat__icon-close" aria-hidden="true"></i>
    </button>
</div>
@endif

<script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
