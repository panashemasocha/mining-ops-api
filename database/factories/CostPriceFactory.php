<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CostPrice>
 */
class CostPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commodity' => $this->faker->randomElement(['loading cost', 'ore cost']),
            'ore_type' => 'Kyanite',
            'quality' => $this->faker->randomElement(['High', 'Medium', 'Low']),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'date_created' => $this->faker->date(),
            'created_by' => User::factory()->create(['role_id' => 2]), // 'management'
        ];
    }
}
