<?php

namespace Database\Factories;

use App\Models\DieselAllocation;
use App\Models\Dispatch;
use App\Models\ExcavatorUsage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExcavatorUsage>
 */
class ExcavatorUsageFactory extends Factory
{
    protected $model = ExcavatorUsage::class;

    public function definition()
    {
        return [
            'driver_id' => function () {
                $driver = User::where('job_position_id', 5)->inRandomOrder()->first();
                return $driver
                    ? $driver->id
                    : User::factory()->create(['job_position_id' => 5])->id;
            },
            'vehicle_id' => function () {
                $vehicle = Vehicle::inRandomOrder()->first();
                return $vehicle
                    ? $vehicle->id
                    : Vehicle::factory()->create()->id;
            },
            'dispatch_id' => function () {
                $dispatch = Dispatch::inRandomOrder()->first();
                return $dispatch
                    ? $dispatch->id
                    : Dispatch::factory()->create()->id;
            },
            'start' => $this->faker->dateTimeBetween('-1 month'),
            'end' => $this->faker->dateTimeBetween('now', '+1 month'),
            'diesel_allocation_id' => DieselAllocation::factory(),
        ];
    }
}
