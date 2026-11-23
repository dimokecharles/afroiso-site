@extends('layouts.admin')
@section('title', $contact->name)

@section('content')
<div class="head">
    <div>
        <h1>{{ $contact->name }}</h1>
        @if ($contact->organisation)
            <p class="sub">{{ $contact->organisation }}</p>
        @endif
    </div>
    <div class="head__actions">
        <a class="btn btn--plain" href="{{ route('admin.contacts.index') }}">Back to contacts</a>
        <a class="btn" href="{{ route('admin.contacts.edit', $contact) }}">Edit contact</a>
    </div>
</div>

<div class="kpis">
    <div class="kpi"><span class="kpi__label">Paid in total</span><strong>US${{ number_format($paidTotal, 2) }}</strong></div>
    <div class="kpi"><span class="kpi__label">Orders</span><strong>{{ $contact->orders->count() }}</strong></div>
    <div class="kpi"><span class="kpi__label">Enquiries</span><strong>{{ $contact->enquiries->count() }}</strong></div>
</div>

<div class="two-col">
    <div>
        <div class="panel">
            <h2>History</h2>
            @if ($history->isEmpty())
                <p class="muted">Nothing yet. Enquiries, enrolments, orders and invoices for this contact will appear here.</p>
            @else
                <ul class="timeline">
                    @foreach ($history as $item)
                        <li>
                            <i class="fa-solid {{ $item['icon'] }}" aria-hidden="true"></i>
                            <div>
                                <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                                <time datetime="{{ $item['when']->toIso8601String() }}">{{ $item['when']->format('j M Y, H:i') }} · {{ $item['detail'] }}</time>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="panel">
            @include('admin._notes', ['on' => 'contact', 'record' => $contact, 'notes' => $contact->notes])
        </div>
    </div>
    <div class="panel">
        <h2>Details</h2>
        <dl class="detail">
            <div><dt>Email</dt><dd>@if ($contact->email)<a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>@else Not given @endif</dd></div>
            <div><dt>Phone</dt><dd>{{ $contact->phone ?: 'Not given' }}</dd></div>
            <div><dt>Organisation</dt><dd>{{ $contact->organisation ?: 'Not given' }}</dd></div>
            <div><dt>Country</dt><dd>{{ $contact->country ?: 'Not given' }}</dd></div>
            <div><dt>First seen</dt><dd>{{ $contact->created_at->format('j F Y') }}, from {{ ['enquiry' => 'an enquiry', 'order' => 'an order', 'manual' => 'staff entry'][$contact->source] ?? $contact->source }}</dd></div>
        </dl>
    </div>
</div>
@endsection
