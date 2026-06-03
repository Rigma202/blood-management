<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodBankUser extends Model
{
    protected $fillable = [
        'user_id',
        'blood_bank_id'
    ];
}
