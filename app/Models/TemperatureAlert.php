<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureAlert extends Model
{
    protected $fillable = [
        'refrigerator_id',
        'temperature',
        'recorded_at',
        'notified',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'temperature' => 'float',
        'notified' => 'boolean',
    ];

    public function refrigerator()
    {
        return $this->belongsTo(Refrigerator::class);
    }
}