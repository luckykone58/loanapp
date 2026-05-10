<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = [
        'name',
        'host',
        'status',
        'expired_date',
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];
}


