<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use App\Models\VehicleSubType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSubTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $map = [
            'Admin' => ['passenger', 'utility'],
            'Mining' => ['haulage', 'excavation', 'support'],
        ];
        foreach ($map as $cat => $types) {
            $category = VehicleCategory::where('name', $cat)->first();
            foreach ($types as $name) {
                VehicleSubType::firstOrCreate(['name' => $name, 'category_id' => $category->id]);
            }
        }
    }
}
