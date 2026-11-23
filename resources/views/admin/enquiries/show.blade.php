@extends('layouts.admin')
@section('title', 'Enquiry from ' . $enquiry->name)

@section('content')
<div class="head">
    <div>
        <h1>Enquiry from {{ $enquiry->name }}</h1>
        <p class="sub"><span @class(['tag', 'tag--new' => $enquiry->status === 'new', 'tag--ok' => $enquiry->status === 'converted', 'tag--bad' => $enquiry->status === 'closed'])>{{ $enquiry->statusLabel() }}</span> Received {{ $enquiry->created_at->format('j F Y, H:i') }}</p>
    </div>
    <div class="head__actions">
        <a class="btn btn--plain" href="{{ route('admin.enquiries.index') }}">Back to enquiries</a>
        @if ($enquiry->contact)
            <a class="btn" href="{{ route('admin.contacts.show', $enquiry->contact) }}">Open contact</a>
        @endif
    </div>
</div>

<div class="two-col">
    <div>
        <div class="panel">
            <h2>Message</h2>
            <dl class="detail">
                <div><dt>Email</dt><dd><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></dd></div>
                @if ($enquiry->phone)
                    <div><dt>Phone</dt><dd>{{ $enquiry->phone }}</dd></div>
                @endif
                @if ($enquiry->organisation)
                    <div><dt>Organisation</dt><dd>{{ $enquiry->organisation }}</dd></div>
                @endif
                @if ($enquiry->interest)
                    <div><dt>Interested in</dt><dd>{{ $enquiry->interest }}</dd></div>
                @endif
                @if ($enquiry->subject)
                    <div><dt>Regarding</dt><dd>{{ $enquiry->subject }}</dd></div>
                @endif
                <div><dt>Message</dt><dd>{!! nl2br(e($enquiry->message)) !!}</dd></div>
            </dl>
            <div class="form__actions" style="margin-top: 18px">
                <a class="btn" href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: ' . ($enquiry->subject ?: 'Your enquiry to AfroISO')) }}">Reply by email</a>
            </div>
        </div>
        <div class="panel">
            @include('admin._notes', ['on' => 'enquiry', 'record' => $enquiry, 'notes' => $enquiry->notes])
        </div>
    </div>
    <div>
        <div class="panel">
            <h2>Lead</h2>
            <form method="post" action="{{ route('admin.enquiries.update', $enquiry) }}" class="form">
                @csrf @method('put')
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        @foreach (\App\Models\Enquiry::STATUSES as $key => $label)
                            <option value="{{ $key }}" @selected(old('status', $enquiry->status) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="assigned_to">Owner</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $person)
                            <option value="{{ $person->id }}" @selected((int) old('assigned_to', $enquiry->assigned_to) === $person->id)>{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="follow_up_on">Follow up on</label>
                    <input id="follow_up_on" name="follow_up_on" type="date" value="{{ old('follow_up_on', $enquiry->follow_up_on?->format('Y-m-d')) }}">
                </div>
                <div class="form__actions"><button class="btn" type="submit">Save lead</button></div>
            </form>
        </div>
        <div class="panel">
            <form method="post" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry?')">
                @csrf @method('delete')
                <button class="btn btn--plain" type="submit">Delete enquiry</button>
            </form>
        </div>
    </div>
</div>
@endsection
