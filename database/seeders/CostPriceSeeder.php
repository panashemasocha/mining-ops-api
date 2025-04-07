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
        //CostPrice::factory()->count(7)->create();
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'management');
        })->get();

        $commodity = $this->faker->randomElement(['loading cost', 'ore cost']);

        if ($commodity === 'loading cost') {
            $price = 2;
            $qualityType = null;
            $qualityGrade = null;
            $oreType = 'Kyanite';
        } else {
            $price = $this->faker->randomElement([7.5, 6, 10, 7.5, 8, 11]);
            $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
            $qualityGrade = $qualityType === 'Gem-Quality'
                ? $this->faker->randomElement(['A', 'B', 'C'])
                : $this->faker->randomElement(['High', 'Medium', 'Low']);
            $oreType = 'Kyanite';
        }

        for ($i = 0; $i < 5; $i++) {
            CostPrice::create([
                'commodity' => $this->faker->randomElement(['loading cost', 'ore cost']),
                'ore_type' => $oreType,
                'quality_type'  => $qualityType,
                'quality_grade' => $qualityGrade,
                'price' => $price,
                'date_created' => $this->faker->date(),
                'created_by' => $users->random()->id,
            ]);
        }
    }
}