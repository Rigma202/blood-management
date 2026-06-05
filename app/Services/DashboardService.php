<?php

namespace App\Services;

use App\Models\BloodBag;
use App\Models\Refrigerator;
use App\Models\TemperatureLog;
use Illuminate\Support\Collection;
use App\Models\BloodBank;
use App\Models\User;

class DashboardService
{
    public function getTotalBloodBags(): int
    {
        $userId = auth()->id();

        return BloodBag::whereHas('refrigerator.bloodBank.users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->count();
    }

    public function getStockByBloodGroup(): Collection
    {
        $userId = auth()->id();

        return BloodBag::query()
            ->whereHas('refrigerator.bloodBank.users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->selectRaw('blood_group, SUM(quantity) as quantity')
            ->groupBy('blood_group')
            ->get();
    }

    public function getRefrigeratorHealthScore(): float
    {
        $userId = auth()->id();

        $refrigerators = Refrigerator::where('is_active', true)
            ->whereHas('bloodBank.users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->with('temperatureLogs')
            ->get();
        if ($refrigerators->isEmpty()) {
            return 0.0;
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
        $userId = auth()->id();

            return TemperatureLog::query()
                ->where('temperature', '>', 8.0)
                ->whereDate('recorded_at', today())
                ->whereHas('refrigerator', function ($query) use ($userId) {
                    $query->where('is_active', true)
                        ->whereHas('bloodBank.users', function ($q) use ($userId) {
                            $q->where('users.id', $userId);
                        });
                })
                ->with('refrigerator:id,name')
                ->latest('recorded_at')
                ->limit(10)
                ->get()
                ->map(fn ($log) => [
                    'refrigerator_name' => $log->refrigerator->name,
                    'temperature'       => $log->temperature,
                    'recorded_at'       => $log->recorded_at->format('H:i:s'),
                    'status'            => 'Critical',
                ]);
    }

    public function getAverageTemperatureToday(): ?float
    {
    $userId = auth()->id();

    $average = TemperatureLog::query()
        ->whereDate('recorded_at', today())
        ->whereHas('refrigerator.bloodBank.users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->avg('temperature');

    return $average ? round($average, 2) : null;
    }

    public function getTotalExpiredBags(): int
    {
            $userId = auth()->id();

            return BloodBag::query()
                ->whereDate('expiry_date', '<', today())
                ->whereHas('refrigerator.bloodBank.users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->count();
    }

    public function getActiveRefrigerators(): int
    {
            $userId = auth()->id();

            return Refrigerator::query()
                ->where('is_active', true)
                ->whereHas('bloodBank.users', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->count();
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

    public function getAdminDashboardSummary(): array
    {
        return [
            'total_blood_banks'            => $this->getTotalBloodBanks(),
            'banks_with_active_fridges'    => $this->getBanksWithActiveRefrigerators(),
            'bank_with_max_refrigerators'  => $this->getBankWithMaximumRefrigerators(),
            'total_staff_count'            => $this->getTotalStaffCount(),
        ];
    }

    public function getTotalBloodBanks(): int
    {
        return BloodBank::count();
    }

    public function getBanksWithActiveRefrigerators(): int
    {
        return BloodBank::whereHas('refrigerators', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    public function getBankWithMaximumRefrigerators(): ?array
    {
        $bank = BloodBank::withCount('refrigerators')
            ->orderByDesc('refrigerators_count')
            ->first();

        if (!$bank) {
            return null;
        }

        return [
            'name' => $bank->name,
            'refrigerator_count' => $bank->refrigerators_count,
        ];
    }

    public function getTotalStaffCount(): int
    {
        return User::where('role', 'staff')->count();
    }

}
