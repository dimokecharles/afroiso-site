<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = ['name', 'organisation', 'email', 'phone', 'interest', 'subject', 'message', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];
}
