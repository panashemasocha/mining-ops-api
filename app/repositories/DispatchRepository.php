<?php

namespace App\Repositories;

use App\Models\Dispatch;

class DispatchRepository
{
    public function getAllDispatches()
    {
        return Dispatch::all();
    }

    public function getDispatchesForDriver($driverId)
    {
        return Dispatch::whereHas('vehicle.assignedDrivers', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })->get();
    }
}