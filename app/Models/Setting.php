<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToDomain;

// --- Setting Model ---
// This model is used by the AppServiceProvider to dynamically set the theme path.
class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    use BelongsToDomain;
    protected $fillable = ['domain_id', 'name', 'value'];
}