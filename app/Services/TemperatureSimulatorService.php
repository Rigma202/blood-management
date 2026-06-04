<?php

namespace App\Services;

use App\Models\Refrigerator;
use App\Models\TemperatureLog;

class TemperatureSimulatorService
{

    public function generateTemperature(int $refrigeratorId): float
    {
        $last = TemperatureLog::where('refrigerator_id', $refrigeratorId)
            ->latest('recorded_at')
            ->value('temperature');

        if ($last === null) {
            return round(mt_rand(30, 50) / 10, 1); 
        }
        $drift = round((mt_rand(-3, 5) / 10), 1);
        $next  = round($last + $drift, 1);
        return max(1.0, min(12.0, $next));
    }
}