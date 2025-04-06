<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $zimbabweanLastNames = [
            'Moyo',
            'Ncube',
            'Chikore',
            'Khumalo',
            'Sithole',
            'Dube',
            'Mhlanga',
            'Nyathi',
            'Chireya',
            'Madziva',
            'Chitando',
            'Matope',
            'Mandaza',
            'Mangena'
        ];

        return [
            'employee_code' => 'EMP' . $this->faker->unique()->numberBetween(100, 999),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->randomElement($zimbabweanLastNames),
            'phone_number' => '+263' . $this->faker->unique()->numberBetween(712000000, 779999999),
            'pin' => $this->faker->numerify('####'),
            'status' => $this->faker->randomElement([0, 1, 2]),
            'job_position_id' => \App\Models\JobPosition::factory(),
            'branch_id' => \App\Models\Branch::factory(),
            'department_id' => \App\Models\Department::factory(),
            'role_id' => \App\Models\UserRole::factory(),
            'physical_address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'national_id' => $this->faker->optional()->numerify('##########'),
            'gender' => $this->faker->optional()->randomElement(['male', 'female']),
            'email' => $this->faker->unique()->userName . '@mwamiresources.com',
        ];
    }
}
