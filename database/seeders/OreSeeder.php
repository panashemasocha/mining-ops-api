<?php
namespace Database\Seeders;

use App\Models\Ore;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class OreSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $suppliers = Supplier::all();
        $users = User::whereHas('jobPosition', function ($query) {
            $query->where('name', 'Quality Controller');
        })->get();

        // prepare letters A–Z
        $letters = range('A', 'Z');

        for ($i = 0; $i < 10; $i++) {
            // pick quality type/grade fresh each iteration
            $qualityType = $this->faker->randomElement(['Gem-Quality', 'Industrial-Grade']);
            $quality_grade = $qualityType === 'Gem-Quality'
                ? $this->faker->randomElement(['A', 'B', 'C'])
                : $this->faker->randomElement(['High', 'Medium', 'Low']);

            Ore::create([
                'type'           => 'Kyanite',
                'quality_type'   => $qualityType,
                'quality_grade'  => $quality_grade,
                'quantity'       => $this->faker->randomFloat(2, 0.1, 1000),
                'supplier_id'    => $suppliers->random()->id,
                'created_by'     => $users->random()->id,
                'location_name'  => 'Point ' . $letters[$i % count($letters)],
                'longitude'      => $this->faker->longitude(25.237, 33.056),
                'latitude'       => $this->faker->latitude(-22.421, -15.609),
                'altitude'       => $this->faker->numberBetween(500, 1500),
            ]);
        }
    }
}
