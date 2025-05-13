<?php

namespace App\Repositories;

use App\Models\Dispatch;

class DispatchRepository
{
    /**
     * Fetch all dispatches created on or between the given dates 
     * sorted by newest first.
     *
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate    YYYY-MM-DD
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDispatches(string $startDate, string $endDate)
    {
        return Dispatch::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Fetch dispatches for a specific driver created on or between the given dates 
     * based on associated trips, sorted by newest first.
     *
     * @param  int     $driverId   ID of the driver
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate    YYYY-MM-DD
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDispatchesForDriver(int $driverId, string $startDate, string $endDate)
    {
        return Dispatch::whereHas('trips', function ($query) use ($driverId) {
            $query->where('driver_id', $driverId);
        })
            // ->whereDate('created_at', '>=', $startDate)
            // ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}