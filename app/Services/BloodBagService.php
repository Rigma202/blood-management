<?php

namespace App\Services;

use App\Models\BloodBag;
use App\Models\BloodBank;

class BloodBagService
{
    public function getAll()
    {
        return BloodBag::with([
            'refrigerator',
            'refrigerator.bloodBank'
        ])->get();
    }

    public function getUserBloodBanksWithRefrigerators(int $userId)
    {
        return BloodBank::whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        })->with(['refrigerators' => function ($q) {
            $q->where('is_active', true);
        }])->get();
    }

    public function getRefrigeratorsForUserAndBank(int $userId, int $bloodBankId)
    {
        $bank = BloodBank::where('id', $bloodBankId)
            ->whereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })->first();

        if (! $bank) {
            return collect();
        }

        return $bank->refrigerators()->where('is_active', true)->get();
    }

    public function create(array $data)
    {
        return BloodBag::create([
            'refrigerator_id' => $data['refrigerator_id'],
            'bag_number'      => $data['bag_number'],
            'blood_group'     => $data['blood_group'],
            'donor_name'      => $data['donor_name'],
            'collection_date' => $data['collection_date'],
            'expiry_date'     => $data['expiry_date'],
            'quantity'        => $data['quantity'],
            'status'          => $data['status'],
            'is_tested'       => $data['is_tested'],
            'is_secure'       => $data['is_secure']
        ]);
    }

    public function update(BloodBag $bloodBag, array $data)
    {
        $bloodBag->update([
            'bag_number'      => $data['bag_number'],
            'blood_group'     => $data['blood_group'],
            'donor_name'      => $data['donor_name'],
            'collection_date' => $data['collection_date'],
            'expiry_date'     => $data['expiry_date'],
            'quantity'        => $data['quantity'],
            'status'          => $data['status'],
            'is_tested'       => $data['is_tested'] ?? false,
            'is_secure'       => $data['is_secure'] ?? false,
        ]);

        return $bloodBag->fresh(['refrigerator.bloodBank']);
    }

    public function delete(BloodBag $bloodBag)
    {
        return $bloodBag->delete();
    }

    public function findById($id)
    {
        return BloodBag::with([
            'refrigerator',
            'refrigerator.bloodBank'
        ])->findOrFail($id);
    }
}
