<?php
namespace App\Repositories;

use App\Models\Trip;

class TripRepository
{
    /**
     * Fetch all trips created on or between the given dates
     * sorted by newest first.
     *
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate    YYYY-MM-DD
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrips(string $startDate, string $endDate)
    {
        return Trip::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Fetch trips for a specific driver created on or between the given dates 
     * sorted by newest first.
     *
     * @param  int     $driverId   ID of the driver
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate    YYYY-MM-DD
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsForDriver(int $driverId, string $startDate, string $endDate)
    {
        return Trip::where('driver_id', $driverId)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
