<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserRole>
 */
class UserRoleFactory extends Factory
{
    protected $model = UserRole::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'executive', 'management', 'lower-management', 'general'
            ]),
        ];
    }
}
