<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserRoleSeeder::class,
            JobPositionSeeder::class,
            DepartmentSeeder::class,
            BranchSeeder::class,
            UserSeeder::class,
            DriverInfoSeeder::class,

            VehicleCategorySeeder::class,
            VehicleSubTypeSeeder::class,
            VehicleSeeder::class,
            AssignedVehicleSeeder::class,
            PaymentMethodSeeder::class,
            SupplierSeeder::class,

            OreTypeSeeder::class,    
            OreQualityTypeSeeder::class, 
            OreQualityGradeSeeder::class,

            OreSeeder::class,
            CostPriceSeeder::class,
            DispatchSeeder::class,
            TripSeeder::class,

            AccountSeeder::class,
            GLTransactionSeeder::class,
            GLEntrySeeder::class,
 
            DieselAllocationTypeSeeder::class,
            DieselAllocationSeeder::class,
            ExcavatorUsageSeeder::class,

            OreLoaderSeeder::class,
            MiningSiteSeeder::class,

        ]);
    }
}
