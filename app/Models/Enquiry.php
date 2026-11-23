<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Enquiry extends Model
{
    /** Where a lead stands. An enquiry starts as "new" and ends as "converted" or "closed". */
    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'converted' => 'Converted', 'closed' => 'Closed'];

    protected $fillable = ['name', 'organisation', 'email', 'phone', 'interest', 'subject', 'message', 'is_read', 'status', 'assigned_to', 'follow_up_on', 'contact_id', 'awaiting_reply', 'last_message_at'];

    protected $casts = ['is_read' => 'boolean', 'follow_up_on' => 'date', 'awaiting_reply' => 'boolean', 'last_message_at' => 'datetime'];

    protected $attributes = ['awaiting_reply' => true];

    protected static function booted(): void
    {
        static::creating(function (Enquiry $enquiry) {
            $enquiry->status = $enquiry->status ?: 'new';
            $enquiry->last_message_at ??= now();
            $enquiry->contact_id ??= Contact::sync($enquiry->name, $enquiry->email, $enquiry->phone, $enquiry->organisation, 'enquiry')?->id;
        });
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** The conversation after the enquiry itself: staff replies and customer replies, oldest first. */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, ['converted', 'closed'], true);
    }
}
