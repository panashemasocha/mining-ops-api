<?php

namespace Database\Factories;

use App\Models\GLTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GLTransaction>
 */
class GLTransactionFactory extends Factory
{
    protected $model = GLTransaction::class;

    public function definition()
    {
        $supplierName = $this->faker->firstName . ' ' . $this->faker->lastName;
        $dispatchId   = $this->faker->numberBetween(1, 500);
        $oreType      = "Kyanite";

        // Choose one of the two patterns
        if ($this->faker->boolean) {
            $description = "Ore ($oreType) Cost - $supplierName - $dispatchId ";
        } else {
            $description = "Loading Cost- $supplierName- $dispatchId ";
        }

        return [
            'trans_date'  => $this->faker->date(),
            'description' => $description,
            'created_by'  => 1,
        ];
    }

}
