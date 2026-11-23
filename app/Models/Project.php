<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/** A consulting engagement for one client. The stages follow the four steps shown on the public Consulting page. */
class Project extends Model
{
    public const STAGES = [
        'proposal' => 'Proposal',
        'gap-analysis' => 'Gap analysis',
        'building' => 'Building the system',
        'internal-audit' => 'Internal audits',
        'readiness' => 'Readiness check',
        'completed' => 'Completed',
        'on-hold' => 'On hold',
    ];

    /** The stages a project moves through in order. "On hold" sits outside the sequence. */
    public const PATH = ['proposal', 'gap-analysis', 'building', 'internal-audit', 'readiness', 'completed'];

    protected $fillable = ['contact_id', 'title', 'standard', 'stage', 'value', 'start_date', 'target_date', 'assigned_to', 'description'];

    protected $casts = ['value' => 'decimal:2', 'start_date' => 'date', 'target_date' => 'date'];

    protected $attributes = ['stage' => 'proposal'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->stage] ?? ucfirst((string) $this->stage);
    }

    public function isActive(): bool
    {
        return ! in_array($this->stage, ['completed', 'on-hold'], true);
    }

    public function isOverdue(): bool
    {
        return $this->isActive() && $this->target_date !== null && $this->target_date->isPast() && ! $this->target_date->isToday();
    }

    public function valueLabel(): string
    {
        return $this->value === null ? 'Not set' : 'US$' . number_format((float) $this->value, 2);
    }
}
