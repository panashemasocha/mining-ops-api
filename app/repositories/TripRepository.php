<?php

namespace App\Repositories;

use App\Http\Resources\TripResource;
use App\Models\Trip;

class TripRepository
{
    public function getAllTrips()
    {
        return TripResource::collection(Trip::paginate(10));
    }

    public function getTripsForDriver($driverId)
    {
        return TripResource::collection(Trip::paginate(10)->where('driver_id', $driverId))->get();
    }
}