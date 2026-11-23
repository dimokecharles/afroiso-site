@extends('layouts.admin')
@section('title', $item['label'])

@section('content')
<div class="head">
    <h1>{{ $item['label'] }}</h1>
    <a class="btn btn--plain" href="{{ route('admin.texts.index') }}">Back to website text</a>
</div>
<div class="panel">
    <form method="post" action="{{ route('admin.texts.update', $key) }}" class="form" style="max-width: none">
        @csrf @method('put')
        <div class="field">
            <label for="value">Wording</label>
            @if ($item['kind'] === 'line')
                <input id="value" name="value" type="text" value="{{ old('value', $value) }}" maxlength="200" required>
            @else
                <textarea id="value" name="value" rows="{{ $item['kind'] === 'page' ? 30 : 6 }}" required>{{ old('value', $value) }}</textarea>
            @endif
            @if ($item['kind'] === 'page')
                <small>Start a line with ## for a heading and ### for a smaller heading. Start lines with - for a bulleted list, or with 1. 2. 3. for a numbered list. Leave an empty line between paragraphs.</small>
            @endif
        </div>
        <div class="form__actions">
            <button class="btn" type="submit" name="action" value="save">Save</button>
            @if ($changed)
                <button class="btn btn--plain" type="submit" name="action" value="reset" formnovalidate onclick="return confirm('Throw away the edited wording and go back to the built-in wording?')">Go back to the built-in wording</button>
            @endif
        </div>
    </form>
</div>
@if (str_starts_with($key, 'policy.') && ! $changed)
    <div class="panel">
        <p class="muted">This is the built-in draft, loaded for editing. Links and the yellow "to confirm" highlights from the draft are shown here as plain text. Nothing changes on the website until you save.</p>
    </div>
@endif
@endsection
