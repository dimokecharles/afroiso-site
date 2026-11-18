<div class="table-wrap">
    <table>
        <thead><tr><th>Received</th><th>Name</th><th>Regarding</th><th>Email</th><th><span style="position:absolute;left:-9999px">Actions</span></th></tr></thead>
        <tbody>
            @forelse ($enquiries as $enquiry)
                <tr @class(['unread' => ! $enquiry->is_read])>
                    <td>{{ $enquiry->created_at->format('j M Y, H:i') }}</td>
                    <td>{{ $enquiry->name }}</td>
                    <td>{{ $enquiry->subject ?: $enquiry->interest ?: 'General' }}</td>
                    <td>{{ $enquiry->email }}</td>
                    <td class="actions"><a href="{{ route('admin.enquiries.show', $enquiry) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="5">No enquiries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
