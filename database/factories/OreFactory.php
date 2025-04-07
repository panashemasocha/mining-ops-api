<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ore>
 */
class OreFactory extends Factory
{
    protected $model = Ore::class;

    public function definition()
    {
       $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);

       $quality_grade = $qualityType === 'Gem-Quality'
           ? $this->faker->randomElement(['A', 'B', 'C'])
           : $this->faker->randomElement(['High', 'Medium', 'Low']);

       return [
           'type' => 'Kyanite',
           'quality_type'=>$qualityType,
           'quality_grade' => $quality_grade,
           'quantity' => $this->faker->randomFloat(2, 0.1, 1000),
           'supplier_id' => Supplier::factory()->create(),
           'created_by' => User::factory()->create(['job_position_id' => 4]), // 'Quality Controller'
           'longitude' => $this->faker->longitude(25.237, 33.056),
           'latitude' => $this->faker->latitude(-22.421, -15.609),
           'altitude' => $this->faker->numberBetween(500, 1500),
       ];
    }
}