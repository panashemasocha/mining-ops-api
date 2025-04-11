<?php

namespace App\Repositories;

use App\Models\CostPrice;

class PriceRepository
{
    public function getAllPrices()
    {
        return CostPrice::paginate(10);
    }
}