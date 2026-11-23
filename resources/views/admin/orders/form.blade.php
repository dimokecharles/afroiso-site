@extends('layouts.admin')
@section('title', 'New consulting invoice')

@section('content')
<div class="head">
    <h1>New consulting invoice</h1>
    <a class="btn btn--plain" href="{{ route('admin.orders.index') }}">Back to payments</a>
</div>
<div class="panel">
    <p>Saving creates a private pay link for this invoice. You can copy the link or email it to the client from the next screen.</p>
    <form method="post" action="{{ route('admin.orders.store') }}" class="form">
        @csrf
        @if ($project)
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <p>This invoice will be linked to the project <strong>{{ $project->title }}</strong>.</p>
        @endif
        <div class="form__row">
            <div class="field">
                <label for="customer_name">Client contact name</label>
                <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name', $project?->contact->name) }}" required>
            </div>
            <div class="field">
                <label for="organisation">Organisation</label>
                <input id="organisation" name="organisation" type="text" value="{{ old('organisation', $project?->contact->organisation) }}">
            </div>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="customer_email">Email</label>
                <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', $project?->contact->email) }}" required>
            </div>
            <div class="field">
                <label for="customer_phone">Phone</label>
                <input id="customer_phone" name="customer_phone" type="text" value="{{ old('customer_phone', $project?->contact->phone) }}">
            </div>
        </div>
        <div class="field">
            <label for="description">What the invoice is for</label>
            <input id="description" name="description" type="text" value="{{ old('description', $project?->title) }}" required>
            <small>Shown to the client on the pay page, for example "ISO 9001 gap analysis, invoice 2026-014".</small>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="amount">Amount (US$)</label>
                <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required>
            </div>
        </div>
        <div class="field">
            <label for="notes">Internal notes (not shown to the client)</label>
            <textarea id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
        </div>
        <div class="form__actions"><button class="btn" type="submit">Create invoice and pay link</button></div>
    </form>
</div>
@endsection
