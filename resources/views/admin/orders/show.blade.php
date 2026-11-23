@extends('layouts.admin')
@section('title', $order->reference)

@section('content')
<div class="head">
    <h1>{{ $order->typeLabel() }} {{ $order->reference }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.orders.index') }}">Back to payments</a>
</div>
<div class="panel">
    <dl class="detail">
        <div><dt>Status</dt><dd><span @class(['tag', 'tag--ok' => $order->isPaid(), 'tag--bad' => in_array($order->status, ['failed', 'cancelled'])])>{{ $order->statusLabel() }}</span></dd></div>
        <div><dt>For</dt><dd>{{ $order->description }}</dd></div>
        <div><dt>Amount</dt><dd>{{ $order->amountLabel() }}</dd></div>
        <div><dt>Customer</dt><dd>{{ $order->customer_name }}@if ($order->organisation), {{ $order->organisation }}@endif</dd></div>
        <div><dt>Email</dt><dd><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></dd></div>
        @if ($order->customer_phone)
            <div><dt>Phone</dt><dd>{{ $order->customer_phone }}</dd></div>
        @endif
        <div><dt>Created</dt><dd>{{ $order->created_at->format('j F Y, H:i') }}</dd></div>
        @if ($order->paid_at)
            <div><dt>Paid</dt><dd>{{ $order->paid_at->format('j F Y, H:i') }} ({{ $order->gateway }}{{ $order->gateway_reference ? ', ' . $order->gateway_reference : '' }})</dd></div>
        @endif
        @if ($order->notes)
            <div><dt>Internal notes</dt><dd>{!! nl2br(e($order->notes)) !!}</dd></div>
        @endif
    </dl>
</div>
<div class="panel">
    <h2>Pay link</h2>
    <p>Anyone with this link can view this {{ $order->type === 'invoice' ? 'invoice' : 'order' }} and pay it. Send it only to the customer.</p>
    <div class="field">
        <label for="paylink">Link</label>
        <input id="paylink" type="text" value="{{ $order->payUrl() }}" readonly onclick="this.select()">
    </div>
    @if ($order->canBePaid())
        <div class="form__actions" style="margin-top: 16px">
            <form method="post" action="{{ route('admin.orders.send', $order) }}">
                @csrf
                <button class="btn" type="submit">Email pay link to {{ $order->customer_email }}</button>
            </form>
            <form method="post" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('Mark this as paid? Use this when the money was received outside the website.')">
                @csrf @method('put')
                <button class="btn btn--plain" type="submit" name="action" value="mark-paid">Mark as paid</button>
            </form>
            <form method="post" action="{{ route('admin.orders.update', $order) }}" onsubmit="return confirm('Cancel this order?')">
                @csrf @method('put')
                <button class="btn btn--plain" type="submit" name="action" value="cancel">Cancel</button>
            </form>
        </div>
    @endif
</div>
@endsection
