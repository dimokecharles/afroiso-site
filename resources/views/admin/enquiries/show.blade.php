@extends('layouts.admin')
@section('title', 'Enquiry from ' . $enquiry->name)

@section('content')
<div class="head">
    <h1>Enquiry from {{ $enquiry->name }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.enquiries.index') }}">Back to enquiries</a>
</div>
<div class="panel">
    <dl class="detail">
        <div><dt>Received</dt><dd>{{ $enquiry->created_at->format('j F Y, H:i') }}</dd></div>
        <div><dt>Email</dt><dd><a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a></dd></div>
        @if ($enquiry->phone)<div><dt>Phone</dt><dd>{{ $enquiry->phone }}</dd></div>@endif
        @if ($enquiry->organisation)<div><dt>Organisation</dt><dd>{{ $enquiry->organisation }}</dd></div>@endif
        @if ($enquiry->interest)<div><dt>Interested in</dt><dd>{{ $enquiry->interest }}</dd></div>@endif
        @if ($enquiry->subject)<div><dt>Regarding</dt><dd>{{ $enquiry->subject }}</dd></div>@endif
        <div><dt>Message</dt><dd>{!! nl2br(e($enquiry->message)) !!}</dd></div>
    </dl>
</div>
<div class="panel">
    <form method="post" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry?')">
        @csrf @method('delete')
        <button class="btn btn--plain" type="submit">Delete enquiry</button>
    </form>
</div>
@endsection
