@extends('layouts.admin')
@section('title', 'Staff logins')

@section('content')
<div class="head">
    <h1>Staff logins</h1>
    <a class="btn" href="{{ route('admin.staff.create') }}">Add a staff login</a>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Login</th><th><span class="vh">Actions</span></th></tr></thead>
            <tbody>
                @foreach ($staff as $person)
                    <tr>
                        <td>{{ $person->name }}{{ $person->is(auth()->user()) ? ' (you)' : '' }}</td>
                        <td>{{ $person->email }}</td>
                        <td>{{ $person->roleLabel() }}</td>
                        <td><span @class(['tag', 'tag--ok' => $person->is_active, 'tag--off' => ! $person->is_active])>{{ $person->is_active ? 'Active' : 'Switched off' }}</span></td>
                        <td class="actions"><a href="{{ route('admin.staff.edit', $person) }}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="panel">
    <h2>What each role can open</h2>
    <ul class="role-list">
        <li><strong>Administrator:</strong> everything, including staff logins.</li>
        <li><strong>Finance:</strong> dashboard, contacts, enrolments, payments and consulting projects.</li>
        <li><strong>Sales:</strong> dashboard, contacts, enquiries and leads, enrolments and consulting projects. No payments or invoices.</li>
        <li><strong>Content editor:</strong> courses, publications and certificates.</li>
    </ul>
</div>
@endsection
