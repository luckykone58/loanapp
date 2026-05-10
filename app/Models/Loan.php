<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToDomain;

class Loan extends Model
{
    use BelongsToDomain;

    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'user_id',
        'loan_number',
        'amount',
        'start_date',
        'period',
        'interest',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'amount' => 'decimal:2',
        'interest' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
