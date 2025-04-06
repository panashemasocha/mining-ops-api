<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
          //  UserRoleSeeder::class,
          //  JobPositionSeeder::class,
          //  DepartmentSeeder::class,
           // BranchSeeder::class,
            UserSeeder::class,
        ]);
    }
}
