<?php

namespace Database\Seeders;

use App\Models\AssignedVehicle;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class AssignedVehicleSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function run()
    {
        $drivers = User::whereHas('jobPosition', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        $vehicles = Vehicle::all();

        foreach ($drivers as $driver) {
            AssignedVehicle::create([
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicles->random()->id,
                'vehicle_type' => $this->faker->randomElement(['truck horse', 'trailer 1', 'trailer 2']),
            ]);
        }
    }
}