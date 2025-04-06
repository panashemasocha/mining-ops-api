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
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'management');
        })->get();

        for ($i = 0; $i < 5; $i++) {
            CostPrice::create([
                'commodity' => $this->faker->randomElement(['loading cost', 'ore cost']),
                'ore_type' => 'Kyanite',
                'quality' => $this->faker->randomElement(['High', 'Medium', 'Low']),
                'price' => $this->faker->randomFloat(2, 10, 100),
                'date_created' => $this->faker->date(),
                'created_by' => $users->random()->id,
            ]);
        }
    }
}