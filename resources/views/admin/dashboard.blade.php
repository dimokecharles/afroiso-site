@extends('layouts.admin')
@section('title', 'Dashboard')

@php
    $money = fn ($value) => 'US$' . number_format((float) $value, $value == round($value) ? 0 : 2);
    $change = $revenueLastMonth > 0 ? round(($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth * 100) : null;
    $conversion = $enquirerCount > 0 ? round($convertedCount / $enquirerCount * 100) : null;
    $statusIcons = ['paid' => 'fa-circle-check', 'pending' => 'fa-clock', 'failed' => 'fa-circle-xmark', 'cancelled' => 'fa-ban'];
    $topCourseMax = max(1, $topCourses->max('revenue') ?? 0);
    $topPublicationMax = max(1, $topPublications->max('revenue') ?? 0);
    $interestMax = max(1, $courseInterest->max('enquiries') ?? 0);
    $can = fn (string $area) => auth()->user()->canAccess($area);
@endphp

@section('content')
<div class="head">
    <div>
        <h1>Dashboard</h1>
        <p class="sub">{{ now()->format('l, j F Y') }}</p>
    </div>
    <div class="head__actions">
        <a class="btn btn--plain" href="{{ route('admin.contacts.create') }}">Add a contact</a>
        @if ($can('payments'))
            <a class="btn" href="{{ route('admin.orders.create') }}">New consulting invoice</a>
        @endif
    </div>
</div>

<div class="kpis">
    @if ($can('payments'))
    <a class="kpi" href="{{ route('admin.orders.index', ['status' => 'paid']) }}">
        <span class="kpi__label"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i>Revenue this month</span>
        <strong>{{ $money($revenueThisMonth) }}</strong>
        <span class="kpi__note">
            @if ($change !== null)
                {{ $change >= 0 ? 'Up' : 'Down' }} {{ abs($change) }}% on last month ({{ $money($revenueLastMonth) }})
            @else
                {{ $paidCountThisMonth }} {{ $paidCountThisMonth === 1 ? 'payment' : 'payments' }} received
            @endif
        </span>
    </a>
    <a class="kpi" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">
        <span class="kpi__label"><i class="fa-solid fa-clock" aria-hidden="true"></i>Awaiting payment</span>
        <strong>{{ $pendingCount }}</strong>
        <span class="kpi__note">Worth {{ $money($pendingValue) }}</span>
    </a>
    @endif
    @if ($can('enquiries'))
    <a class="kpi" href="{{ route('admin.enquiries.index', ['status' => 'new']) }}">
        <span class="kpi__label"><i class="fa-solid fa-inbox" aria-hidden="true"></i>New enquiries</span>
        <strong>{{ $newEnquiries }}</strong>
        <span class="kpi__note">{{ $openLeads }} open {{ $openLeads === 1 ? 'lead' : 'leads' }} in total</span>
    </a>
    @endif
    <a class="kpi" href="{{ route('admin.contacts.index') }}">
        <span class="kpi__label"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>Enquiries converted</span>
        <strong>{{ $conversion !== null ? $conversion . '%' : 'No data yet' }}</strong>
        <span class="kpi__note">{{ $convertedCount }} of {{ $enquirerCount }} people who enquired went on to pay</span>
    </a>
</div>

@if ($can('enquiries') && $followUps->isNotEmpty())
<div class="panel panel--alert">
    <h2><i class="fa-solid fa-bell" aria-hidden="true"></i>Follow-ups due</h2>
    <ul class="plain-list">
        @foreach ($followUps as $enquiry)
            <li><a href="{{ route('admin.enquiries.show', $enquiry) }}">{{ $enquiry->name }}</a> <span class="muted">{{ $enquiry->subject ?: $enquiry->interest ?: 'General enquiry' }}, due {{ $enquiry->follow_up_on->format('j M Y') }}</span></li>
        @endforeach
    </ul>
</div>
@endif

@if ($can('payments'))
<div class="grid grid--2">
    <section class="panel">
        <h2>Revenue by month</h2>
        <p class="muted">Payments received in the last six months.</p>
        <div class="bars" role="img" aria-label="Revenue by month: {{ $months->map(fn ($month) => $month['full'] . ' ' . $money($month['total']))->implode(', ') }}">
            @foreach ($months as $month)
                <div class="bars__col" title="{{ $month['full'] }}: {{ $money($month['total']) }}">
                    <span class="bars__value">{{ $month['total'] > 0 ? $money($month['total']) : '' }}</span>
                    <span class="bars__bar" style="height: {{ max($month['total'] > 0 ? 3 : 0, round($month['total'] / $monthMax * 100)) }}%"></span>
                    <span class="bars__label">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <h2>Payment activity</h2>
        <p class="muted">Every enrolment, order and invoice, by status.</p>
        <ul class="status-list">
            @foreach ($statusCounts as $key => $row)
                <li class="status-list__item status-list__item--{{ $key }}">
                    <i class="fa-solid {{ $statusIcons[$key] ?? 'fa-circle' }}" aria-hidden="true"></i>
                    <a href="{{ route('admin.orders.index', ['status' => $key]) }}">{{ $row['label'] }}</a>
                    <strong>{{ $row['total'] }}</strong>
                </li>
            @endforeach
        </ul>
    </section>
</div>

<div class="grid grid--2">
    <section class="panel">
        <h2>Best-selling courses</h2>
        @include('admin._ranking', ['rows' => $topCourses, 'max' => $topCourseMax, 'empty' => 'No paid enrolments yet.', 'unit' => 'enrolment'])
    </section>
    <section class="panel">
        <h2>Best-selling publications</h2>
        @include('admin._ranking', ['rows' => $topPublications, 'max' => $topPublicationMax, 'empty' => 'No paid publication orders yet.', 'unit' => 'order'])
    </section>
</div>

@endif

<div class="grid grid--2">
    <section class="panel">
        <h2>Course interest</h2>
        <p class="muted">Enquiries received and page views in the last 14 days.</p>
        <div class="table-wrap">
            <table class="table--compact">
                <thead><tr><th>Course</th><th>Enquiries</th><th>Page views</th></tr></thead>
                <tbody>
                    @foreach ($courseInterest as $row)
                        <tr>
                            <td>{{ $row['title'] }}</td>
                            <td><span class="meter"><span style="width: {{ round($row['enquiries'] / $interestMax * 100) }}%"></span></span>{{ $row['enquiries'] }}</td>
                            <td>{{ $row['views'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <h2>Website visitors</h2>
        <p class="muted">{{ number_format($visitorTotal) }} {{ $visitorTotal === 1 ? 'visitor' : 'visitors' }} and {{ number_format($viewTotal) }} page {{ $viewTotal === 1 ? 'view' : 'views' }} in the last 14 days.</p>
        <div class="bars bars--thin" role="img" aria-label="Visitors per day: {{ $days->map(fn ($day) => $day['label'] . ' ' . $day['visitors'])->implode(', ') }}">
            @foreach ($days as $day)
                <div class="bars__col" title="{{ $day['label'] }}: {{ $day['visitors'] }} {{ $day['visitors'] === 1 ? 'visitor' : 'visitors' }}, {{ $day['views'] }} page views">
                    <span class="bars__value">{{ $day['visitors'] > 0 && $day['visitors'] === $dayMax ? $day['visitors'] : '' }}</span>
                    <span class="bars__bar" style="height: {{ max($day['visitors'] > 0 ? 3 : 0, round($day['visitors'] / $dayMax * 100)) }}%"></span>
                    <span class="bars__label">{{ $loop->first || $loop->last || $loop->index % 3 === 0 ? $day['short'] : '' }}</span>
                </div>
            @endforeach
        </div>
        <div class="grid grid--2 grid--tight">
            <div>
                <h3>Where visitors come from</h3>
                <ul class="plain-list">
                    @forelse ($topReferrers as $host => $count)
                        <li>{{ $host }} <span class="muted">{{ $count }}</span></li>
                    @empty
                        <li class="muted">No outside links recorded yet.</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3>Most-viewed pages</h3>
                <ul class="plain-list">
                    @forelse ($topPages as $path => $count)
                        <li>{{ $path === '/' ? 'Home page' : $path }} <span class="muted">{{ $count }}</span></li>
                    @empty
                        <li class="muted">No visits recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
</div>

<div class="grid grid--2">
    @if ($can('enquiries'))
    <section class="panel">
        <h2>Latest enquiries</h2>
        @include('admin.enquiries._table', ['enquiries' => $enquiries, 'compact' => true])
        <p class="more"><a href="{{ route('admin.enquiries.index') }}">All enquiries and leads</a></p>
    </section>
    @endif
    @if ($can('payments'))
    <section class="panel">
        <h2>Latest payments and orders</h2>
        <div class="table-wrap">
            <table class="table--compact">
                <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->reference }}</a></td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->amountLabel() }}</td>
                            <td><span @class(['tag', 'tag--ok' => $order->isPaid(), 'tag--bad' => in_array($order->status, ['failed', 'cancelled'])])>{{ $order->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No payments or orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="more"><a href="{{ route('admin.orders.index') }}">All payments</a></p>
    </section>
    @endif
</div>
@endsection
