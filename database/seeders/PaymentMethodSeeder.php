<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        // Seed Payment methods
        PaymentMethod::create(['name' => 'Cash']);
        PaymentMethod::create(['name' => 'Bank transfer']);
        PaymentMethod::create(['name' => 'Ecocash']);
    }
}
