<?php

namespace Database\Factories;

use App\Models\OdometerReading;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OdometerReading>
 */
class OdometerReadingFactory extends Factory
{
    protected $model = OdometerReading::class;

    public function definition(): array
    {
        $initial = $this->faker->numberBetween(0, 100000);
        $vehicleIds = Vehicle::pluck('id')->toArray();
        $tripIds = Trip::pluck('id')->toArray();

        return [
            'vehicle_id'        => $this->faker->randomElement($vehicleIds),
            'trip_id'           => $this->faker->randomElement($tripIds),
            'initial_value'     => $initial,
            'trip_end_value'    => $initial + $this->faker->numberBetween(0, 500),
            'reading_unit'      => $this->faker->randomElement(['Kilometre', 'Mile']),
            'meter_not_working' => $this->faker->boolean(10),
        ];
    }
}
