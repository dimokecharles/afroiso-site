<!doctype html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #1B2333; line-height: 1.5">
    @if ($kind === 'placed')
        <h2 style="color: #0F1E45">New {{ strtolower($order->typeLabel()) }}</h2>
        <p>{{ $order->customer_name }} has started a {{ strtolower($order->typeLabel()) }} on the website. Payment status: {{ $order->statusLabel() }}.</p>
    @elseif ($kind === 'receipt')
        <h2 style="color: #0F1E45">Payment received</h2>
        <p>Dear {{ $order->customer_name }}, thank you. We have received your payment.</p>
    @else
        <h2 style="color: #0F1E45">Your invoice from {{ config('afroiso.name') }}</h2>
        <p>Dear {{ $order->customer_name }}, you can view and pay this invoice online using the secure link below.</p>
    @endif

    <p><strong>Reference:</strong> {{ $order->reference }}</p>
    <p><strong>For:</strong> {{ $order->description }}</p>
    <p><strong>Amount:</strong> {{ $order->amountLabel() }}</p>
    @if ($kind === 'placed')
        <p><strong>Name:</strong> {{ $order->customer_name }}</p>
        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
        @if ($order->customer_phone)<p><strong>Phone:</strong> {{ $order->customer_phone }}</p>@endif
        @if ($order->organisation)<p><strong>Organisation:</strong> {{ $order->organisation }}</p>@endif
    @endif
    @if ($kind === 'receipt' && $order->paid_at)
        <p><strong>Paid on:</strong> {{ $order->paid_at->format('j F Y, H:i') }}</p>
    @endif
    @if ($kind !== 'placed')
        <p><a href="{{ $order->payUrl() }}">{{ $kind === 'receipt' ? 'View your receipt' : 'View and pay this invoice' }}</a></p>
        <p style="color: #4A5568; font-size: 13px">{{ config('afroiso.name') }}, {{ config('afroiso.address') }}. Questions: {{ config('afroiso.email') }}</p>
    @endif
</body>
</html>
