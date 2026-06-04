<?php

namespace App\Services;

use App\Models\Refrigerator;
use Illuminate\Support\Collection;

class BloodExpiryService
{
    public function expiringWithin24Hours(Refrigerator $refrigerator): Collection
    {
        return $refrigerator->bloodBags()
            ->whereBetween('expiry_date', [now(), now()->addDay()])
            ->orderBy('expiry_date')
            ->get();
    }

    public function alreadyExpired(Refrigerator $refrigerator): Collection
    {
        return $refrigerator->bloodBags()
            ->whereDate('expiry_date', '<', today())
            ->orderByDesc('expiry_date')
            ->get();
    }

    public function nearRiskPercentage(Refrigerator $refrigerator): float
    {
        $total = $refrigerator->bloodBags()->count();
        if ($total === 0) {
            return 0;
        }
        $expiring = $this->expiringWithin24Hours($refrigerator)->count();
        return round($expiring * 100 / $total, 2);
    }

    public function summary(Refrigerator $refrigerator): array
    {
        return [
            'expiring_within_24' => $this->expiringWithin24Hours($refrigerator),
            'already_expired'    => $this->alreadyExpired($refrigerator),
            'near_risk_pct'      => $this->nearRiskPercentage($refrigerator),
            'total_bags'         => $refrigerator->bloodBags()->count(),
        ];
    }
}