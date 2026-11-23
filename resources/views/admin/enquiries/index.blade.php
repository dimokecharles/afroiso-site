@extends('layouts.admin')
@section('title', 'Enquiries and leads')

@section('content')
<div class="head"><h1>Enquiries and leads</h1></div>
<div class="panel">
    <div class="form__actions">
        <a @class(['btn', 'btn--plain' => $status !== null]) href="{{ route('admin.enquiries.index') }}">All ({{ $counts->sum() }})</a>
        @foreach (\App\Models\Enquiry::STATUSES as $key => $label)
            <a @class(['btn', 'btn--plain' => $status !== $key]) href="{{ route('admin.enquiries.index', ['status' => $key]) }}">{{ $label }} ({{ $counts[$key] ?? 0 }})</a>
        @endforeach
    </div>
</div>
<div class="panel">
    @include('admin.enquiries._table', ['enquiries' => $enquiries])
    <div class="pagination">{{ $enquiries->links('admin._pagination') }}</div>
</div>
@endsection
