<?php

namespace Database\Seeders;

use App\Models\Dispatch;
use App\Models\Ore;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class DispatchSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $ores = Ore::all();
        $vehicles = Vehicle::all();
        $siteClerks = User::whereHas('jobPosition', function ($query) {
            $query->where('name', 'Site Clerk');
        })->get();

        for ($i = 0; $i < 5; $i++) {
            Dispatch::create([
                'ore_id' => $ores->random()->id,
                'vehicle_id' => $vehicles->random()->id,
                'site_clerk_id' => $siteClerks->random()->id,
                'loading_method' => null,
                'ore_cost_per_tonne' => $this->faker->randomFloat(2, 10, 100),
                'loading_cost_per_tonne' => $this->faker->randomFloat(2, 5, 50),
                'ore_quantity' => $this->faker->randomFloat(2, 10, 100),
                'status' => 'pending',
                'payment_status' => 'n/a',
            ]);
        }
    }
}