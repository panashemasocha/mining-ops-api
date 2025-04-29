<?php

namespace Database\Factories;

use App\Models\FundingRequest;
use App\Models\PaymentMethod;
use App\Models\Account;
use App\Models\Department;
use App\Models\MiningSite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundingRequestFactory extends Factory
{
    protected $model = FundingRequest::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'payment_method_id' => PaymentMethod::inRandomOrder()->first()?->id ?? PaymentMethod::factory(),
            'account_id' => Account::inRandomOrder()->first()?->id ?? Account::factory(),
            'purpose' => $this->faker->sentence(),
            'approval_notes' => $this->faker->optional()->sentence(),
            'department_id' => Department::inRandomOrder()->first()?->id ?? Department::factory(),
            'mining_site_id' => MiningSite::inRandomOrder()->first()?->id ?? MiningSite::factory(),
            'accountant_id' => User::whereHas('role', fn($q) => $q->where('name', 'accountant'))->inRandomOrder()->first()?->id ?? User::factory(),
            'decision_date' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
