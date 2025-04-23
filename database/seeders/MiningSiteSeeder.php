<?php

namespace Database\Seeders;

use App\Models\MiningSite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MiningSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MiningSite::factory()->count(1)->create();
    }
}
