@extends('layouts.app')
@section('title', $course->title)
@section('description', trim(($course->summary ?: $course->title) . ' ' . $course->delivery . ' course from AfroISO, starting ' . $course->startLabel() . '. Fee: ' . $course->feeLabel() . '.'))

@push('structured-data')
    @php
        $courseData = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course->title,
            'description' => $course->summary ?: $course->title,
            'url' => route('courses.show', $course),
            'provider' => ['@type' => 'EducationalOrganization', '@id' => url('/') . '#organisation', 'name' => config('afroiso.name'), 'url' => url('/')],
        ];
        $instance = ['@type' => 'CourseInstance', 'courseMode' => 'Online'];
        if ($course->start_date) {
            $instance['startDate'] = $course->start_date->toDateString();
        }
        $courseData['hasCourseInstance'] = $instance;
        if ($course->fee !== null) {
            $courseData['offers'] = ['@type' => 'Offer', 'category' => 'Paid', 'price' => number_format((float) $course->fee, 2, '.', ''), 'priceCurrency' => 'USD', 'url' => route('courses.show', $course)];
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($courseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">{{ $course->category ?: 'Training course' }}</div>
        <h1>{{ $course->title }}</h1>
        @if ($course->summary)
            <p>{{ $course->summary }}</p>
        @endif
    </div>
</section>
<section class="section">
    <div class="container split">
        <div class="split__body split__body--wide">
            @if ($course->description)
                <div class="prose">{!! nl2br(e($course->description)) !!}</div>
            @else
                <p>Full course details will be published here shortly. Contact our office for the course outline.</p>
            @endif
        </div>
        <aside class="facts">
            <dl>
                <div><dt>Delivery</dt><dd>{{ $course->delivery }}</dd></div>
                <div><dt>Start date</dt><dd>{{ $course->startLabel() }}</dd></div>
                <div><dt>Fee</dt><dd>{{ $course->feeLabel() }}</dd></div>
            </dl>
            <a class="btn btn--primary" href="{{ route('contact', ['subject' => 'Enrol: ' . $course->title, 'interest' => 'A training course']) }}">Enrol on this course</a>
        </aside>
    </div>
</section>
@endsection
