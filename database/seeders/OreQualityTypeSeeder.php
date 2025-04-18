<?php

namespace Database\Seeders;

use App\Models\OreQualityType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OreQualityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OreQualityType::create(['quality' => 'Gem-Quality']);
        OreQualityType::create(['quality' => 'Industrial-Grade']);
    }
}
