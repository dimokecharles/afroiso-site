@extends('layouts.admin')
@section('title', $publication->exists ? 'Edit publication' : 'Add a publication')

@section('content')
<div class="head">
    <h1>{{ $publication->exists ? 'Edit publication' : 'Add a publication' }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.publications.index') }}">Back to publications</a>
</div>
<div class="panel">
    <form method="post" enctype="multipart/form-data" action="{{ $publication->exists ? route('admin.publications.update', $publication) : route('admin.publications.store') }}" class="form">
        @csrf
        @if ($publication->exists) @method('put') @endif
        <div class="form__row">
            <div class="field">
                <label for="type">Type</label>
                <select id="type" name="type">
                    <option value="book" @selected(old('type', $publication->type) === 'book')>Book</option>
                    <option value="magazine" @selected(old('type', $publication->type) === 'magazine')>Magazine</option>
                </select>
            </div>
            <div class="field">
                <label for="price">Price in US dollars</label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $publication->price) }}">
            </div>
            <div class="field">
                <label for="sort_order">Position in lists</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $publication->sort_order ?? 0) }}">
            </div>
        </div>
        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" type="text" value="{{ old('title', $publication->title) }}" required>
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="6">{{ old('description', $publication->description) }}</textarea>
        </div>
        <div class="field">
            <label for="cover">Cover image</label>
            @if ($publication->cover_path)
                <img class="cover-preview" src="{{ asset($publication->cover_path) }}" alt="Current cover">
            @endif
            <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp">
            <small>JPG, PNG or WebP, up to 4 MB. Choosing a new file replaces the current cover.</small>
        </div>
        <label class="check"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $publication->is_published))> Show this publication on the website</label>
        <div class="form__actions"><button class="btn" type="submit">Save publication</button></div>
    </form>
</div>
@endsection
