<?php

namespace Database\Seeders;

use App\Models\DieselAllocationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DieselAllocationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['type' => 'Top-Up Allocation'],
            ['type' => 'Fixed-Quota (Periodic) Allocation'],
            ['type' => 'Distance-Based Allocation'],
            ['type' => 'Fuel-Card / Account Allocation'],
            ['type' => 'Reimbursement-After-The-Fact'],
        ];

        foreach ($types as $type) {
            DieselAllocationType::create($type);
        }
    }
}
