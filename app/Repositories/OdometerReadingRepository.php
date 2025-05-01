<?php

namespace App\Repositories;

use App\Models\OdometerReading;

class OdometerReadingRepository
{
    public function getOdometerReadings($startDate, $endDate)
    {
        return OdometerReading::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->get();
    }
}