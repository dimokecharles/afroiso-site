<div class="table-wrap">
    <table @class(['table--compact' => $compact ?? false])>
        <thead><tr><th>Received</th><th>Name</th><th>Regarding</th>@unless ($compact ?? false)<th>Owner</th><th>Follow up</th>@endunless<th>Status</th><th><span class="vh">Actions</span></th></tr></thead>
        <tbody>
            @forelse ($enquiries as $enquiry)
                <tr @class(['unread' => ! $enquiry->is_read])>
                    <td>{{ $enquiry->created_at->format('j M Y, H:i') }}</td>
                    <td>{{ $enquiry->name }}</td>
                    <td>{{ $enquiry->subject ?: $enquiry->interest ?: 'General' }}</td>
                    @unless ($compact ?? false)
                        <td>{{ $enquiry->owner?->name ?? 'Unassigned' }}</td>
                        <td>{{ $enquiry->follow_up_on?->format('j M Y') ?? 'None' }}</td>
                    @endunless
                    <td><span @class(['tag', 'tag--new' => $enquiry->status === 'new', 'tag--ok' => $enquiry->status === 'converted', 'tag--bad' => $enquiry->status === 'closed'])>{{ $enquiry->statusLabel() }}</span></td>
                    <td class="actions"><a href="{{ route('admin.enquiries.show', $enquiry) }}">Open</a></td>
                </tr>
            @empty
                <tr><td colspan="{{ ($compact ?? false) ? 5 : 7 }}">No enquiries yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
