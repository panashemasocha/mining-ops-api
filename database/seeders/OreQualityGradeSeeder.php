<?php

namespace Database\Seeders;

use App\Models\OreQualityGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OreQualityGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OreQualityGrade::create(['grade' => 'A']);
        OreQualityGrade::create(['grade' => 'B']);
        OreQualityGrade::create(['grade' => 'C']);

        OreQualityGrade::create(['grade' => 'High']);
        OreQualityGrade::create(['grade' => 'Medium']);
        OreQualityGrade::create(['grade' => 'Low']);
    }
}
