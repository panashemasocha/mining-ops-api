<?php

namespace App\Repositories;

use App\Models\Dispatch;

class DispatchRepository
{
    /**
     * Fetch all dispatches created between the given start and end dates,
     * sorted by newest first.
     *
     * @param  string  $startDate  ISO date string for the range start
     * @param  string  $endDate    ISO date string for the range end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDispatches(string $startDate, string $endDate)
    {
        return Dispatch::whereBetween('date_created', [$startDate, $endDate])
            ->orderBy('date_created', 'desc')
            ->get();
    }

    /**
     * Fetch dispatches for a specific driver created between the given start and end dates,
     * sorted by newest first.
     *
     * @param  int     $driverId   ID of the driver
     * @param  string  $startDate  ISO date string for the range start
     * @param  string  $endDate    ISO date string for the range end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDispatchesForDriver(int $driverId, string $startDate, string $endDate)
    {
        return Dispatch::whereHas('vehicle.assignedDrivers', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })
            ->whereBetween('date_created', [$startDate, $endDate])
            ->orderBy('date_created', 'desc')
            ->get();
    }
}
