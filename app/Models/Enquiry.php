<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Enquiry extends Model
{
    /** Where a lead stands. An enquiry starts as "new" and ends as "converted" or "closed". */
    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'converted' => 'Converted', 'closed' => 'Closed'];

    protected $fillable = ['name', 'organisation', 'email', 'phone', 'interest', 'subject', 'message', 'is_read', 'status', 'assigned_to', 'follow_up_on', 'contact_id'];

    protected $casts = ['is_read' => 'boolean', 'follow_up_on' => 'date'];

    protected static function booted(): void
    {
        static::creating(function (Enquiry $enquiry) {
            $enquiry->status = $enquiry->status ?: 'new';
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
