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
            <h2>From</h2>
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
            </dl>
        </div>
        <div class="panel" id="conversation">
            <div class="head">
                <h2>Conversation</h2>
                @if ($enquiry->awaiting_reply)
                    <form method="post" action="{{ route('admin.enquiries.done', $enquiry) }}">
                        @csrf
                        <button class="btn btn--plain" type="submit">No reply needed</button>
                    </form>
                @endif
            </div>
            <div class="thread">
                <div class="bubble bubble--in">
                    <div class="bubble__meta">{{ $enquiry->name }}, {{ $enquiry->created_at->format('j M Y, H:i') }}, website form</div>
                    <p>{{ $enquiry->message }}</p>
                </div>
                @foreach ($enquiry->messages as $message)
                    <div @class(['bubble', 'bubble--out' => $message->isOutgoing(), 'bubble--in' => ! $message->isOutgoing()])>
                        <div class="bubble__meta">
                            @if ($message->isOutgoing())
                                {{ $message->user?->name ?? 'Staff' }}, {{ $message->created_at->format('j M Y, H:i') }}
                                @if ($message->delivery === 'sent')
                                    <span class="tag tag--ok">Emailed</span>
                                @elseif ($message->delivery === 'logged')
                                    <span class="tag tag--off">Not emailed: email is off</span>
                                @else
                                    <span class="tag tag--bad">Email failed</span>
                                @endif
                            @else
                                {{ $enquiry->name }}, {{ $message->created_at->format('j M Y, H:i') }}, recorded by {{ $message->user?->name ?? 'staff' }}
                            @endif
                        </div>
                        <p>{{ $message->body }}</p>
                    </div>
                @endforeach
            </div>
            <form method="post" action="{{ route('admin.enquiries.reply', $enquiry) }}" class="form" style="margin-top: 20px">
                @csrf
                <div class="field">
                    <label for="reply-body">Reply to {{ $enquiry->name }}</label>
                    <textarea id="reply-body" name="body" rows="5" required>{{ old('body') }}</textarea>
                    <small>Sent by email to {{ $enquiry->email }}, starting "Dear {{ $enquiry->name }}," and signed with your name. A copy is kept here.</small>
                </div>
                <div class="form__actions"><button class="btn" type="submit">Send reply</button></div>
            </form>
            <details class="received">
                <summary>Record a reply from the customer</summary>
                <form method="post" action="{{ route('admin.enquiries.received', $enquiry) }}" class="form">
                    @csrf
                    <div class="field">
                        <label for="received-body">What the customer said</label>
                        <textarea id="received-body" name="body" rows="4" required></textarea>
                        <small>Use this when the customer answers by email, phone or WhatsApp. Nothing is sent.</small>
                    </div>
                    <div class="form__actions"><button class="btn btn--plain" type="submit">Record customer reply</button></div>
                </form>
            </details>
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
