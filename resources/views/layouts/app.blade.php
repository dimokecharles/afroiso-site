<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0A1530">
    @php
        // Page title: "<page> | AfroISO Institute & Consultants", or the site name with a tagline on the homepage.
        $pageTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES);
        $fullTitle = $pageTitle !== ''
            ? $pageTitle . ' | ' . config('afroiso.name')
            : config('afroiso.name') . ' | ISO Training, Consulting and Publications in Harare';
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="@yield('description', 'AfroISO Institute & Consultants in Harare, Zimbabwe: e-learning courses in ISO 9001, ISO 14001, ISO 45001, ISO/IEC 27001 and food safety, internal auditor training, ISO consulting, books and magazines.')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="{{ url()->current() }}">
    @if (config('afroiso.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('afroiso.google_site_verification') }}">
    @endif

    {{-- How the page looks when shared on WhatsApp, Facebook, LinkedIn and X --}}
    <meta property="og:site_name" content="{{ config('afroiso.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_GB">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="@yield('description', 'E-learning courses in ISO 9001, ISO 14001, ISO 45001, ISO/IEC 27001 and food safety, internal auditor training, ISO consulting, books and magazines.')">
    <meta property="og:image" content="@yield('image', asset('images/share.jpg'))">
    <meta property="og:image:alt" content="{{ config('afroiso.name') }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Site icons: browser tab, Google results, phone home screen --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Facts about the organisation for Google, plus anything a page adds --}}
    @php
        $organisation = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            '@id' => url('/') . '#organisation',
            'name' => config('afroiso.name'),
            'alternateName' => 'AfroISO',
            'url' => url('/'),
            'logo' => asset('images/icon-512.png'),
            'image' => asset('images/share.jpg'),
            'description' => 'Training institute, consultancy and publisher for ISO management system standards, based in Harare, Zimbabwe.',
            'email' => config('afroiso.email'),
            'telephone' => config('afroiso.phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '2nd Floor, East Wing, Celestial Park, Liberation Legacy Way',
                'addressLocality' => 'Harare',
                'addressCountry' => 'ZW',
            ],
        ];
        $profiles = array_values(array_filter(config('afroiso.social', [])));
        if ($profiles) {
            $organisation['sameAs'] = $profiles;
        }
        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('afroiso.name'),
            'alternateName' => 'AfroISO',
            'url' => url('/'),
        ];
        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG;
    @endphp
    <script type="application/ld+json">{!! json_encode($organisation, $jsonFlags) !!}</script>
    <script type="application/ld+json">{!! json_encode($website, $jsonFlags) !!}</script>
    @stack('structured-data')

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
            <a class="topbar__item notranslate" translate="no" href="mailto:{{ config('afroiso.email') }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i>{{ config('afroiso.email') }}</a>
            {{-- Language switcher: the page is translated in the browser by Google Translate (see js/site.js) --}}
            <div class="lang notranslate" translate="no">
                <button class="lang__toggle" type="button" aria-expanded="false" aria-controls="lang-menu">
                    <i class="fa-solid fa-globe" aria-hidden="true"></i><span data-lang-current>Language</span><i class="fa-solid fa-chevron-down lang__arrow" aria-hidden="true"></i>
                </button>
                <ul class="lang__menu" id="lang-menu" hidden>
                    <li><button type="button" data-lang="en" lang="en">English</button></li>
                    <li><button type="button" data-lang="zh-CN" lang="zh">中文 (Chinese)</button></li>
                    <li><button type="button" data-lang="fr" lang="fr">Français (French)</button></li>
                    <li><button type="button" data-lang="pt" lang="pt">Português (Portuguese)</button></li>
                    <li><button type="button" data-lang="sn" lang="sn">ChiShona (Shona)</button></li>
                </ul>
            </div>
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

<div id="google_translate_element" aria-hidden="true"></div>
<script src="{{ asset('js/site.js') }}" defer></script>
</body>
</html>
