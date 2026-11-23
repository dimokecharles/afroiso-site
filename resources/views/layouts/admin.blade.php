<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') | AfroISO admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<header class="bar">
    <div class="bar__inner">
        <a class="bar__brand" href="{{ route('admin.dashboard') }}">AfroISO admin</a>
        <nav aria-label="Admin">
            <a href="{{ route('admin.enquiries.index') }}" @class(['is-active' => request()->routeIs('admin.enquiries.*')])>Enquiries</a>
            <a href="{{ route('admin.orders.index') }}" @class(['is-active' => request()->routeIs('admin.orders.*')])>Payments</a>
            <a href="{{ route('admin.courses.index') }}" @class(['is-active' => request()->routeIs('admin.courses.*')])>Courses</a>
            <a href="{{ route('admin.publications.index') }}" @class(['is-active' => request()->routeIs('admin.publications.*')])>Publications</a>
            <a href="{{ route('admin.certificates.index') }}" @class(['is-active' => request()->routeIs('admin.certificates.*')])>Certificates</a>
            <a href="{{ route('home') }}" target="_blank" rel="noopener">View site</a>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">Log out</button></form>
        </nav>
    </div>
</header>
<main class="wrap">
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
</body>
</html>
