<?php

// app/Services/TemperatureService.php

namespace App\Services;

use App\Models\TemperatureLog;
use Carbon\Carbon;

class TemperatureService
{
 
    public function log(
        int $refrigeratorId,
        float $temperature,
        ?string $recordedAt = null
    ): TemperatureLog {
        return TemperatureLog::create([
            'refrigerator_id' => $refrigeratorId,
            'temperature'     => $temperature,
            'recorded_at'     => $recordedAt ?? now(),
        ]);
    }


    public function dailyAnalysis(int $refrigeratorId, ?string $date = null): array
    {
        $date = $date ? Carbon::parse($date) : today();

        $logs = TemperatureLog::where('refrigerator_id', $refrigeratorId)
            ->whereDate('recorded_at', $date)
            ->get();

        if ($logs->isEmpty()) {
            return $this->emptyAnalysis($date);
        }

        $temperatures  = $logs->pluck('temperature');
        $totalMinutes  = $logs->count();
        $unsafeMinutes = $logs->filter(fn($l) => $l->temperature > 6.0)->count();

        return [
            'date'            => $date->toDateString(),
            'total_readings'  => $totalMinutes,
            'average_temp'    => round($temperatures->avg(), 2),
            'highest_temp'    => $temperatures->max(),
            'lowest_temp'     => $temperatures->min(),
            'unsafe_minutes'  => $unsafeMinutes,
            'risk_percentage' => round(($unsafeMinutes / $totalMinutes) * 100, 2),
        ];
    }

    public function isCriticalFor10Minutes(int $refrigeratorId): bool
    {
        $recentLogs = TemperatureLog::where('refrigerator_id', $refrigeratorId)
            ->orderByDesc('recorded_at')
            ->limit(10)
            ->get();

        if ($recentLogs->count() < 3) {
            return false;
        }

        return $recentLogs->every(fn($log) => $log->temperature > 8.0);
    }

    public function getStatus(float $temperature): string
    {
        return match(true) {
            $temperature <= 6.0 => 'safe',
            $temperature <= 8.0 => 'warning',
            default             => 'critical',
        };
    }


    private function emptyAnalysis(Carbon $date): array
    {
        return [
            'date'            => $date->toDateString(),
            'total_readings'  => 0,
            'average_temp'    => null,
            'highest_temp'    => null,
            'lowest_temp'     => null,
            'unsafe_minutes'  => 0,
            'risk_percentage' => 0,
        ];
    }
}