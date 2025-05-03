<?php

namespace App\Repositories;

use App\Models\VehicleSubType;
class VehicleSubTypeRepository
{
    //Get all vehicle sub types
    public function getAllSubTypes()
    {
        return VehicleSubType::all();
    }
}