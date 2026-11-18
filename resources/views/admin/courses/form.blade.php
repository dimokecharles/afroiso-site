@extends('layouts.admin')
@section('title', $course->exists ? 'Edit course' : 'Add a course')

@section('content')
<div class="head">
    <h1>{{ $course->exists ? 'Edit course' : 'Add a course' }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.courses.index') }}">Back to courses</a>
</div>
<div class="panel">
    <form method="post" action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.courses.store') }}" class="form">
        @csrf
        @if ($course->exists) @method('put') @endif
        <div class="field">
            <label for="title">Course title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $course->title) }}" required>
        </div>
        <div class="field">
            <label for="slug">Web address name</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $course->slug) }}">
            <small>Leave empty to create it from the title. It appears in the page link, for example /courses/lead-auditor.</small>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="code">Homepage card heading</label>
                <input id="code" name="code" type="text" value="{{ old('code', $course->code) }}">
                <small>For example ISO 9001:2026. Leave empty to keep the course off the homepage cards.</small>
            </div>
            <div class="field">
                <label for="category">Line under the heading</label>
                <input id="category" name="category" type="text" value="{{ old('category', $course->category) }}">
                <small>For example Quality management.</small>
            </div>
        </div>
        <div class="field">
            <label for="summary">Short summary</label>
            <textarea id="summary" name="summary" rows="2" maxlength="500">{{ old('summary', $course->summary) }}</textarea>
        </div>
        <div class="field">
            <label for="description">Full description</label>
            <textarea id="description" name="description" rows="10">{{ old('description', $course->description) }}</textarea>
        </div>
        <div class="form__row">
            <div class="field">
                <label for="fee">Fee in US dollars</label>
                <input id="fee" name="fee" type="number" step="0.01" min="0" value="{{ old('fee', $course->fee) }}">
            </div>
            <div class="field">
                <label for="delivery">Delivery</label>
                <input id="delivery" name="delivery" type="text" value="{{ old('delivery', $course->delivery) }}" required>
            </div>
            <div class="field">
                <label for="start_date">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="field">
                <label for="sort_order">Position in lists</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $course->sort_order ?? 0) }}">
                <small>Lower numbers appear first.</small>
            </div>
        </div>
        <label class="check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $course->is_published))> Show this course on the website</label>
        <div class="form__actions"><button class="btn" type="submit">Save course</button></div>
    </form>
</div>
@endsection
