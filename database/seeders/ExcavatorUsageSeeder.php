<?php

namespace Database\Seeders;

use App\Models\ExcavatorUsage;
use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExcavatorUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExcavatorUsage::factory()->count(3)->create();
       
    }
}
