<?php

namespace Database\Seeders;

use App\Models\GLTransaction;
use Database\Factories\GLTransactionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GLTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GLTransaction::factory()->count(10)->create();
    }
}
