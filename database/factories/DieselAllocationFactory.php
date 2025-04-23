<?php

namespace Database\Factories;

use App\Models\DieselAllocation;
use App\Models\DieselAllocationType;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DieselAllocation>
 */
class DieselAllocationFactory extends Factory
{
    protected $model = DieselAllocation::class;

    public function definition()
    {
        $allocationTypes = DieselAllocationType::all();
        return [
            'vehicle_id' => Vehicle::factory(),
            'type_id' => $allocationTypes->random()->id,
            'litres' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
