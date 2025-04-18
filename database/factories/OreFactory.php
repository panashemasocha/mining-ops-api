<?php

namespace Database\Factories;

use App\Models\Ore;
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
        // keep a static counter and letters array
        static $index = 0;
        static $letters = null;
        if (is_null($letters)) {
            $letters = range('A', 'Z');
        }

        $letter = $letters[$index % count($letters)];
        $index++;

        $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
        $quality_grade = $qualityType === 'Gem-Quality'
            ? $this->faker->randomElement(['A', 'B', 'C'])
            : $this->faker->randomElement(['High', 'Medium', 'Low']);

        return [
            'type'           => 'Kyanite',
            'quality_type'   => $qualityType,
            'quality_grade'  => $quality_grade,
            'quantity'       => $this->faker->randomFloat(2, 0.1, 1000),
            'supplier_id'    => Supplier::factory(),
            'created_by'     => User::factory()->state(['job_position_id' => 4]), // Quality Controller
            'location_name'  => 'Point ' . $letter,
            'longitude'      => $this->faker->longitude(25.237, 33.056),
            'latitude'       => $this->faker->latitude(-22.421, -15.609),
            'altitude'       => $this->faker->numberBetween(500, 1500),
        ];
    }
}
