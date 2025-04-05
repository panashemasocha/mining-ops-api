<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\JobPosition;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Set the Faker locale to Zimbabwean English (if available) or use default
        $faker = Faker::create('en_ZW');

        // Get all job positions
        $jobPositions = JobPosition::all();

        foreach ($jobPositions as $jobPosition) {
            for ($i = 0; $i < 2; $i++) {
                User::factory()->create([
                    'employee_code'   => 'EMP' . $faker->unique()->numberBetween(1000, 9999),
                    'first_name'      => $faker->firstName,
                    'last_name'       => $faker->lastName,
                    'phone_number'    => '+263' . $faker->unique()->numberBetween(712000000, 779999999),
                    'pin'             => '1234',
                    'status'          => $faker->randomElement([0, 1, 2]),
                    'job_position_id' => $jobPosition->id,
                    'branch_id'       => $faker->numberBetween(1, 2),
                    'department_id'   => $faker->numberBetween(1, 2),
                    'role_id'         => $faker->numberBetween(1, 4),
                    'physical_address'=> $faker->address,
                    'date_of_birth'   => $faker->date('Y-m-d', '2000-12-31'),
                    'national_id'     => $faker->optional()->numerify('##########'),
                    'gender'          => $faker->optional()->randomElement(['male', 'female']),
                    'email'           => strtolower($faker->firstName . '.' . $faker->lastName) . '@mwamiresources.com',
                ]);
            }
        }

        // For the "Driver" job position, 
        $driverPosition = JobPosition::where('name', 'Driver')->first();
        if ($driverPosition) {
            for ($i = 0; $i < 5; $i++) {
                User::factory()->create([
                    'employee_code'   => 'EMP' . $faker->unique()->numberBetween(1000, 9999),
                    'first_name'      => $faker->firstName,
                    'last_name'       => $faker->lastName,
                    'phone_number'    => '+263' . $faker->unique()->numberBetween(712000000, 779999999),
                    'pin'             => '1234',
                    'status'          => $faker->randomElement([0, 1, 2]),
                    'job_position_id' => $driverPosition->id,
                    'branch_id'       => $faker->numberBetween(1, 2),
                    'department_id'   => $faker->numberBetween(1, 2),
                    'role_id'         => $faker->numberBetween(1, 4),
                    'physical_address'=> $faker->address,
                    'date_of_birth'   => $faker->date('Y-m-d', '2000-12-31'),
                    'national_id'     => $faker->optional()->numerify('##########'),
                    'gender'          => $faker->optional()->randomElement(['male', 'female']),
                    'email'           => strtolower($faker->firstName . '.' . $faker->lastName) . '@mwamiresources.com',
                ]);
            }
        }
    }
}
