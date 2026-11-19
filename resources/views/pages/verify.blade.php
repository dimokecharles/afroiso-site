@extends('layouts.app')
@section('title', 'Verify a Certificate')
@section('description', 'Check that a training certificate was issued by AfroISO Institute & Consultants by entering its certificate number or scanning its QR code.')

@section('content')
{{-- Heading with the verification form beside it --}}
<section class="vhero">
    <div class="container vhero__inner">
        <div class="vhero__text">
            <div class="eyebrow eyebrow--light">Certificates</div>
            <h1>Verify a certificate</h1>
            <p>Check that a certificate was issued by AfroISO. Enter the certificate number exactly as it is printed, or scan the QR code on the certificate with your phone.</p>
            <ul class="vhero__ways">
                <li><i class="fa-solid fa-hashtag" aria-hidden="true"></i>By certificate number</li>
                <li><i class="fa-solid fa-qrcode" aria-hidden="true"></i>By QR code</li>
            </ul>
        </div>
        <form method="get" action="{{ route('verify') }}#result" class="verify-form verify-form--page verify-panel vhero__form">
            <div class="vhero__badge" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
            <label for="number">Certificate number</label>
            <div class="verify-form__row">
                <input id="number" name="number" type="text" value="{{ $number }}" autocomplete="off" required>
                <button class="btn btn--accent btn--cut" type="submit">Verify<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </div>
            <p class="vhero__hint">Type it exactly as it is printed on the certificate.</p>
        </form>
    </div>
</section>

@if ($searched)
<section class="section vresult" id="result">
    <div class="container narrow">
        @if ($certificate && $certificate->status === 'valid')
            <div class="cert" role="status">
                <div class="cert__seal">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <h2>This certificate is valid</h2>
                    <p>Issued by AfroISO Institute &amp; Consultants.</p>
                </div>
                <dl class="cert__facts">
                    <div><dt>Certificate number</dt><dd>{{ $certificate->number }}</dd></div>
                    <div><dt>Issued to</dt><dd>{{ $certificate->holder_name }}</dd></div>
                    <div><dt>Course</dt><dd>{{ $certificate->course_title }}</dd></div>
                    <div><dt>Date issued</dt><dd>{{ $certificate->issued_on->format('j F Y') }}</dd></div>
                </dl>
            </div>
        @elseif ($certificate)
            <div class="vnotice" role="status">
                <i class="fa-solid fa-ban" aria-hidden="true"></i>
                <div>
                    <h2>This certificate has been withdrawn</h2>
                    <p>Certificate {{ $certificate->number }} is no longer valid. Contact our office if you need more information.</p>
                    <a class="btn btn--primary btn--cut" href="{{ route('contact', ['subject' => 'Certificate ' . $certificate->number]) }}">Contact our office</a>
                </div>
            </div>
        @else
            <div class="vnotice" role="status">
                <i class="fa-solid fa-circle-xmark" aria-hidden="true"></i>
                <div>
                    <h2>No certificate found</h2>
                    <p>We have no record of certificate number &ldquo;{{ $number }}&rdquo;. Check the number and try again, or contact our office.</p>
                    <a class="btn btn--primary btn--cut" href="{{ route('contact', ['subject' => 'Certificate ' . $number]) }}">Contact our office</a>
                </div>
            </div>
        @endif
    </div>
</section>
<script>
    if (!location.hash) { document.getElementById('result').scrollIntoView(); }
</script>
@endif

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
                <li><span>2</span><div><strong>Enter the number, or scan the code</strong><p>Type the number in the box at the top of this page and press Verify. Or point your phone camera at the QR code and open the link it shows.</p></div></li>
                <li><span>3</span><div><strong>See the result straight away</strong><p>The page shows who the certificate was issued to, the course and the date. If we have no record of the number, the page says so.</p></div></li>
            </ol>
        </div>
    </div>
</section>

{{-- What the result shows, beside common questions --}}
<section class="section">
    <div class="container vinfo">
        <div class="vinfo__shows">
            <div class="eyebrow">The result</div>
            <h2>What a valid result shows</h2>
            <ul class="vfacts">
                <li><i class="fa-solid fa-hashtag" aria-hidden="true"></i><div><strong>Certificate number</strong><span>The number you searched for.</span></div></li>
                <li><i class="fa-solid fa-user-check" aria-hidden="true"></i><div><strong>Issued to</strong><span>The name of the person who holds the certificate.</span></div></li>
                <li><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i><div><strong>Course</strong><span>The course the certificate was awarded for.</span></div></li>
                <li><i class="fa-solid fa-calendar-check" aria-hidden="true"></i><div><strong>Date issued</strong><span>The day AfroISO issued the certificate.</span></div></li>
            </ul>
        </div>
        <div class="vinfo__faq">
            <div class="eyebrow">Common questions</div>
            <h2>If something does not match</h2>
            <div class="faq">
                <details open>
                    <summary>The page says no certificate was found</summary>
                    <p>Check that you typed the number exactly as it is printed on the certificate, then try again. If it is still not found, contact our office with the number and the name on the certificate.</p>
                </details>
                <details>
                    <summary>The name or course is different from the certificate I was shown</summary>
                    <p>The details on this page are AfroISO's record. If the certificate you were shown says something different, contact our office.</p>
                </details>
                <details>
                    <summary>The page says the certificate has been withdrawn</summary>
                    <p>A withdrawn certificate is no longer valid. Contact our office if you need more information.</p>
                </details>
            </div>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="cta">
            <div class="cta__text">
                <h2>Need help with a certificate?</h2>
                <p>Send us the certificate number and our office will check it for you.</p>
            </div>
            <div class="cta__actions">
                <a class="btn btn--accent btn--cut" href="{{ route('contact', ['subject' => 'Certificate verification']) }}">Contact our office<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="btn btn--light btn--cut" href="{{ route('courses.index') }}">View courses</a>
            </div>
        </div>
    </div>
</section>
@endsection
