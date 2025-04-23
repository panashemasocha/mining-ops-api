<?php

namespace Database\Seeders;

use App\Models\ExcavatorUsage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExcavatorUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExcavatorUsage::factory()->count(10)->create();
    }
}
