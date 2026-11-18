<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['number', 'holder_name', 'course_title', 'issued_on', 'status'];

    protected $casts = ['issued_on' => 'date'];
}
