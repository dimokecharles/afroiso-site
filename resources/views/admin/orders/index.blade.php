@extends('layouts.admin')
@section('title', 'Payments')

@section('content')
<div class="head">
    <h1>Payments</h1>
    <a class="btn" href="{{ route('admin.orders.create') }}">New consulting invoice</a>
</div>
<div class="stats">
    <div class="stat"><strong>US${{ number_format((float) $paidTotal, 2) }}</strong><span>Paid in total</span></div>
    <a class="stat" href="{{ route('admin.orders.index', ['status' => 'pending']) }}"><strong>{{ $pendingCount }}</strong><span>Awaiting payment</span></a>
</div>
<div class="panel">
    <form method="get" action="{{ route('admin.orders.index') }}" class="form">
        <div class="form__row">
            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="">All types</option>
                    @foreach (\App\Models\Order::TYPES as $key => $label)
                        <option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach (\App\Models\Order::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form__actions"><button class="btn btn--plain" type="submit">Filter</button></div>
    </form>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Reference</th><th>Date</th><th>Type</th><th>Customer</th><th>For</th><th>Amount</th><th>Status</th><th><span style="position:absolute;left:-9999px">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->reference }}</td>
                        <td>{{ $order->created_at->format('j M Y, H:i') }}</td>
                        <td>{{ $order->typeLabel() }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->description }}</td>
                        <td>{{ $order->amountLabel() }}</td>
                        <td><span @class(['tag', 'tag--ok' => $order->isPaid(), 'tag--bad' => in_array($order->status, ['failed', 'cancelled']), 'tag--off' => $order->status === 'refunded'])>{{ $order->statusLabel() }}</span></td>
                        <td class="actions"><a href="{{ route('admin.orders.show', $order) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8">No payments or orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $orders->links('admin._pagination') }}</div>
</div>
@endsection
