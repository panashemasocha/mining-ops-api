<?php

namespace App\Repositories;

use App\Models\Trip;

class TripRepository
{
    public function getAllTrips()
    {
        return Trip::all();
    }

    public function getTripsForDriver($driverId)
    {
        return Trip::where('driver_id', $driverId)->get();
    }
}