<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepository
{
    public function getAllVehicles()
    {
        return Vehicle::all();
    }
}