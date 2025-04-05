<?php

namespace Database\Seeders;

use App\Models\JobPosition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Job Positions
        $jobPositions = [
            ['name' => 'Director', 'role_id' => 1],
            ['name' => 'CEO', 'role_id' => 1],
            ['name' => 'Accountant', 'role_id' => 2],
            ['name' => 'Quality controller', 'role_id' => 3],
            ['name' => 'Driver', 'role_id' => 4],
            ['name' => 'Accountant assistant', 'role_id' => 3],
            ['name' => 'Site clerk', 'role_id' => 3],
        ];
        foreach ($jobPositions as $position) {
            JobPosition::create($position);
        }
    }
}
