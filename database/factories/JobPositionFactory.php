<?php

namespace Database\Factories;

use App\Models\JobPosition;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPosition>
 */
class JobPositionFactory extends Factory
{
    protected $model = JobPosition::class;

    public function definition()
    {
        $positions = [
            'Director' => 'executive',
            'CEO' => 'executive',
            'Accountant' => 'management',
            'Quality Controller' => 'lower-management',
            'Driver' => 'general',
            'Accountant Assistant' => 'lower-management',
            'Site Clerk' => 'lower-management',
            'Operations Manager' => 'management',
            'Safety Officer' => 'management',
        ];

        $position = $this->faker->unique()->randomElement(array_keys($positions));
        $role = UserRole::where('name', $positions[$position])->firstOrCreate(['name' => $positions[$position]]);

        return [
            'name' => $position,
            'role_id' => $role->id,
        ];
    }
}
