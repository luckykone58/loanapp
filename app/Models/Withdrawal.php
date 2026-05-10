<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToDomain;

class Withdrawal extends Model
{
    use BelongsToDomain;

    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'user_id',
        'amount',
        'status',
        'withdraw_name',
        'withdraw_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
