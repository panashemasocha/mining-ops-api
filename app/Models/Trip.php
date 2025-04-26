<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'dispatch_id',
        'ore_quantity',
        'initial_longitude',
        'initial_latitude',
        'initial_altitude',
        'final_longitude',
        'final_latitude',
        'final_altitude',
        'diesel_allocation_id',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id');
    }

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function dieselAllocation()
    {
        return $this->belongsTo(DieselAllocation::class);
    }

    public function oreloaders()
    {
        return $this->hasMany(OreLoader::class);
    }

    public function odometerReading()
    {
        return $this->hasMany(OdometerReading::class);
    }
}
