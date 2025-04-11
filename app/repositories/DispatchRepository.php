<?php

namespace App\Repositories;

use App\Models\Dispatch;

class DispatchRepository
{
    public function getAllDispatches($perPage = 10)
    {
        return Dispatch::paginate($perPage, ['*'], 'dispatches_page');
    }

    public function getDispatchesForDriver($driverId, $perPage = 10)
    {
        return Dispatch::whereHas('vehicle.assignedDrivers', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })->paginate($perPage, ['*'], 'dispatches_page');
    }
}