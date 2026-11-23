@extends('layouts.admin')
@section('title', 'Enrolments')

@section('content')
<div class="head"><h1>Enrolments</h1></div>
<div class="panel">
    <h2>By course</h2>
    <div class="table-wrap">
        <table class="table--compact">
            <thead><tr><th>Course</th><th>Start date</th><th>Paid</th><th>Awaiting payment</th><th><span class="vh">Actions</span></th></tr></thead>
            <tbody>
                @foreach ($summary as $row)
                    <tr>
                        <td>{{ $row['course']->title }}</td>
                        <td>{{ $row['course']->startLabel() }}</td>
                        <td>{{ $row['paid'] }}</td>
                        <td>{{ $row['pending'] }}</td>
                        <td class="actions"><a href="{{ route('admin.enrolments.index', ['course' => $row['course']->id]) }}">Show list</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="panel">
    <form method="get" action="{{ route('admin.enrolments.index') }}" class="filters">
        <div class="field">
            <label for="course">Course</label>
            <select id="course" name="course">
                <option value="">All courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected($courseId === $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="status">Payment status</label>
            <select id="status" name="status">
                <option value="">All statuses</option>
                @foreach (\App\Models\Order::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn--plain" type="submit">Filter</button>
    </form>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Enrolled</th><th>Name</th><th>Email</th><th>Course</th><th>Fee</th><th>Status</th><th><span class="vh">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($enrolments as $order)
                    <tr>
                        <td>{{ $order->created_at->format('j M Y') }}</td>
                        <td>@if ($order->contact_id)<a href="{{ route('admin.contacts.show', $order->contact_id) }}">{{ $order->customer_name }}</a>@else{{ $order->customer_name }}@endif</td>
                        <td>{{ $order->customer_email }}</td>
                        <td>{{ $order->course?->title ?? $order->description }}</td>
                        <td>{{ $order->amountLabel() }}</td>
                        <td><span @class(['tag', 'tag--ok' => $order->isPaid(), 'tag--bad' => in_array($order->status, ['failed', 'cancelled'])])>{{ $order->statusLabel() }}</span></td>
                        <td class="actions"><a href="{{ route('admin.orders.show', $order) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7">No enrolments match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $enrolments->links('admin._pagination') }}</div>
</div>
@endsection
