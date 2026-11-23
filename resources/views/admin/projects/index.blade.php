@extends('layouts.admin')
@section('title', 'Consulting projects')

@section('content')
<div class="head">
    <h1>Consulting projects</h1>
    <a class="btn" href="{{ route('admin.projects.create') }}">New project</a>
</div>
<div class="panel">
    <div class="form__actions">
        <a @class(['btn', 'btn--plain' => $stage !== null]) href="{{ route('admin.projects.index') }}">All ({{ $counts->sum() }})</a>
        @foreach (\App\Models\Project::STAGES as $key => $label)
            <a @class(['btn', 'btn--plain' => $stage !== $key]) href="{{ route('admin.projects.index', ['stage' => $key]) }}">{{ $label }} ({{ $counts[$key] ?? 0 }})</a>
        @endforeach
    </div>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Project</th><th>Client</th><th>Stage</th><th>Owner</th><th>Target date</th><th>Value</th><th>Paid</th><th><span class="vh">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td><a href="{{ route('admin.projects.show', $project) }}">{{ $project->title }}</a>@if ($project->standard)<br><span class="muted">{{ $project->standard }}</span>@endif</td>
                        <td>{{ $project->contact->organisation ?: $project->contact->name }}</td>
                        <td><span @class(['tag', 'tag--ok' => $project->stage === 'completed', 'tag--off' => $project->stage === 'on-hold', 'tag--new' => $project->stage === 'proposal'])>{{ $project->stageLabel() }}</span></td>
                        <td>{{ $project->owner?->name ?? 'Unassigned' }}</td>
                        <td>{{ $project->target_date?->format('j M Y') ?? 'Not set' }}@if ($project->isOverdue()) <span class="tag tag--bad">Overdue</span>@endif</td>
                        <td>{{ $project->valueLabel() }}</td>
                        <td>US${{ number_format((float) $project->paid_total, 2) }}</td>
                        <td class="actions"><a href="{{ route('admin.projects.show', $project) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8">No consulting projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $projects->links('admin._pagination') }}</div>
</div>
@endsection
