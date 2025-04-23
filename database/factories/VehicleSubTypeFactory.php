<?php

namespace Database\Factories;

use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleSubType>
 */
class VehicleSubTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {


        $choices = [
            'Admin' => ['passenger', 'utility'],
            'Mining' => ['haulage', 'excavation', 'support'],
        ];

        $category = VehicleCategory::inRandomOrder()->first() ?? VehicleCategory::factory();
        $name = $this->faker->unique()->randomElement($choices[$category->name] ?? []);

        return [
            'name' => $name,
            'category_id' => $category->id,
        ];

    }
}
