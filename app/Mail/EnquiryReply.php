<?php

namespace App\Mail;

use App\Models\Enquiry;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** A reply to an enquiry, written by staff in the message box. The customer's answer goes to the office address. */
class EnquiryReply extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry, public Message $reply)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . ($this->enquiry->subject ?: 'Your enquiry to ' . config('afroiso.name')),
            replyTo: [new Address(config('afroiso.office_email'), config('afroiso.name'))],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reply');
    }
}
