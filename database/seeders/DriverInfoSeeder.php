<?php
namespace Database\Seeders;

use App\Models\DriverInfo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DriverInfoSeeder extends Seeder
{
    public function run()
    {
        $drivers = User::whereHas('jobPosition', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($drivers as $driver) {
            DriverInfo::create([
                'user_id' => $driver->id,
                'license_number' => 'DL' . rand(100000, 999999),
                'last_known_longitude' => $this->randomZimbabweLongitude(),
                'last_known_latitude' => $this->randomZimbabweLatitude(),
                'last_known_altitude' => rand(500, 1500),
                'status' => 'off trip', 
            ]);
        }
    }

    private function randomZimbabweLongitude()
    {
        return rand(25237000, 33056000) / 1000000;
    }

    private function randomZimbabweLatitude()
    {
        return rand(-22621000, -15609000) / 1000000;
    }
}