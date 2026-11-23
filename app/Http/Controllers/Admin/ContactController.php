<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $contacts = Contact::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $query->where(fn ($inner) => $inner->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('organisation', 'like', $like)->orWhere('phone', 'like', $like));
            })
            ->withCount(['enquiries', 'orders'])
            ->withSum(['orders as paid_total' => fn ($query) => $query->where('status', 'paid')], 'amount')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.contacts.index', ['contacts' => $contacts, 'search' => $search]);
    }

    public function create()
    {
        return view('admin.contacts.form', ['contact' => new Contact()]);
    }

    public function store(Request $request)
    {
        $contact = Contact::create($this->validated($request) + ['source' => 'manual']);

        return redirect()->route('admin.contacts.show', $contact)->with('status', 'Contact added.');
    }

    public function show(Contact $contact)
    {
        $contact->load(['enquiries' => fn ($query) => $query->latest(), 'orders' => fn ($query) => $query->latest(), 'notes.user']);

        // One history, newest first: every enquiry and every order this person has made.
        $history = $contact->enquiries->map(fn ($enquiry) => [
            'when' => $enquiry->created_at,
            'icon' => 'fa-inbox',
            'title' => 'Enquiry: ' . ($enquiry->subject ?: $enquiry->interest ?: 'General'),
            'detail' => $enquiry->statusLabel(),
            'url' => route('admin.enquiries.show', $enquiry),
        ])->concat($contact->orders->map(fn ($order) => [
            'when' => $order->created_at,
            'icon' => $order->type === 'enrolment' ? 'fa-user-graduate' : ($order->type === 'publication' ? 'fa-book-open' : 'fa-file-invoice-dollar'),
            'title' => $order->typeLabel() . ': ' . $order->description,
            'detail' => $order->amountLabel() . ', ' . strtolower($order->statusLabel()),
            'url' => route('admin.orders.show', $order),
        ]))->sortByDesc('when')->values();

        return view('admin.contacts.show', [
            'contact' => $contact,
            'history' => $history,
            'paidTotal' => (float) $contact->orders->where('status', 'paid')->sum('amount'),
        ]);
    }

    public function edit(Contact $contact)
    {
        return view('admin.contacts.form', ['contact' => $contact]);
    }

    public function update(Request $request, Contact $contact)
    {
        $contact->update($this->validated($request, $contact));

        return redirect()->route('admin.contacts.show', $contact)->with('status', 'Contact saved.');
    }

    private function validated(Request $request, ?Contact $contact = null): array
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email'))) ?: null]);

        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:160', Rule::unique('contacts', 'email')->ignore($contact?->id)],
            'phone' => ['nullable', 'string', 'max:60'],
            'organisation' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', 'string', 'max:80'],
        ]);
    }
}
