<?php

namespace App\Repositories;

use App\Models\Ore;

class OreRepository
{
    /**
     * Fetch ores created on or between the given start and end dates 
     * sorted by newest first.
     *
     * @param  string  $startDate  YYYY-MM-DD
     * @param  string  $endDate    YYYY-MM-DD
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOres(string $startDate, string $endDate)
    {
        return Ore::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}