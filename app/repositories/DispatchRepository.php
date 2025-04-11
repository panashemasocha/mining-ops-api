<?php

namespace App\Repositories;

use App\Http\Resources\DispatchResource;
use App\Models\Dispatch;

class DispatchRepository
{
    public function getAllDispatches()
    {
        return DispatchResource::collection(Dispatch::paginate(10));
    }

    public function getDispatchesForDriver($driverId)
    {
        return Dispatch::whereHas('vehicle.assignedDrivers', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })->get();
    }
}