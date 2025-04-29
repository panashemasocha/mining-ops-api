<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\GLEntry;
use App\Models\GLTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GLEntry>
 */
class GLEntryFactory extends Factory
{
    protected $model = GLEntry::class;

    public function definition()
    {
        $debit = $this->faker->randomFloat(2, 0, 1000);
        $credit = $debit > 0 ? 0 : $this->faker->randomFloat(2, 0, 1000);

        return [
            'trans_id'   => GLTransaction::factory(),
            'account_id' => Account::factory(),
            'debit_amt'  => $debit,
            'credit_amt' => $credit,
        ];
    }
}
