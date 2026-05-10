<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToDomain;

class UserInfo extends Model
{
    use BelongsToDomain;

    protected $table = 'users_info';

    public $timestamps = false;

    protected $fillable = [
        'domain_id',
        'user_id',
        'wallet',
        'credit_score',
        'withdrawal_code',
        'full_name',
        'id_card_number',
        'id_card_front',
        'id_card_back',
        'id_card_selfie',
        'email',
        'address',
        'signature',
        'company',
        'company_address',
        'position',
        'monthly_income',
        'contact_1_person',
        'contact_1_phone',
        'contact_1_relativity',
        'contact_2_person',
        'contact_2_phone',
        'contact_2_relativity',
        'bank_name',
        'bank_number',
    ];
}
