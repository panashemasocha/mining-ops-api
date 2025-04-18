<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepository
{
    public function getAllVehicles($perPage = 10)
    {
        return Vehicle::paginate($perPage,['*'],'vehicles_page');
    }
}