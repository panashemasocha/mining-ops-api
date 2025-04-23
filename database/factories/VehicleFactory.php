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
        return [
            'category_id'         => VehicleCategory::factory(),
            'sub_type_id'         => VehicleSubType::factory(),
            'department_id'       => Department::factory(),
            'assigned_site_id'    => MiningSite::factory(),

            'reg_number'          => $this->faker->unique()->regexify('[A-Z]{2}-[0-9]{4}'),
            'vehicle_type'        => $this->faker->randomElement(['tractor','truck','excavator','bobcat']),
            'make'                => $this->faker->randomElement(['Toyota','John Deere','Scania','Isuzu']),
            'model'               => $this->faker->bothify('Model-??##'),
            'year_of_manufacture' => $this->faker->year(
                                        'now'    
                                    ),
            'vin'                 => $this->faker->regexify('[A-HJ-NPR-Za-hj-npr-z0-9]{17}'),

            'loading_capacity'    => $this->faker->randomFloat(2, 1, 100),
            'engine_hours'        => $this->faker->numberBetween(0, 10000),
           
            'fuel_type'           => $this->faker->randomElement(['petrol','diesel','electric','hybrid']),
            'acquisition_date'    => $this->faker->date(),
            'next_service_date'   => $this->faker->dateTimeBetween('+30 days', '+1 year')->format('Y-m-d'),
            'insurance_expiry_date'=>$this->faker->dateTimeBetween('now', '+2 years')->format('Y-m-d'),

            'last_known_longitude'=> $this->faker->longitude(25.237, 33.056),
            'last_known_latitude' => $this->faker->latitude(-22.421, -15.609),
            'last_known_altitude' => $this->faker->numberBetween(500, 1500),

            'status'              => $this->faker->randomElement([
                'active','inactive','maintenance','decommissioned','active trip','off trip'
            ]),
        ];
    }
}
