@extends('layouts.admin')
@section('title', 'Message box')

@section('content')
<div class="head"><h1>Message box</h1></div>
<div class="panel">
    <div class="form__actions">
        <a @class(['btn', 'btn--plain' => $show !== 'waiting']) href="{{ route('admin.messages.index') }}">Waiting for a reply ({{ $waitingCount }})</a>
        <a @class(['btn', 'btn--plain' => $show !== 'all']) href="{{ route('admin.messages.index', ['show' => 'all']) }}">All conversations ({{ $allCount }})</a>
    </div>
</div>
<div class="panel">
    <ul class="inbox">
        @forelse ($conversations as $enquiry)
            <li @class(['inbox__item', 'is-waiting' => $enquiry->awaiting_reply])>
                <a href="{{ route('admin.enquiries.show', $enquiry) }}#conversation">
                    <span class="inbox__who">{{ $enquiry->name }}@if ($enquiry->organisation) <span class="muted">{{ $enquiry->organisation }}</span>@endif</span>
                    <span class="inbox__subject">{{ $enquiry->subject ?: $enquiry->interest ?: 'General enquiry' }}</span>
                    <span class="inbox__preview">{{ \Illuminate\Support\Str::limit($enquiry->message, 110) }}</span>
                </a>
                <div class="inbox__meta">
                    <span @class(['tag', 'tag--new' => $enquiry->awaiting_reply, 'tag--ok' => ! $enquiry->awaiting_reply])>{{ $enquiry->awaiting_reply ? 'Waiting for a reply' : 'Replied' }}</span>
                    <span class="muted">{{ ($enquiry->last_message_at ?? $enquiry->created_at)->format('j M Y, H:i') }}</span>
                    <span class="muted">{{ $enquiry->messages_count + 1 }} {{ $enquiry->messages_count === 0 ? 'message' : 'messages' }}</span>
                </div>
            </li>
        @empty
            <li class="inbox__empty">{{ $show === 'waiting' ? 'Nothing is waiting for a reply.' : 'No conversations yet.' }}</li>
        @endforelse
    </ul>
    <div class="pagination">{{ $conversations->links('admin._pagination') }}</div>
</div>
<div class="panel">
    <h2>What arrives here</h2>
    <p class="muted">Messages sent through the website contact form arrive here automatically. Replies you send from a conversation are emailed to the customer and kept with it. When a customer answers by email, phone or WhatsApp, record their reply in the conversation so the history stays complete.</p>
</div>
@endsection
