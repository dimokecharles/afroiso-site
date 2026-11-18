<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = ['type', 'title', 'description', 'price', 'cover_path', 'is_published', 'sort_order'];

    protected $casts = [
        'is_published' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order')->orderBy('title');
    }

    public function priceLabel(): string
    {
        return $this->price === null ? 'Price on request' : 'US$' . number_format((float) $this->price, 2);
    }

    public function typeLabel(): string
    {
        return $this->type === 'magazine' ? 'Magazine' : 'Book';
    }
}
