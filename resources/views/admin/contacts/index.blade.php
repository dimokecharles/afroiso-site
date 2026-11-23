@extends('layouts.admin')
@section('title', 'Contacts')

@section('content')
<div class="head">
    <h1>Contacts</h1>
    <a class="btn" href="{{ route('admin.contacts.create') }}">Add a contact</a>
</div>
<div class="panel">
    <form method="get" action="{{ route('admin.contacts.index') }}" class="filters">
        <div class="field">
            <label for="q">Search by name, email, organisation or phone</label>
            <input id="q" name="q" type="text" value="{{ $search }}">
        </div>
        <button class="btn btn--plain" type="submit">Search</button>
        @if ($search !== '')
            <a class="btn btn--plain" href="{{ route('admin.contacts.index') }}">Clear</a>
        @endif
    </form>
</div>
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Organisation</th><th>Email</th><th>Phone</th><th>Enquiries</th><th>Orders</th><th>Paid</th><th><span class="vh">Actions</span></th></tr></thead>
            <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <td><a href="{{ route('admin.contacts.show', $contact) }}">{{ $contact->name }}</a></td>
                        <td>{{ $contact->organisation }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->phone }}</td>
                        <td>{{ $contact->enquiries_count }}</td>
                        <td>{{ $contact->orders_count }}</td>
                        <td>US${{ number_format((float) $contact->paid_total, 2) }}</td>
                        <td class="actions"><a href="{{ route('admin.contacts.show', $contact) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8">{{ $search !== '' ? 'No contacts match that search.' : 'No contacts yet. They are added automatically when someone sends an enquiry, enrols or buys.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $contacts->links('admin._pagination') }}</div>
</div>
@endsection
