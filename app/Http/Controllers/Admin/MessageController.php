<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EnquiryReply;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /** The message box: every conversation, the ones waiting for a reply first. */
    public function index(Request $request)
    {
        $show = in_array($request->query('show'), ['waiting', 'all'], true) ? $request->query('show') : 'waiting';

        $conversations = Enquiry::with('owner')
            ->withCount('messages')
            ->when($show === 'waiting', fn ($query) => $query->where('awaiting_reply', true))
            ->orderByDesc('awaiting_reply')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return view('admin.messages.index', [
            'conversations' => $conversations,
            'show' => $show,
            'waitingCount' => Enquiry::where('awaiting_reply', true)->count(),
            'allCount' => Enquiry::count(),
        ]);
    }

    /** Send a reply by email and keep it in the conversation. */
    public function reply(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);

        $message = $enquiry->messages()->create(['direction' => 'out', 'user_id' => $request->user()->id, 'body' => $data['body']]);
        $message->update(['delivery' => $this->deliver($enquiry, $message)]);

        $enquiry->update([
            'awaiting_reply' => false,
            'last_message_at' => now(),
            'is_read' => true,
            'status' => $enquiry->status === 'new' ? 'contacted' : $enquiry->status,
        ]);

        return back()->with('status', match ($message->delivery) {
            'sent' => 'Reply sent to ' . $enquiry->email . '.',
            'logged' => 'Reply saved, but not emailed: email sending is not switched on for this site yet.',
            default => 'Reply saved, but the email could not be sent. Check the email settings, or contact the customer another way.',
        });
    }

    /** Record a reply the customer sent back by email, phone or WhatsApp, so the conversation stays complete. */
    public function received(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);

        $enquiry->messages()->create(['direction' => 'in', 'user_id' => $request->user()->id, 'body' => $data['body']]);
        $enquiry->update(['awaiting_reply' => true, 'last_message_at' => now()]);

        return back()->with('status', 'Customer reply recorded.');
    }

    /** Mark a conversation as needing no reply, without sending anything. */
    public function done(Enquiry $enquiry)
    {
        $enquiry->update(['awaiting_reply' => false]);

        return back()->with('status', 'Marked as no reply needed.');
    }

    private function deliver(Enquiry $enquiry, $message): string
    {
        // With the "log" or "array" mailer nothing leaves the server, so say so instead of claiming it was sent.
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            try {
                Mail::to($enquiry->email)->send(new EnquiryReply($enquiry, $message));
            } catch (\Throwable $e) {
                Log::warning('Reply could not be written to the mail log: ' . $e->getMessage());
            }

            return 'logged';
        }

        try {
            Mail::to($enquiry->email)->send(new EnquiryReply($enquiry, $message));

            return 'sent';
        } catch (\Throwable $e) {
            Log::warning('Reply email could not be sent: ' . $e->getMessage());

            return 'failed';
        }
    }
}
