@extends('layouts.app')
@section('title', 'Training courses')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">Training courses</div>
        <h1>Courses and dates</h1>
        <p>All courses are delivered through our e-learning platform and are open to individuals and organisations.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        @include('courses._intake', ['courses' => $courses])
    </div>
</section>
@endsection
