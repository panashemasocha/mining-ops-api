<?php

namespace App\Repositories;

use App\Models\Trip;

class TripRepository
{
    public function getAllTrips($perPage = 10)
    {
        return Trip::paginate($perPage, ['*'], 'trips_page');
    }

    public function getTripsForDriver($driverId, $perPage = 10)
    {
        return Trip::where('driver_id', $driverId)->paginate($perPage, ['*'], 'trips_page');
    }
}