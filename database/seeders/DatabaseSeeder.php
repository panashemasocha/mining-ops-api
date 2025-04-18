<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
                // UserRoleSeeder::class,
                // JobPositionSeeder::class,
                // DepartmentSeeder::class,
                // BranchSeeder::class,
                // UserSeeder::class,
                // DriverInfoSeeder::class,

                // VehicleSeeder::class,
                // AssignedVehicleSeeder::class,
                // PaymentMethodSeeder::class,
                // SupplierSeeder::class,

                OreTypeSeeder::class,    //Seed this before OreSeeder
                OreQualityTypeSeeder::class, //Seed this before OreSeeder
                OreQualityGradeSeeder::class //Seed this before OreSeeder
                
                // OreSeeder::class,
                // CostPriceSeeder::class,
                // DispatchSeeder::class,
                // TripSeeder::class

                // AccountSeeder::class,
                // GLTransactionSeeder::class,
                // GLEntrySeeder::class

        

        ]);
    }
}
