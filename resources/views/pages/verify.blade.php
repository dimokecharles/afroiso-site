@extends('layouts.app')
@section('title', 'Verify a certificate')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">Certificates</div>
        <h1>Verify a certificate</h1>
        <p>Enter the certificate number exactly as it is printed on the certificate.</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <form method="get" action="{{ route('verify') }}" class="verify-form verify-form--page">
            <label for="number">Certificate number</label>
            <div class="verify-form__row">
                <input id="number" name="number" type="text" value="{{ $number }}" required>
                <button class="btn btn--primary" type="submit">Verify</button>
            </div>
        </form>

        @if ($searched && $certificate && $certificate->status === 'valid')
            <div class="result result--ok" role="status">
                <h2>This certificate is valid</h2>
                <dl>
                    <div><dt>Certificate number</dt><dd>{{ $certificate->number }}</dd></div>
                    <div><dt>Issued to</dt><dd>{{ $certificate->holder_name }}</dd></div>
                    <div><dt>Course</dt><dd>{{ $certificate->course_title }}</dd></div>
                    <div><dt>Date issued</dt><dd>{{ $certificate->issued_on->format('j F Y') }}</dd></div>
                </dl>
            </div>
        @elseif ($searched && $certificate)
            <div class="result result--bad" role="status">
                <h2>This certificate has been withdrawn</h2>
                <p>Certificate {{ $certificate->number }} is no longer valid. Contact our office if you need more information.</p>
            </div>
        @elseif ($searched)
            <div class="result result--bad" role="status">
                <h2>No certificate found</h2>
                <p>We have no record of certificate number &ldquo;{{ $number }}&rdquo;. Check the number and try again, or contact our office.</p>
            </div>
        @endif
    </div>
</section>
@endsection
