<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\MiningSite;
use App\Models\VehicleCategory;
use App\Models\VehicleSubType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicleCategories   = VehicleCategory::all();
        $vehicleSubTypes     = VehicleSubType::all();
        $departments         = Department::all();
        $miningSites         = MiningSite::all();

        // Select make first
        $make = $this->faker->randomElement(['Toyota', 'John Deere', 'Scania', 'Isuzu']);

        // Define real-world models for each make
        $modelsByMake = [
            'Toyota'    => ['Hilux', 'Land Cruiser', 'Fortuner', 'Prado'],
            'John Deere'=> ['1025R', '5075E', '6120M', '8030'],
            'Scania'    => ['R500', 'S730', 'P360', 'G410'],
            'Isuzu'     => ['D-Max', 'NPR', 'F-Series', 'Elf'],
        ];

        // Restricted Zimbabwean plate prefixes
        $platePrefixes = ['AFZ', 'AFJ', 'AEZ', 'AEG', 'ACZ', 'AHA', 'AEU'];
        $prefix = $this->faker->randomElement($platePrefixes);

        return [
            'category_id'             => $vehicleCategories->random()->id,
            'sub_type_id'             => $vehicleSubTypes->random()->id,
            'department_id'           => $departments->random()->id,
            'assigned_site_id'        => $miningSites->random()->id,

            'reg_number'              => $prefix . $this->faker->numerify('####'),

            'vehicle_type'            => $this->faker->randomElement(['tractor', 'truck', 'excavator', 'bobcat']),
            'make'                    => $make,
            'model'                   => $this->faker->randomElement($modelsByMake[$make]),
            'year_of_manufacture'     => $this->faker->year('now'),
            'vin'                     => $this->faker->regexify('[A-HJ-NPR-Za-hj-npr-z0-9]{17}'),

            'odometer_reading_unit'   => $this->faker->randomElement(['Kilometre', 'Mile']),
            'initial_odometer_reading'=> (string) $this->faker->numberBetween(0, 200_000),

            'loading_capacity'        => $this->faker->randomFloat(2, 1, 100),
            'engine_hours'            => $this->faker->numberBetween(0, 10000),

            'fuel_type'               => $this->faker->randomElement(['petrol', 'diesel', 'electric', 'hybrid']),
            'acquisition_date'        => $this->faker->date(),
            'next_service_date'       => $this->faker->dateTimeBetween('+30 days', '+1 year')->format('Y-m-d'),
            'insurance_expiry_date'   => $this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),

            'last_known_longitude' => '31.053028' ?? $this->faker->longitude(25.237, 33.056),
            'last_known_latitude' => '-17.824858' ?? $this->faker->latitude(-22.421, -15.609),
            'last_known_altitude' => '1509.40' ?? $this->faker->numberBetween(500, 1500),

            'status' => $this->faker->randomElement([
                'off trip'
            ]),
        ];
    }
}
