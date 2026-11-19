@extends('layouts.app')
@section('title', 'Contact AfroISO in Harare')
@section('description', 'Contact AfroISO Institute & Consultants at Celestial Park, Liberation Legacy Way, Harare. Call +263 718 781 215 or send an enquiry about courses, consulting or publications.')

@php
    $whatsapp = config('afroiso.whatsapp_number');
    $mapQuery = 'Celestial Park, Liberation Legacy Way, Harare, Zimbabwe';
@endphp

@section('content')
<section class="page-head page-head--about page-head--contact">
    <div class="container">
        <div class="eyebrow eyebrow--light">Contact us</div>
        <h1>Talk to our office</h1>
        <p>Tell us which course or service you are interested in and we will reply with dates, fees and next steps.</p>
    </div>
</section>

{{-- Three direct ways to reach the office --}}
<section class="reach">
    <div class="container">
        <div class="reach__bar">
            <a class="reach__item" href="tel:{{ config('afroiso.phone_link') }}">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                <span><small>Call us</small><strong>{{ config('afroiso.phone') }}</strong></span>
            </a>
            @if ($whatsapp)
                <a class="reach__item" href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                    <span><small>WhatsApp</small><strong>Chat with us</strong></span>
                </a>
            @endif
            <a class="reach__item" href="mailto:{{ config('afroiso.email') }}">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <span><small>Email us</small><strong>{{ config('afroiso.email') }}</strong></span>
            </a>
        </div>
    </div>
</section>

<section class="section section--contact">
    <div class="container contact">
        <div class="contact__form" id="enquiry">
            <div class="contact__intro">
                <div class="eyebrow">Send an enquiry</div>
                <h2>Write to us</h2>
            </div>
            @if (session('sent'))
                <div class="result result--ok" role="status">
                    <h2>Thank you. Your enquiry has been sent.</h2>
                    <p>Our office will reply by email or phone.</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="result result--bad" role="alert">
                    <h2>Please check the form</h2>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('contact.send') }}" class="form">
                @csrf
                <input type="hidden" name="subject" value="{{ old('subject', $subject) }}">
                <div class="hp" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>

                @if (old('subject', $subject))
                    <p class="form__subject">Regarding: <strong>{{ old('subject', $subject) }}</strong></p>
                @endif

                <div class="form__grid">
                    <div class="field">
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
                    </div>
                    <div class="field">
                        <label for="organisation">Organisation (optional)</label>
                        <input id="organisation" name="organisation" type="text" value="{{ old('organisation') }}" autocomplete="organization">
                    </div>
                    <div class="field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    <div class="field">
                        <label for="phone">Phone number (optional)</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel">
                    </div>
                </div>
                <div class="field">
                    <label for="interest">I am interested in</label>
                    <select id="interest" name="interest">
                        @foreach ($interests as $option)
                            <option value="{{ $option }}" @selected(old('interest', $interest) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                </div>
                <button class="btn btn--accent btn--cut" type="submit">Send enquiry<i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
            </form>
        </div>

        <aside class="office">
            <h2>What happens next</h2>
            <ol class="office__next">
                <li><span>1</span><p>Your enquiry goes to our office in Harare.</p></li>
                <li><span>2</span><p>We reply by email or phone with dates, fees and next steps.</p></li>
                <li><span>3</span><p>When you are ready, we send you an invoice.</p></li>
            </ol>
            <h3>Our Office</h3>
            <ul class="office__list">
                <li><i class="fa-solid fa-phone" aria-hidden="true"></i><a href="tel:{{ config('afroiso.phone_link') }}">{{ config('afroiso.phone') }}</a></li>
                <li><i class="fa-solid fa-envelope" aria-hidden="true"></i><a href="mailto:{{ config('afroiso.email') }}">{{ config('afroiso.email') }}</a></li>
                <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ config('afroiso.address') }}</span></li>
            </ul>
        </aside>
    </div>
</section>

{{-- Map of the office --}}
<section class="map">
    <iframe title="Map showing the AfroISO office at Celestial Park, Harare" src="https://www.google.com/maps?q={{ urlencode($mapQuery) }}&z=15&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    <div class="container map__holder">
        <div class="map__panel">
            <div class="eyebrow eyebrow--light">Find us</div>
            <h2>Celestial Park, Harare</h2>
            <p>{{ config('afroiso.address') }}</p>
            <a class="btn btn--accent btn--cut" href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($mapQuery) }}" target="_blank" rel="noopener">Get directions<i class="fa-solid fa-diamond-turn-right" aria-hidden="true"></i></a>
        </div>
    </div>
</section>
@endsection
