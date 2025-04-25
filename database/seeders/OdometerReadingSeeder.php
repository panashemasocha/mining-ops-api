<?php

namespace Database\Seeders;

use App\Models\OdometerReading;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OdometerReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OdometerReading::factory()->count(10)->create();
    }
}
