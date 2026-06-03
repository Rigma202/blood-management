<?php
namespace App\Services;

use App\Models\Refrigerator;

class RefrigeratorService
{
    public function getAll()
    {
        return Refrigerator::with('bloodBank')
            ->paginate(10);
    }

    public function create(array $data)
    {
        return Refrigerator::create($data);
    }

    public function update(
        Refrigerator $refrigerator,
        array $data
    ) {
        $refrigerator->update($data);

        return $refrigerator;
    }

    public function delete(
        Refrigerator $refrigerator
    ) {
        return $refrigerator->delete();
    }
}
