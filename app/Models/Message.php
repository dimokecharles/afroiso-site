<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One message in an enquiry's conversation. "out" is a reply from staff; "in" is a reply from the customer.
 * For "out", delivery records what happened to the email: sent, failed, or logged (email is not switched on).
 */
class Message extends Model
{
    protected $fillable = ['direction', 'user_id', 'body', 'delivery'];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOutgoing(): bool
    {
        return $this->direction === 'out';
    }
}
