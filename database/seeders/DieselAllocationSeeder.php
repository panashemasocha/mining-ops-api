<?php

namespace Database\Seeders;

use App\Models\DieselAllocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DieselAllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DieselAllocation::factory()->count(10)->create();
    }
}
