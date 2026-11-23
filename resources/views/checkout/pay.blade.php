@extends('layouts.app')
@section('title', $order->isPaid() ? 'Payment received' : 'Payment')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">{{ $order->typeLabel() }}</div>
        <h1>{{ $order->isPaid() ? 'Payment received' : 'Payment' }}</h1>
        <p>Reference {{ $order->reference }}</p>
    </div>
</section>
<section class="section">
    <div class="container narrow">
        @if ($order->type !== 'invoice')
            <ol class="checkout-steps" aria-label="Steps">
                <li class="is-done"><span>1</span>Your details</li>
                <li @class(['is-current' => ! $order->isPaid(), 'is-done' => $order->isPaid()])><span>2</span>Payment</li>
                <li @class(['is-current' => $order->isPaid()])><span>3</span>Confirmation</li>
            </ol>
        @endif

        @if ($order->isPaid())
            <div class="cert" role="status">
                <div class="cert__seal">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    <h2>Thank you. Your payment was received.</h2>
                    <p>A receipt has been sent to {{ $order->customer_email }}.</p>
                </div>
                <dl class="cert__facts">
                    <div><dt>Reference</dt><dd>{{ $order->reference }}</dd></div>
                    <div><dt>For</dt><dd>{{ $order->description }}</dd></div>
                    <div><dt>Amount paid</dt><dd>{{ $order->amountLabel() }}</dd></div>
                    <div><dt>Paid on</dt><dd>{{ $order->paid_at?->format('j F Y, H:i') }}</dd></div>
                </dl>
            </div>
            @if ($order->type === 'enrolment')
                <p class="pay-note">Your place on the course is confirmed. Our office will send you the joining details before the start date.</p>
            @elseif ($order->type === 'publication')
                <p class="pay-note">Our office will send your publication to {{ $order->customer_email }}.</p>
            @endif
        @elseif ($order->status === 'cancelled')
            <div class="vnotice" role="status">
                <i class="fa-solid fa-ban" aria-hidden="true"></i>
                <div>
                    <h2>This order was cancelled</h2>
                    <p>If you think this is a mistake, contact our office and quote reference {{ $order->reference }}.</p>
                    <a class="btn btn--primary btn--cut" href="{{ route('contact', ['subject' => 'Order ' . $order->reference]) }}">Contact our office</a>
                </div>
            </div>
        @else
            @if ($order->status === 'failed')
                <div class="form-error" role="alert">The payment did not go through and you have not been charged. You can try again below.</div>
            @endif

            <div class="pay-card">
                <dl class="cert__facts">
                    <div><dt>Reference</dt><dd>{{ $order->reference }}</dd></div>
                    <div><dt>For</dt><dd>{{ $order->description }}</dd></div>
                    <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                    @if ($order->organisation)
                        <div><dt>Organisation</dt><dd>{{ $order->organisation }}</dd></div>
                    @endif
                    <div class="pay-card__total"><dt>Amount to pay</dt><dd>{{ $order->amountLabel() }}</dd></div>
                </dl>

                @if ($gateway === 'test')
                    <div class="pay-card__actions">
                        <p class="pay-card__test"><strong>Test payment.</strong> The real payment provider is not connected yet. On this UAT site these buttons stand in for it, and no money moves.</p>
                        <form method="post" action="{{ route('pay.process', ['order' => $order->token]) }}">
                            @csrf
                            <button class="btn btn--accent btn--cut" type="submit" name="outcome" value="success">Pay {{ $order->amountLabel() }} (test)<i class="fa-solid fa-lock" aria-hidden="true"></i></button>
                            <button class="btn btn--outline btn--cut" type="submit" name="outcome" value="fail">Simulate a failed payment</button>
                        </form>
                    </div>
                @else
                    <div class="pay-card__actions">
                        <p><strong>Online payment is being set up.</strong> Your details are saved under reference {{ $order->reference }}. Our office will contact you at {{ $order->customer_email }} with the payment details.</p>
                        <a class="btn btn--primary btn--cut" href="{{ route('contact', ['subject' => 'Payment for ' . $order->reference]) }}">Contact our office</a>
                    </div>
                @endif
            </div>
            <p class="pay-note">Keep this page's address if you want to come back and pay later. It is private to this order.</p>
        @endif
    </div>
</section>
@endsection
