<?php

namespace Database\Factories;

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
            'driver_id' => User::factory()->create(['job_position_id' => 5]), // 'Driver'
            'vehicle_id' => Vehicle::factory()->create(),
            'dispatch_id' => Dispatch::factory()->create(),
            'ore_quantity' => $this->faker->randomFloat(2, 10, 100),
            'initial_longitude' => $this->faker->longitude(25.237, 33.056),
            'initial_latitude' => $this->faker->latitude(-22.421, -15.609),
            'initial_altitude' => $this->faker->numberBetween(500, 1500),
            'final_longitude' => $this->faker->longitude(25.237, 33.056),
            'final_latitude' => $this->faker->latitude(-22.421, -15.609),
            'final_altitude' => $this->faker->numberBetween(500, 1500),
            'initial_diesel' => $this->faker->randomFloat(2, 50, 200),
            'trip_diesel_allocated' => $this->faker->randomFloat(2, 50, 200),
            'top_up_diesel' => $this->faker->randomFloat(2, 0, 50),
            'status' => 'pending',
        ];
    }
}
