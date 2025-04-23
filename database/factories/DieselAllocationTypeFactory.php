<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DieselAllocationType>
 */
class DieselAllocationTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([
                'Top-Up Allocation',
                'Fixed-Quota (Periodic) Allocation',
                'Distance-Based Allocation',
                'Fuel-Card / Account Allocation',
                'Fuel-Card / Account Allocation',
                'Reimbursement-After-The-Fact'
            ]),
        ];
    }
}
