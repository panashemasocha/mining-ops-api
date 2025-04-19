<?php
namespace App\Repositories;

use App\Models\OreQualityType;

class OreQualityTypeRepository
{
    public function getAllOreQualityTypes(){
       return OreQualityType::all();
    }
}