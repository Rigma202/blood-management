<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureLog extends Model
{
    protected $fillable = [
        'refrigerator_id',
        'temperature',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    public function refrigerator()
    {
        return $this->belongsTo(Refrigerator::class);
    }
}
