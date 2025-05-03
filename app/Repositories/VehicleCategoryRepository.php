<?php

namespace App\Repositories;

use App\Models\VehicleCategory;

class VehicleCategoryRepository
{
    //Gets all vehicle categories
    public function getAllCategories()
    {
        return VehicleCategory::all();
    }
}
