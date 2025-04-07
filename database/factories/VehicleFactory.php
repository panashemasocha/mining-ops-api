<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reg_number'=> $this->faker->regexify('[A-Z]{2}-[0-9]{4}'),
            'vehicle_type' => $this->faker->randomElement([
                // 'truck horse',
                //  'trailer 1', 
                //  'trailer 2', 
                 'tractor',
                //   'single cab truck', 
                //   'club cab truck', 
                //   'double cab truck'
                ]),
            'loading_capacity' => $this->faker->randomFloat(2, 5, 50), // Random float between 5 and 50 tonnes
            'last_known_longitude' => $this->faker->longitude(25.237, 33.056), // Longitude range for Zimbabwe
            'last_known_latitude' => $this->faker->latitude(-22.421, -15.609), // Latitude range for Zimbabwe
            'last_known_altitude' => $this->faker->numberBetween(500, 1500), // Altitude between 500 and 1500 meters
            'status' => $this->faker->randomElement(['active trip', 'off trip']), 
        ];
    }
}
