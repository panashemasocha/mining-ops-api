<?php

namespace App\Repositories;

use App\Models\Vehicle;

class VehicleRepository
{
    public function getAllVehicles()
    {
        return Vehicle::all();
    }
    
    /**
     * Get a vehicle by its ID
     *
     * @param int $id
     * @return \App\Models\Vehicle|null
     */
    public function getVehicleById(int $id)
    {
        // This explicitly returns the vehicle instance or null
        return Vehicle::where('id', $id)->first();
    }
}