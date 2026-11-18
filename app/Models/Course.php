<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'slug', 'code', 'category', 'summary', 'description',
        'fee', 'delivery', 'start_date', 'is_published', 'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_published' => 'boolean',
        'fee' => 'decimal:2',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderBy('title');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function feeLabel(): string
    {
        if ($this->fee === null) {
            return 'On request';
        }

        return 'US$' . rtrim(rtrim(number_format((float) $this->fee, 2), '0'), '.');
    }

    public function startLabel(): string
    {
        return $this->start_date ? $this->start_date->format('j F Y') : 'To be announced';
    }
}
