<?php

namespace Database\Factories;

use App\Models\DriverInfo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverInfo>
 */
class DriverInfoFactory extends Factory
{
    protected $model = DriverInfo::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create(['job_position_id' => 5]), 
            'license_number' => 'DL' . $this->faker->unique()->numberBetween(100000, 999999),
            'last_known_longitude' => $this->faker->longitude(25.237, 33.056), 
            'last_known_latitude' => $this->faker->latitude(-22.421, -15.609), 
            'last_known_altitude' => $this->faker->numberBetween(500, 1500),
            'status' => $this->faker->randomElement(['active trip', 'off trip']),
        ];
    }
}
