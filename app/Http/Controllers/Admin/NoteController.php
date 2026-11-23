<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /** Add a staff note to a contact or an enquiry. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'on' => ['required', 'in:contact,enquiry'],
            'id' => ['required', 'integer'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $record = $data['on'] === 'contact' ? Contact::findOrFail($data['id']) : Enquiry::findOrFail($data['id']);
        $record->notes()->create(['body' => $data['body'], 'user_id' => $request->user()->id]);

        return back()->with('status', 'Note added.');
    }
}
