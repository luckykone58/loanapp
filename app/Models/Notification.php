<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToDomain;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use BelongsToDomain;

    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'user_id',
        'title',
        'message',
        'subtext',
        'notes',
        'type',
        'status',
        'created_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
