<?php
namespace App\Repositories;

use App\Models\OreQualityGrade;

class OreQualityGradeRepository
{
    public function getAllOreQualityGrade()
    {
        OreQualityGrade::all();
    }
}