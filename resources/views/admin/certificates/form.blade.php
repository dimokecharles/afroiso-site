@extends('layouts.admin')
@section('title', $certificate->exists ? 'Edit certificate' : 'Add a certificate')

@section('content')
<div class="head">
    <h1>{{ $certificate->exists ? 'Edit certificate' : 'Add a certificate' }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.certificates.index') }}">Back to certificates</a>
</div>
<div class="panel">
    <form method="post" action="{{ $certificate->exists ? route('admin.certificates.update', $certificate) : route('admin.certificates.store') }}" class="form">
        @csrf
        @if ($certificate->exists) @method('put') @endif
        <div class="form__row">
            <div class="field">
                <label for="number">Certificate number</label>
                <input id="number" name="number" type="text" value="{{ old('number', $certificate->number) }}" required>
            </div>
            <div class="field">
                <label for="issued_on">Date issued</label>
                <input id="issued_on" name="issued_on" type="date" value="{{ old('issued_on', $certificate->issued_on?->format('Y-m-d')) }}" required>
            </div>
            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="valid" @selected(old('status', $certificate->status) === 'valid')>Valid</option>
                    <option value="revoked" @selected(old('status', $certificate->status) === 'revoked')>Withdrawn</option>
                </select>
            </div>
        </div>
        <div class="field">
            <label for="holder_name">Issued to</label>
            <input id="holder_name" name="holder_name" type="text" value="{{ old('holder_name', $certificate->holder_name) }}" required>
        </div>
        <div class="field">
            <label for="course_title">Course</label>
            <input id="course_title" name="course_title" type="text" value="{{ old('course_title', $certificate->course_title) }}" required>
        </div>
        <div class="form__actions"><button class="btn" type="submit">Save certificate</button></div>
    </form>
</div>
@endsection
