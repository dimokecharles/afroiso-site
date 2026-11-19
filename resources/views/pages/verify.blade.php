@extends('layouts.app')
@section('title', 'Verify a Certificate')
@section('description', 'Check that a training certificate was issued by AfroISO Institute & Consultants by entering its certificate number or scanning its QR code.')

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">Certificates</div>
        <h1>Verify a certificate</h1>
        <p>Enter the certificate number exactly as it is printed on the certificate, or scan the QR code on the certificate with your phone.</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        <form method="get" action="{{ route('verify') }}" class="verify-form verify-form--page verify-panel">
            <label for="number">Certificate number</label>
            <div class="verify-form__row">
                <input id="number" name="number" type="text" value="{{ $number }}" required>
                <button class="btn btn--accent btn--cut" type="submit">Verify<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
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

{{-- How verification works: explainer video beside the three steps --}}
<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center">
            <div class="eyebrow">How it works</div>
            <h2>Verify a certificate in three steps</h2>
        </div>
        <div class="howto">
            <div class="howto__video">
                <video controls playsinline preload="metadata" poster="{{ asset('images/how-verification-works.jpg') }}">
                    <source src="{{ asset('videos/how-verification-works.mp4') }}" type="video/mp4">
                    Your browser cannot play this video. The three steps are described beside it.
                </video>
            </div>
            <ol class="howto__steps">
                <li><span>1</span><div><strong>Find the number or the QR code</strong><p>Both are printed on the AfroISO certificate.</p></div></li>
                <li><span>2</span><div><strong>Enter the number, or scan the code</strong><p>Type the number in the box above and press Verify. Or point your phone camera at the QR code and open the link it shows.</p></div></li>
                <li><span>3</span><div><strong>See the result straight away</strong><p>The page shows who the certificate was issued to, the course and the date. If we have no record of the number, the page says so.</p></div></li>
            </ol>
        </div>
    </div>
</section>
@endsection
