<?php

namespace App\Repositories;

use App\Models\Ore;

class OreRepository
{
    public function getAllOres()
    {
        return Ore::all();
    }
}