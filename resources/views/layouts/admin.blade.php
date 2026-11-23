<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | AfroISO admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $menu = [
        'Overview' => [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard', 'fa-gauge-high', 'dashboard'],
        ],
        'Customers' => [
            ['Contacts', 'admin.contacts.index', 'admin.contacts.*', 'fa-address-book', 'contacts'],
            ['Message box', 'admin.messages.index', 'admin.messages.*', 'fa-envelope-open-text', 'enquiries'],
            ['Enquiries and leads', 'admin.enquiries.index', 'admin.enquiries.*', 'fa-inbox', 'enquiries'],
            ['Enrolments', 'admin.enrolments.index', 'admin.enrolments.*', 'fa-user-graduate', 'enrolments'],
            ['Payments', 'admin.orders.index', 'admin.orders.*', 'fa-credit-card', 'payments'],
            ['Consulting projects', 'admin.projects.index', 'admin.projects.*', 'fa-diagram-project', 'consulting'],
        ],
        'Content' => [
            ['Courses', 'admin.courses.index', 'admin.courses.*', 'fa-graduation-cap', 'content'],
            ['Publications', 'admin.publications.index', 'admin.publications.*', 'fa-book-open', 'content'],
            ['Certificates', 'admin.certificates.index', 'admin.certificates.*', 'fa-award', 'content'],
        ],
        'Settings' => [
            ['Staff logins', 'admin.staff.index', 'admin.staff.*', 'fa-users-gear', 'staff'],
        ],
    ];
    // Show only the parts of the panel this person's role includes.
    $menu = array_filter(array_map(fn ($links) => array_filter($links, fn ($link) => auth()->user()?->canAccess($link[4])), $menu));
@endphp
<a class="skip" href="#main">Skip to content</a>
<div class="shell">
    <aside class="side" id="side">
        <a class="side__brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/icon-192.png') }}" alt="" width="40" height="40">
            <span>AfroISO<small>Admin</small></span>
        </a>
        <nav aria-label="Admin">
            @foreach ($menu as $group => $links)
                <div class="side__group">{{ $group }}</div>
                @foreach ($links as [$label, $route, $pattern, $icon, $area])
                    <a href="{{ route($route) }}" @class(['is-active' => request()->routeIs($pattern)]) @if (request()->routeIs($pattern)) aria-current="page" @endif><i class="fa-solid {{ $icon }}" aria-hidden="true"></i>{{ $label }}</a>
                @endforeach
            @endforeach
        </nav>
        <div class="side__foot">
            <a href="{{ route('admin.account') }}" @class(['is-active' => request()->routeIs('admin.account')])><i class="fa-solid fa-key" aria-hidden="true"></i>My password</a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>View website</a>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>Log out</button></form>
        </div>
    </aside>
    <div class="shell__main">
        <header class="top">
            <button class="top__menu" type="button" aria-controls="side" aria-expanded="false" onclick="var s=document.getElementById('side');var o=s.classList.toggle('is-open');this.setAttribute('aria-expanded',o)"><i class="fa-solid fa-bars" aria-hidden="true"></i><span>Menu</span></button>
            <div class="top__title">@yield('title', 'Admin')</div>
            <a class="top__user" href="{{ route('admin.account') }}"><i class="fa-solid fa-circle-user" aria-hidden="true"></i>{{ auth()->user()?->name }} <span class="tag">{{ auth()->user()?->roleLabel() }}</span></a>
        </header>
        @if (config('afroiso.uat'))
            <div class="uat-note">UAT test site. Data here is separate from the live website.</div>
        @endif
        <main class="wrap" id="main">
            @if (session('status'))
                <div class="flash" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
