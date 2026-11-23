@extends('layouts.admin')
@section('title', $contact->exists ? 'Edit contact' : 'Add a contact')

@section('content')
<div class="head">
    <h1>{{ $contact->exists ? 'Edit contact' : 'Add a contact' }}</h1>
    <a class="btn btn--plain" href="{{ $contact->exists ? route('admin.contacts.show', $contact) : route('admin.contacts.index') }}">Back</a>
</div>
<div class="panel">
    <form method="post" action="{{ $contact->exists ? route('admin.contacts.update', $contact) : route('admin.contacts.store') }}" class="form">
        @csrf
        @if ($contact->exists) @method('put') @endif
        <div class="form__row">
            <div class="field">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $contact->name) }}" required>
            </div>
            <div class="field">
                <label for="organisation">Organisation</label>
                <input id="organisation" name="organisation" type="text" value="{{ old('organisation', $contact->organisation) }}">
            </div>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $contact->email) }}">
                <small>Enquiries and orders from this address are added to this contact automatically.</small>
            </div>
            <div class="field">
                <label for="phone">Phone or WhatsApp</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $contact->phone) }}">
            </div>
            <div class="field">
                <label for="country">Country</label>
                <input id="country" name="country" type="text" value="{{ old('country', $contact->country) }}">
            </div>
        </div>
        <div class="form__actions"><button class="btn" type="submit">Save contact</button></div>
    </form>
</div>
@endsection
