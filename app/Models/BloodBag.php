<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodBag extends Model
{
    protected $fillable = [
        'created_by',
        'refrigerator_id',
        'bag_number',
        'blood_group',
        'donor_name',
        'collection_date',
        'expiry_date',
        'quantity',
        'status',
        'is_tested',
        'is_secure'
    ];

    protected $casts = [
        'collection_date' => 'date',
        'expiry_date' => 'date',
        'is_tested' => 'boolean',
        'is_secure' => 'boolean'
    ];

    public function refrigerator()
    {
        return $this->belongsTo(Refrigerator::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
