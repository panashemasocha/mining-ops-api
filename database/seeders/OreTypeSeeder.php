<?php

namespace Database\Seeders;

use App\Models\OreType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OreTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OreType::create(['type' => 'Kyanite']);

    }
}
