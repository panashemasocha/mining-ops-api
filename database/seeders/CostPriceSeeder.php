<?php

namespace Database\Seeders;

use App\Models\CostPrice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class CostPriceSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        // Fetch all management users
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'management');
        })->get();

        // Define the list of commodities to seed
        $commodities = ['loading cost', 'ore cost', 'diesel cost'];

        foreach ($commodities as $commodity) {
            // Set defaults
            $price = null;
            $qualityType = null;
            $qualityGrade = null;
            $oreType = null;

            // Determine parameters based on commodity
            switch ($commodity) {
                case 'loading cost':
                    $price = 2;
                    break;

                case 'ore cost':
                    $price = $this->faker->randomElement([7.5, 6, 10, 7.5, 8, 11]);
                    $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
                    $qualityGrade = $qualityType === 'Gem-Quality'
                        ? $this->faker->randomElement(['A', 'B', 'C'])
                        : $this->faker->randomElement(['High', 'Medium', 'Low']);
                    $oreType = 'Kyanite';
                    break;

                case 'diesel cost':
                    $price = 1.46;
                    break;
            }

            for ($i = 0; $i < 1; $i++) {
                CostPrice::create([
                    'commodity'     => $commodity,
                    'ore_type'      => $oreType,
                    'quality_type'  => $qualityType,
                    'quality_grade' => $qualityGrade,
                    'price'         => $price,
                    'created_by'    => $users->random()->id,
                ]);
            }
        }
    }
}
