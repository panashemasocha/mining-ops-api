<?php

namespace Database\Factories;

use App\Models\DieselAllocation;
use App\Models\Dispatch;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => function () {
                $driver = User::where('job_position_id', 5)->inRandomOrder()->first();
                return $driver
                    ? $driver->id
                    : User::factory()->create(['job_position_id' => 5])->id;
            },
            'vehicle_id' => function () {
                $vehicle = Vehicle::inRandomOrder()->first();
                return $vehicle
                    ? $vehicle->id
                    : Vehicle::factory()->create()->id;
            },
            'dispatch_id' => function () {
                $dispatch = Dispatch::inRandomOrder()->first();
                return $dispatch
                    ? $dispatch->id
                    : Dispatch::factory()->create()->id;
            },
            'ore_quantity' => $this->faker->randomFloat(2, 10, 100),
            'initial_longitude' => $this->faker->longitude(25.237, 33.056),
            'initial_latitude' => $this->faker->latitude(-22.421, -15.609),
            'initial_altitude' => $this->faker->numberBetween(500, 1500),
            'final_longitude' => $this->faker->longitude(25.237, 33.056),
            'final_latitude' => $this->faker->latitude(-22.421, -15.609),
            'final_altitude' => $this->faker->numberBetween(500, 1500),
            'diesel_allocation_id' => DieselAllocation::factory(),
            'status' => 'pending',
        ];
    }
}
