<?php

namespace App\Services;

use App\Models\BloodBag;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use Illuminate\Support\Collection;

class DashboardService
{
    public function getTotalBloodBags(): int
    {
        return BloodBag::count();
    }

    public function getStockByBloodGroup(): Collection
    {
        return BloodBag::all()
            ->groupBy('blood_group')
            ->map(function ($bags, $bloodGroup) {
                return [
                    'blood_group' => $bloodGroup,
                    'quantity'    => (int) $bags->sum('quantity'),
                ];
            })
            ->values();
    }

    public function getRefrigeratorHealthScore(): float
    {
        $refrigerators = Refrigerator::where('is_active', true)
            ->with('temperatureLogs')
            ->get();

        if ($refrigerators->isEmpty()) {
            return 100.0;
        }

        $totalReadings = 0;
        $healthyReadings = 0;

        foreach ($refrigerators as $fridge) {
            $logs = $fridge->temperatureLogs()
                ->whereDate('recorded_at', today())
                ->get();

            $totalReadings += $logs->count();
            $healthyReadings += $logs->filter(fn($log) => $log->temperature >= 2 && $log->temperature <= 6)->count();
        }

        if ($totalReadings === 0) {
            return 100.0;
        }

        return round(($healthyReadings / $totalReadings) * 100, 2);
    }

    public function getCriticalTemperatureAlerts(): Collection
    {
        return TemperatureLog::whereHas('refrigerator', fn($q) => $q->where('is_active', true))
            ->where('temperature', '>', 8.0)
            ->whereDate('recorded_at', today())
            ->with('refrigerator')
            ->latest('recorded_at')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'refrigerator_name' => $log->refrigerator->name,
                'temperature' => $log->temperature,
                'recorded_at' => $log->recorded_at->format('H:i:s'),
                'status' => 'Critical',
            ]);
    }

    public function getAverageTemperatureToday(): ?float
    {
        $average = TemperatureLog::whereDate('recorded_at', today())
            ->avg('temperature');

        return $average ? round($average, 2) : null;
    }

    public function getTotalExpiredBags(): int
    {
        return BloodBag::whereDate('expiry_date', '<', today())->count();
    }

    public function getActiveRefrigerators(): int
    {
        return Refrigerator::where('is_active', true)->count();
    }

    public function getDashboardSummary(): array
    {
        return [
            'total_bags' => $this->getTotalBloodBags(),
            'stock_by_group' => $this->getStockByBloodGroup(),
            'health_score' => $this->getRefrigeratorHealthScore(),
            'critical_alerts' => $this->getCriticalTemperatureAlerts(),
            'avg_temp_today' => $this->getAverageTemperatureToday(),
            'expired_bags' => $this->getTotalExpiredBags(),
            'active_fridges' => $this->getActiveRefrigerators(),
        ];
    }
}