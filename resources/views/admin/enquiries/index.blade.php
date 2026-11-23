@extends('layouts.admin')
@section('title', 'Enquiries')

@section('content')
<div class="head"><h1>Enquiries</h1></div>
<div class="panel">
    @include('admin.enquiries._table', ['enquiries' => $enquiries])
    <div class="pagination">{{ $enquiries->links('admin._pagination') }}</div>
</div>
@endsection
