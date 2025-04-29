<?php

namespace App\Repositories;

use App\Models\Ore;

class OreRepository
{
    public function getOres(string $startDate, string $endDate)
    {
        return Ore::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}