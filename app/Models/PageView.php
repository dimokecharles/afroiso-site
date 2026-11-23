<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One visit to a public page. Holds no name, email or IP address: the visitor value is a one-day anonymous code. */
class PageView extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['path', 'referrer_host', 'visitor', 'viewed_on'];

    protected $casts = ['viewed_on' => 'date'];
}
