@extends('layouts.admin')
@section('title', $project->title)

@php
    $position = array_search($project->stage, \App\Models\Project::PATH, true);
    $canPay = auth()->user()->canAccess('payments');
@endphp

@section('content')
<div class="head">
    <div>
        <h1>{{ $project->title }}</h1>
        <p class="sub"><span @class(['tag', 'tag--ok' => $project->stage === 'completed', 'tag--off' => $project->stage === 'on-hold', 'tag--new' => $project->stage === 'proposal'])>{{ $project->stageLabel() }}</span>
            {{ $project->contact->organisation ?: $project->contact->name }}@if ($project->isOverdue()) <span class="tag tag--bad">Overdue</span>@endif</p>
    </div>
    <div class="head__actions">
        <a class="btn btn--plain" href="{{ route('admin.projects.index') }}">Back to projects</a>
        <a class="btn" href="{{ route('admin.projects.edit', $project) }}">Edit project</a>
    </div>
</div>

<div class="panel">
    <h2>Progress</h2>
    <ol class="stages">
        @foreach (\App\Models\Project::PATH as $index => $key)
            <li @class(['is-done' => $position !== false && $index < $position, 'is-current' => $position !== false && $index === $position])><span>{{ $index + 1 }}</span>{{ \App\Models\Project::STAGES[$key] }}</li>
        @endforeach
    </ol>
    @if ($project->stage === 'on-hold')
        <p class="muted">This project is on hold.</p>
    @endif
</div>

<div class="kpis">
    <div class="kpi"><span class="kpi__label">Agreed value</span><strong>{{ $project->valueLabel() }}</strong></div>
    <div class="kpi"><span class="kpi__label">Invoiced</span><strong>US${{ number_format($invoiced, 2) }}</strong></div>
    <div class="kpi"><span class="kpi__label">Paid</span><strong>US${{ number_format($paid, 2) }}</strong><span class="kpi__note">US${{ number_format(max(0, $invoiced - $paid), 2) }} outstanding</span></div>
</div>

<div class="two-col">
    <div>
        <div class="panel">
            <div class="head">
                <h2>Invoices</h2>
                @if ($canPay)
                    <a class="btn btn--plain" href="{{ route('admin.orders.create', ['project' => $project->id]) }}">New invoice for this project</a>
                @endif
            </div>
            <div class="table-wrap">
                <table class="table--compact">
                    <thead><tr><th>Reference</th><th>For</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($project->invoices as $invoice)
                            <tr>
                                <td>@if ($canPay)<a href="{{ route('admin.orders.show', $invoice) }}">{{ $invoice->reference }}</a>@else{{ $invoice->reference }}@endif</td>
                                <td>{{ $invoice->description }}</td>
                                <td>{{ $invoice->amountLabel() }}</td>
                                <td><span @class(['tag', 'tag--ok' => $invoice->isPaid(), 'tag--bad' => in_array($invoice->status, ['failed', 'cancelled']), 'tag--off' => $invoice->status === 'refunded'])>{{ $invoice->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No invoices for this project yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel">
            @include('admin._notes', ['on' => 'project', 'record' => $project, 'notes' => $project->notes])
        </div>
    </div>
    <div class="panel">
        <h2>Details</h2>
        <dl class="detail">
            <div><dt>Client</dt><dd><a href="{{ route('admin.contacts.show', $project->contact) }}">{{ $project->contact->name }}</a>{{ $project->contact->organisation ? ', ' . $project->contact->organisation : '' }}</dd></div>
            <div><dt>Standard</dt><dd>{{ $project->standard ?: 'Not set' }}</dd></div>
            <div><dt>Owner</dt><dd>{{ $project->owner?->name ?? 'Unassigned' }}</dd></div>
            <div><dt>Start date</dt><dd>{{ $project->start_date?->format('j F Y') ?? 'Not set' }}</dd></div>
            <div><dt>Target date</dt><dd>{{ $project->target_date?->format('j F Y') ?? 'Not set' }}</dd></div>
            @if ($project->description)
                <div><dt>Scope and description</dt><dd>{!! nl2br(e($project->description)) !!}</dd></div>
            @endif
        </dl>
    </div>
</div>
@endsection
