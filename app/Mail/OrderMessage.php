<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * One email layout for the three order messages: "placed" (to the office), "receipt" and "paylink" (to the customer).
 */
class OrderMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $kind)
    {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->kind) {
            'placed' => 'New ' . strtolower($this->order->typeLabel()) . ': ' . $this->order->reference,
            'receipt' => 'Payment received: ' . $this->order->reference,
            default => 'Your invoice from ' . config('afroiso.name') . ': ' . $this->order->reference,
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order');
    }
}
