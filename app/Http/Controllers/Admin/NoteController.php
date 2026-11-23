<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Enquiry;
use App\Models\Project;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /** Add a staff note to a contact, an enquiry or a consulting project. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'on' => ['required', 'in:contact,enquiry,project'],
            'id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        [$area, $model] = ['contact' => ['contacts', Contact::class], 'enquiry' => ['enquiries', Enquiry::class], 'project' => ['consulting', Project::class]][$data['on']];
        abort_unless($request->user()->canAccess($area), 403);

        $record = $model::findOrFail($data['id']);
        $record->notes()->create(['body' => $data['body'], 'user_id' => $request->user()->id]);

        return back()->with('status', 'Note added.');
    }
}
