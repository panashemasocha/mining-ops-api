<?php

namespace App\Repositories;

use App\Models\JobPosition;

class JobPositionRepository
{
    public function getAllJobPositions()
    {
        return JobPosition::all();
    }
}