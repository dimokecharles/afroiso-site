@extends('layouts.admin')
@section('title', $person->exists ? 'Edit staff login' : 'Add a staff login')

@section('content')
<div class="head">
    <h1>{{ $person->exists ? 'Edit staff login' : 'Add a staff login' }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.staff.index') }}">Back to staff logins</a>
</div>
<div class="panel">
    <form method="post" action="{{ $person->exists ? route('admin.staff.update', $person) : route('admin.staff.store') }}" class="form" autocomplete="off">
        @csrf
        @if ($person->exists) @method('put') @endif
        <div class="form__row">
            <div class="field">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $person->name) }}" required>
            </div>
            <div class="field">
                <label for="email">Email (used to log in)</label>
                <input id="email" name="email" type="email" value="{{ old('email', $person->email) }}" required>
            </div>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role">
                    @foreach (\App\Models\User::ROLES as $key => $label)
                        <option value="{{ $key }}" @selected(old('role', $person->role) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="password">{{ $person->exists ? 'New password (leave empty to keep the current one)' : 'Password' }}</label>
                <input id="password" name="password" type="password" autocomplete="new-password" @required(! $person->exists)>
                <small>At least 10 characters. The person can change it themselves under "My password".</small>
            </div>
        </div>
        <label class="check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $person->is_active))>
            This person can log in
        </label>
        <div class="form__actions"><button class="btn" type="submit">{{ $person->exists ? 'Save' : 'Create login' }}</button></div>
    </form>
</div>
@endsection
