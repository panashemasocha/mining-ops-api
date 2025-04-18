<?php

namespace Database\Factories;

use App\Models\Ore;
use App\Models\OreQualityGrade;
use App\Models\OreQualityType;
use App\Models\OreType;
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
        // Static counter for unique location names
        static $index = 0;
        static $letters = null;
        if (is_null($letters)) {
            $letters = range('A', 'Z');
        }
        $letter = $letters[$index % count($letters)];
        $index++;

        // Pick or create the quality type
        $typeName = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
        $qualityTypeModel = OreQualityType::firstOrCreate([
            'quality' => $typeName,
        ]);

        // Determine allowed grades and pick one
        $allowedGrades = $typeName === 'Gem-Quality'
            ? ['A', 'B', 'C']
            : ['High', 'Medium', 'Low'];
        $chosenGrade = $this->faker->randomElement($allowedGrades);

        // Pick or create the quality grade for this type
        $gradeModel = OreQualityGrade::firstOrCreate([
            'ore_quality_type_id' => $qualityTypeModel->id,
            'quality' => $chosenGrade,
        ]);

        return [
            'ore_type_id' => OreType::factory(),
            'ore_quality_type_id' => $qualityTypeModel->id,
            'ore_quality_grade_id' => $gradeModel->id,
            'quantity' => $this->faker->randomFloat(2, 0.1, 1000),
            'supplier_id' => Supplier::factory(),
            'created_by' => User::factory()->state(['job_position_id' => 4]),
            'location_name' => 'Point ' . $letter,
            'longitude' => $this->faker->longitude(25.237, 33.056),
            'latitude' => $this->faker->latitude(-22.421, -15.609),
            'altitude' => $this->faker->numberBetween(500, 1500),
        ];
    }
}
