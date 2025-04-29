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
        $commodity = $this->faker->randomElement(['loading cost', 'ore cost','diesel cost']);

        if ($commodity === 'loading cost') {
            $price = 2;
            $qualityType = null;
            $qualityGrade = null;
            $oreType = 'Kyanite';
        } else if($commodity === 'ore cost'){
            $price = $this->faker->randomElement([7.5, 6, 10, 7.5, 8, 11]);
            $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
            $qualityGrade = $qualityType === 'Gem-Quality'
                ? $this->faker->randomElement(['A', 'B', 'C'])
                : $this->faker->randomElement(['High', 'Medium', 'Low']);
            $oreType = 'Kyanite';
        }else{
            $price = 2;
            $qualityType = null;
            $qualityGrade = null;
            $oreType = null;
        }

        return [
            'commodity'     => $commodity,
            'ore_type'      => $oreType,
            'quality_type'  => $qualityType,
            'quality_grade' => $qualityGrade,
            'price'         => $price,
            'created_by'    => User::factory()->create(['role_id' => 2]), // 'management'
        ];
    }
}
