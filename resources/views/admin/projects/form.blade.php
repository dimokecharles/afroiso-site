@extends('layouts.admin')
@section('title', $project->exists ? 'Edit project' : 'New consulting project')

@section('content')
<div class="head">
    <h1>{{ $project->exists ? 'Edit project' : 'New consulting project' }}</h1>
    <a class="btn btn--plain" href="{{ $project->exists ? route('admin.projects.show', $project) : route('admin.projects.index') }}">Back</a>
</div>
<div class="panel">
    @if ($contacts->isEmpty())
        <p>A project needs a client. <a href="{{ route('admin.contacts.create') }}">Add the client as a contact</a> first.</p>
    @else
    <form method="post" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}" class="form">
        @csrf
        @if ($project->exists) @method('put') @endif
        <div class="form__row">
            <div class="field">
                <label for="contact_id">Client</label>
                <select id="contact_id" name="contact_id" required>
                    <option value="">Choose a contact</option>
                    @foreach ($contacts as $contact)
                        <option value="{{ $contact->id }}" @selected((int) old('contact_id', $project->contact_id) === $contact->id)>{{ $contact->organisation ? $contact->organisation . ' (' . $contact->name . ')' : $contact->name }}</option>
                    @endforeach
                </select>
                <small>Not in the list? <a href="{{ route('admin.contacts.create') }}">Add a contact</a> first.</small>
            </div>
            <div class="field">
                <label for="standard">Standard</label>
                <input id="standard" name="standard" type="text" value="{{ old('standard', $project->standard) }}" placeholder="For example ISO 9001:2026">
            </div>
        </div>
        <div class="field">
            <label for="title">Project title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $project->title) }}" required>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="stage">Stage</label>
                <select id="stage" name="stage">
                    @foreach (\App\Models\Project::STAGES as $key => $label)
                        <option value="{{ $key }}" @selected(old('stage', $project->stage) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="assigned_to">Owner</label>
                <select id="assigned_to" name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach ($staff as $person)
                        <option value="{{ $person->id }}" @selected((int) old('assigned_to', $project->assigned_to) === $person->id)>{{ $person->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="value">Agreed value (US$)</label>
                <input id="value" name="value" type="number" min="0" step="0.01" value="{{ old('value', $project->value) }}">
            </div>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="start_date">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="field">
                <label for="target_date">Target date</label>
                <input id="target_date" name="target_date" type="date" value="{{ old('target_date', $project->target_date?->format('Y-m-d')) }}">
            </div>
        </div>
        <div class="field">
            <label for="description">Scope and description</label>
            <textarea id="description" name="description" rows="6">{{ old('description', $project->description) }}</textarea>
        </div>
        <div class="form__actions"><button class="btn" type="submit">Save project</button></div>
    </form>
    @endif
</div>
@endsection
