@extends('layouts.admin')
@section('title', 'Certificates')

@section('content')
<div class="head">
    <h1>Certificates</h1>
    <a class="btn" href="{{ route('admin.certificates.create') }}">Add a certificate</a>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Number</th><th>Issued to</th><th>Course</th><th>Date issued</th><th>Status</th><th><span style="position:absolute;left:-9999px">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($certificates as $certificate)
                    <tr>
                        <td>{{ $certificate->number }}</td>
                        <td>{{ $certificate->holder_name }}</td>
                        <td>{{ $certificate->course_title }}</td>
                        <td>{{ $certificate->issued_on->format('j M Y') }}</td>
                        <td><span @class(['tag', 'tag--ok' => $certificate->status === 'valid', 'tag--bad' => $certificate->status !== 'valid'])>{{ $certificate->status === 'valid' ? 'Valid' : 'Withdrawn' }}</span></td>
                        <td class="actions">
                            <a href="{{ route('admin.certificates.edit', $certificate) }}">Edit</a>
                            <form method="post" action="{{ route('admin.certificates.destroy', $certificate) }}" onsubmit="return confirm('Delete this certificate?')">
                                @csrf @method('delete')
                                <button class="link-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No certificates yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $certificates->links('pagination::default') }}</div>
</div>
@endsection
