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

    /**
     * The full description as safe HTML. The text is plain: a line starting with "## " is a heading,
     * lines starting with "- " form a bulleted list, and everything else is a paragraph.
     */
    public function descriptionHtml(): string
    {
        $html = '';
        $blocks = preg_split('/\R\s*\R/', trim(str_replace("\r\n", "\n", (string) $this->description)));

        foreach ($blocks as $block) {
            $paragraph = [];
            $items = [];
            $flush = function () use (&$html, &$paragraph, &$items) {
                if ($paragraph) {
                    $html .= '<p>' . implode('<br>', $paragraph) . '</p>';
                    $paragraph = [];
                }
                if ($items) {
                    $html .= '<ul><li>' . implode('</li><li>', $items) . '</li></ul>';
                    $items = [];
                }
            };

            foreach (explode("\n", $block) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                if (str_starts_with($line, '## ')) {
                    $flush();
                    $html .= '<h2>' . e(substr($line, 3)) . '</h2>';
                } elseif (str_starts_with($line, '- ')) {
                    if ($paragraph) {
                        $flush();
                    }
                    $items[] = e(substr($line, 2));
                } else {
                    if ($items) {
                        $flush();
                    }
                    $paragraph[] = e($line);
                }
            }
            $flush();
        }

        return $html;
    }

    public function startLabel(): string
    {
        return $this->start_date ? $this->start_date->format('j F Y') : 'To be announced';
    }
}
