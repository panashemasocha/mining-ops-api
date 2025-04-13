<?php

namespace Database\Factories;

use App\Models\Ore;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispatch>
 */
class DispatchFactory extends Factory
{
    protected $model = Dispatch::class;

    public function definition()
    {
        return [
            'ore_id' => Ore::factory()->create(),
            'vehicle_id' => Vehicle::factory()->create(),
            'site_clerk_id' => User::factory()->create(['job_position_id' => 7]), // 'Site Clerk'
            'loading_method' => null,
            'ore_cost_per_tonne' => $this->faker->randomFloat(2, 10, 100),
            'loading_cost_per_tonne' => $this->faker->randomFloat(2, 5, 50),
            'ore_quantity' => $this->faker->randomFloat(2, 10, 100),
            'status' => 'pending',
            'payment_status' => 'n/a',
        ];
    }
}
