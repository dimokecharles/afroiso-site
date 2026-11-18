@extends('layouts.admin')
@section('title', 'Courses')

@section('content')
<div class="head">
    <h1>Courses</h1>
    <a class="btn" href="{{ route('admin.courses.create') }}">Add a course</a>
</div>
<div class="panel table-wrap">
    <table>
        <thead><tr><th>Course</th><th>Start date</th><th>Fee</th><th>Status</th><th><span style="position:absolute;left:-9999px">Actions</span></th></tr></thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->startLabel() }}</td>
                    <td>{{ $course->feeLabel() }}</td>
                    <td><span @class(['tag', 'tag--ok' => $course->is_published])>{{ $course->is_published ? 'Published' : 'Hidden' }}</span></td>
                    <td class="actions">
                        <a href="{{ route('admin.courses.edit', $course) }}">Edit</a>
                        <form method="post" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                            @csrf @method('delete')
                            <button class="link-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No courses yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
