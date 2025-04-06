<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssignedVehicle>
 */
class AssignedVehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => User::factory()->create(['job_position_id' => 5]), // 'Driver'
            'vehicle_id' => Vehicle::factory()->create(),
            'vehicle_type' => $this->faker->randomElement(['truck horse', 'trailer 1', 'trailer 2', 'tractor', 'single cab truck', 'club cab truck', 'double cab truck']),
        ];
    }
}
