<?php

namespace App\Repositories;

use App\Models\Ore;

class OreRepository
{
    public function getAllOres($perPage = 10)
    {
        return Ore::paginate($perPage, ['*'], 'ores_page');
    }
}