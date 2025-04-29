<?php

namespace App\Repositories;

use App\Models\Ore;

class OreRepository
{
    public function getOres(string $startDate, string $endDate)
    {
        return Ore::whereBetween('date_created', [$startDate, $endDate])
            ->orderBy('date_created', 'desc')
            ->get();
    }
}