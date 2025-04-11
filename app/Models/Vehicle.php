<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'reg_number','vehicle_type','loading_capacity', 'last_known_longitude', 'last_known_latitude',
        'last_known_altitude', 'status'
    ];

    public function assignedDrivers(){
        return $this->hasMany(AssignedVehicle::class);
    }
}
