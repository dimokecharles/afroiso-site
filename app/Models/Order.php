<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Something a customer pays for: a course enrolment, a publication order or a consulting invoice.
 * Each order has a private pay link, /pay/{token}.
 */
class Order extends Model
{
    public const TYPES = ['enrolment' => 'Course enrolment', 'publication' => 'Publication order', 'invoice' => 'Consulting invoice'];

    public const STATUSES = ['pending' => 'Awaiting payment', 'paid' => 'Paid', 'failed' => 'Payment failed', 'cancelled' => 'Cancelled'];

    protected $fillable = [
        'type', 'course_id', 'publication_id', 'description', 'customer_name', 'customer_email', 'customer_phone',
        'organisation', 'amount', 'currency', 'status', 'gateway', 'gateway_reference', 'paid_at', 'notes', 'contact_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->token = $order->token ?: Str::random(48);
            $order->currency = $order->currency ?: 'USD';
            $order->status = $order->status ?: 'pending';
            $order->contact_id ??= Contact::sync($order->customer_name, $order->customer_email, $order->customer_phone, $order->organisation, 'order')?->id;
        });

        static::created(function (Order $order) {
            $order->forceFill(['reference' => 'AFR-' . $order->created_at->format('Y') . '-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT)])->saveQuietly();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['pending', 'failed'], true);
    }

    public function markPaid(string $gateway, ?string $gatewayReference = null): void
    {
        $this->update(['status' => 'paid', 'gateway' => $gateway, 'gateway_reference' => $gatewayReference, 'paid_at' => now()]);
    }

    public function amountLabel(): string
    {
        return ($this->currency === 'USD' ? 'US$' : $this->currency . ' ') . number_format((float) $this->amount, 2);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function payUrl(): string
    {
        return route('pay.show', ['order' => $this->token]);
    }
}
