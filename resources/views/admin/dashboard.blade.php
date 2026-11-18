@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="head"><h1>Dashboard</h1></div>
<div class="stats">
    <a class="stat" href="{{ route('admin.enquiries.index') }}"><strong>{{ $unread }}</strong><span>Unread enquiries</span></a>
    <a class="stat" href="{{ route('admin.courses.index') }}"><strong>{{ $courseCount }}</strong><span>Courses</span></a>
    <a class="stat" href="{{ route('admin.publications.index') }}"><strong>{{ $publicationCount }}</strong><span>Publications</span></a>
    <a class="stat" href="{{ route('admin.certificates.index') }}"><strong>{{ $certificateCount }}</strong><span>Certificates</span></a>
</div>
<div class="panel">
    <h2>Latest enquiries</h2>
    @include('admin.enquiries._table', ['enquiries' => $enquiries])
</div>
@endsection
