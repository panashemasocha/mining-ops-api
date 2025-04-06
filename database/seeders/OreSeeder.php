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

        for ($i = 0; $i < 10; $i++) {
            Ore::create([
                'type' => 'Kyanite',
                'quality' => $this->faker->randomElement(['High', 'Medium', 'Low']),
                'supplier_id' => $suppliers->random()->id,
                'created_by' => $users->random()->id,
                'longitude' => $this->faker->longitude(25.237, 33.056),
                'latitude' => $this->faker->latitude(-22.421, -15.609),
                'altitude' => $this->faker->numberBetween(500, 1500),
            ]);
        }
    }
}