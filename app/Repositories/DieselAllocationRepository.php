<?php

namespace App\Repositories;

use App\Models\DieselAllocation;

class DieselAllocationRepository
{
    public function getDieselAllocations($startDate, $endDate)
    {
        return DieselAllocation::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->get();

    }
}