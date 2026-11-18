<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Log in | AfroISO admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<main class="login">
    <img src="{{ asset('images/logo.jpg') }}" alt="AfroISO Institute &amp; Consultants">
    <div class="panel">
        <h1>Admin log in</h1>
        @if ($errors->any())
            <div class="errors" role="alert" style="margin-top: 16px">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('login.attempt') }}" class="form" style="margin-top: 20px">
            @csrf
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            <label class="check"><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
            <button class="btn" type="submit">Log in</button>
        </form>
    </div>
</main>
</body>
</html>
