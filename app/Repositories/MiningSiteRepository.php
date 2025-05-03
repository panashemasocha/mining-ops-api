<?php

namespace App\Repositories;

use App\Models\MiningSite;
class MiningSiteRepository
{
    //Retrieves all mining sites
    public function getAllSites()
    {
        return MiningSite::all();
    }
}