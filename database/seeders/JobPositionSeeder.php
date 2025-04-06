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
            ['name' => 'Mining Engineer', 'role_id' => 2],
            ['name' => 'Geologist', 'role_id' => 2],
            ['name' => 'Mine Manager', 'role_id' => 2],
            ['name' => 'Blaster', 'role_id' => 4],
            ['name' => 'Surveyor', 'role_id' => 3],
            ['name' => 'Heavy Equipment Operator', 'role_id' => 4],
            ['name' => 'Safety Officer', 'role_id' => 2],
            ['name' => 'Maintenance Supervisor', 'role_id' => 3],
            ['name' => 'Environmental Officer', 'role_id' => 3],
            ['name' => 'Procurement Officer', 'role_id' => 3],
            ['name' => 'Human Resources Manager', 'role_id' => 2],
            ['name' => 'Logistics Coordinator', 'role_id' => 3],
            ['name' => 'Plant Operator', 'role_id' => 4],
            ['name' => 'Laboratory Technician', 'role_id' => 4],
            ['name' => 'Mine Foreman', 'role_id' => 3],
            ['name' => 'Electrical Technician', 'role_id' => 4],
            ['name' => 'Mechanical Technician', 'role_id' => 4],
            ['name' => 'Warehouse Supervisor', 'role_id' => 3],
            ['name' => 'Security Officer', 'role_id' => 4],
            ['name' => 'Community Liaison Officer', 'role_id' => 3],
        ];
        foreach ($jobPositions as $position) {
            JobPosition::create($position);
        }
    }
}
