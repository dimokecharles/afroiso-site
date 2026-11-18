@extends('layouts.admin')
@section('title', 'Publications')

@section('content')
<div class="head">
    <h1>Publications</h1>
    <a class="btn" href="{{ route('admin.publications.create') }}">Add a publication</a>
</div>
<div class="panel table-wrap">
    <table>
        <thead><tr><th>Title</th><th>Type</th><th>Price</th><th>Status</th><th><span style="position:absolute;left:-9999px">Actions</span></th></tr></thead>
        <tbody>
            @forelse ($publications as $publication)
                <tr>
                    <td>{{ $publication->title }}</td>
                    <td>{{ $publication->typeLabel() }}</td>
                    <td>{{ $publication->priceLabel() }}</td>
                    <td><span @class(['tag', 'tag--ok' => $publication->is_published])>{{ $publication->is_published ? 'Published' : 'Hidden' }}</span></td>
                    <td class="actions">
                        <a href="{{ route('admin.publications.edit', $publication) }}">Edit</a>
                        <form method="post" action="{{ route('admin.publications.destroy', $publication) }}" onsubmit="return confirm('Delete this publication?')">
                            @csrf @method('delete')
                            <button class="link-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No publications yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
