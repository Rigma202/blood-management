<?php

namespace App\Services;

use App\Models\BloodBank;

class BloodBankService
{
    public function getAll()
    {
        return BloodBank::paginate(10);
    }

    public function create(array $data)
    {
        return BloodBank::create($data);
    }

    public function update(BloodBank $bloodBank, array $data)
    {
        $bloodBank->update($data);

        return $bloodBank;
    }

    public function delete(BloodBank $bloodBank)
    {
        return $bloodBank->delete();
    }
}
