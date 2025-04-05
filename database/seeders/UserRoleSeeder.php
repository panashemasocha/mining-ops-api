<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed User Roles
        $roles = [
            ['name' => 'executive'],
            ['name' => 'management'],
            ['name' => 'lower-management'],
            ['name' => 'general'],
        ];
        foreach ($roles as $role) {
            UserRole::create($role);
        }
    }
}
