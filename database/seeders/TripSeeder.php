<?php

namespace Database\Seeders;

use App\Models\DieselAllocation;
use App\Models\Trip;
use App\Models\Dispatch;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class TripSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $dispatches = Dispatch::all();
        $drivers = User::whereHas('jobPosition', function ($query) {
            $query->where('name', 'Driver');
        })->get();
        $vehicles = Vehicle::all();
        $dieselAllocations = DieselAllocation::all();

        for ($i = 0; $i < 5; $i++) {
            Trip::create([
                'driver_id' => $drivers->random()->id,
                'vehicle_id' => $vehicles->random()->id,
                'dispatch_id' => $dispatches->random()->id,
                'ore_quantity' => $this->faker->randomFloat(2, 10, 100),
                'initial_longitude' => $this->faker->longitude(25.237, 33.056),
                'initial_latitude' => $this->faker->latitude(-22.421, -15.609),
                'initial_altitude' => $this->faker->numberBetween(500, 1500),
                'final_longitude' => $this->faker->longitude(25.237, 33.056),
                'final_latitude' => $this->faker->latitude(-22.421, -15.609),
                'final_altitude' => $this->faker->numberBetween(500, 1500),
                'diesel_allocation_id' => $dieselAllocations->random()->id,
                'status' => 'pending',
            ]);
        }
    }
}