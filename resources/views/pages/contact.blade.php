@extends('layouts.app')
@section('title', 'Contact AfroISO in Harare')
@section('description', 'Contact AfroISO Institute & Consultants at Celestial Park, Liberation Legacy Way, Harare. Call +263 718 781 215 or send an enquiry about courses, consulting or publications.')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">Contact</div>
        <h1>Talk to our office</h1>
        <p>Tell us which course or service you are interested in and we will reply with dates, fees and next steps.</p>
    </div>
</section>
<section class="section">
    <div class="container contact">
        <dl class="contact__details">
            <div><dt>Office</dt><dd>{{ config('afroiso.address') }}</dd></div>
            <div><dt>Telephone</dt><dd><a href="tel:{{ config('afroiso.phone_link') }}">{{ config('afroiso.phone') }}</a></dd></div>
            <div><dt>Email</dt><dd><a href="mailto:{{ config('afroiso.email') }}">{{ config('afroiso.email') }}</a></dd></div>
        </dl>

        <div class="contact__form">
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
                <button class="btn btn--primary" type="submit">Send enquiry</button>
            </form>
        </div>
    </div>
</section>
@endsection
