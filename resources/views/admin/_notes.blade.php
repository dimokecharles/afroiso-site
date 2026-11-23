{{-- Staff notes on a contact or an enquiry. Pass $on ("contact" or "enquiry"), $record and $notes. --}}
<h2>Notes</h2>
<form method="post" action="{{ route('admin.notes.store') }}" class="form">
    @csrf
    <input type="hidden" name="on" value="{{ $on }}">
    <input type="hidden" name="id" value="{{ $record->id }}">
    <div class="field">
        <label for="note-body">Add a note</label>
        <textarea id="note-body" name="body" rows="3" required>{{ old('body') }}</textarea>
    </div>
    <div class="form__actions"><button class="btn" type="submit">Save note</button></div>
</form>
@forelse ($notes as $note)
    <div class="note">
        <time datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('j M Y, H:i') }}, {{ $note->user?->name ?? 'Staff' }}</time>
        <p>{{ $note->body }}</p>
    </div>
@empty
    <p class="muted" style="margin-top: 14px">No notes yet.</p>
@endforelse
