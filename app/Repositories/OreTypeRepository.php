<?php
namespace App\Repositories;

use App\Models\OreType;

class OreTypeRepository
{
    public function getOreTypes(){
        OreType::all();
    }
}