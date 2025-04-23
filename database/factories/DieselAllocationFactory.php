<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DieselAllocation>
 */
class DieselAllocationFactory extends Factory
{
    protected $model = DieselAllocation::class;

    public function definition()
    {
        return [
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'type_id' => \App\Models\DieselAllocationType::factory(),
            'litres' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
