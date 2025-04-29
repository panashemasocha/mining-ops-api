<?php

namespace App\Repositories;

use App\Models\Trip;

class TripRepository
{
    /**
     * Fetch all trips created between the given start and end dates,
     * sorted by newest first.
     *
     * @param  string  $startDate  ISO date string for the range start
     * @param  string  $endDate    ISO date string for the range end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrips(string $startDate, string $endDate)
    {
        return Trip::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Fetch trips for a specific driver created between the given start and end dates,
     * sorted by newest first.
     *
     * @param  int     $driverId   ID of the driver
     * @param  string  $startDate  ISO date string for the range start
     * @param  string  $endDate    ISO date string for the range end
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsForDriver(int $driverId, string $startDate, string $endDate)
    {
        return Trip::where('driver_id', $driverId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
