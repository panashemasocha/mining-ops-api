<?php

namespace App\Repositories;

use App\Models\DieselAllocationType;

class DieselAllocationTypeRepository
{
    public function getAllDieselAllocationTypes()
    {
        return DieselAllocationType::all();
    }
}