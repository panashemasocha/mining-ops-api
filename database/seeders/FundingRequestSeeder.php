<?php

namespace Database\Seeders;

use App\Models\FundingRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FundingRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        FundingRequest::factory()->count(2)->create();
    }
}
