<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MiningSite>
 */
class MiningSiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Chibara']),
            'longitude' => $this->faker->longitude(25.237, 33.056), // Longitude range for Zimbabwe
            'latitude' => $this->faker->latitude(-22.421, -15.609), // Latitude range for Zimbabwe
            'altitude' => $this->faker->numberBetween(500, 1500), // Altitude between 500 and 1500 meters
        ];
    }
}
