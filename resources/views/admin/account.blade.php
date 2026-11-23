@extends('layouts.admin')
@section('title', 'My password')

@section('content')
<div class="head"><h1>My password</h1></div>
<div class="panel">
    <dl class="detail">
        <div><dt>Name</dt><dd>{{ $person->name }}</dd></div>
        <div><dt>Email</dt><dd>{{ $person->email }}</dd></div>
        <div><dt>Role</dt><dd>{{ $person->roleLabel() }}</dd></div>
    </dl>
</div>
<div class="panel">
    <h2>Change my password</h2>
    <form method="post" action="{{ route('admin.account.update') }}" class="form">
        @csrf @method('put')
        <div class="field">
            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
                <small>At least 10 characters.</small>
            </div>
            <div class="field">
                <label for="password_confirmation">New password again</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
        </div>
        <div class="form__actions"><button class="btn" type="submit">Change password</button></div>
    </form>
</div>
@endsection
