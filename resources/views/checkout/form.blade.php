@extends('layouts.app')
@section('title', $heading)
@section('robots', 'noindex, follow')

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">{{ $eyebrow }}</div>
        <h1>{{ $itemTitle }}</h1>
        <p>Enter your details, then continue to the payment page.</p>
    </div>
</section>
<section class="section">
    <div class="container contact">
        <div class="contact__form">
            <ol class="checkout-steps" aria-label="Steps">
                <li class="is-current"><span>1</span>Your details</li>
                <li><span>2</span>Payment</li>
                <li><span>3</span>Confirmation</li>
            </ol>
            @if ($errors->any())
                <div class="form-error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ $action }}" class="form">
                @csrf
                <div class="hp" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                </div>
                <div class="form__grid">
                    <div class="field">
                        <label for="customer_name">Full name</label>
                        <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}" autocomplete="name" required>
                    </div>
                    <div class="field">
                        <label for="customer_email">Email address</label>
                        <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}" autocomplete="email" required>
                    </div>
                    <div class="field">
                        <label for="customer_phone">Phone or WhatsApp (optional)</label>
                        <input id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" autocomplete="tel">
                    </div>
                    <div class="field">
                        <label for="organisation">Organisation (optional)</label>
                        <input id="organisation" name="organisation" type="text" value="{{ old('organisation') }}" autocomplete="organization">
                    </div>
                </div>
                <label class="agree">
                    <input type="checkbox" name="accept" value="1" @checked(old('accept')) required>
                    <span>I accept the <a href="{{ route('policy', 'terms') }}" target="_blank" rel="noopener">terms and conditions</a> and the <a href="{{ route('policy', 'refund-policy') }}" target="_blank" rel="noopener">refund policy</a>.</span>
                </label>
                <button class="btn btn--accent btn--cut" type="submit">Continue to payment<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
            </form>
            <p class="checkout-alt">Not ready to pay, or enrolling a group? <a href="{{ $enquiryLink }}">Send us an enquiry instead</a>.</p>
        </div>

        <aside class="facts">
            @if ($cover)
                <img class="facts__cover" src="{{ asset($cover) }}" alt="">
            @endif
            <h2 class="facts__title">{{ $itemTitle }}</h2>
            <dl>
                @foreach ($itemLines as $label => $value)
                    <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                @endforeach
            </dl>
        </aside>
    </div>
</section>
@endsection
