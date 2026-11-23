<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $status = array_key_exists((string) $request->query('status'), Enquiry::STATUSES) ? $request->query('status') : null;

        $enquiries = Enquiry::with('owner')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'status' => $status,
            'counts' => Enquiry::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Enquiry $enquiry)
    {
        if (! $enquiry->is_read) {
            $enquiry->update(['is_read' => true]);
        }

        return view('admin.enquiries.show', [
            'enquiry' => $enquiry->load(['contact', 'owner', 'notes.user']),
            'staff' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /** Move the lead along: status, owner and follow-up date. */
    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Enquiry::STATUSES))],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_on' => ['nullable', 'date'],
        ]);

        $enquiry->update($data);

        return back()->with('status', 'Lead updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('status', 'Enquiry deleted.');
    }
}
