<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'reg_number',
        'category',
        'sub_type',
        'department',
        'assigned_site',
        'vehicle_type',
        'make',
        'model',
        'year_of_manufacture',
        'vin',
        'loading_capacity',
        'engine_hours',
        'current_odometer_km',
        'fuel_type',
        'acquisition_date',
        'next_service_date',
        'insurance_expiry_date',
        'last_known_longitude',
        'last_known_latitude',
        'last_known_altitude',
        'status',
    ];

    public function assignedDrivers()
    {
        return $this->hasMany(AssignedVehicle::class);
    }

    public function dieselAllocations()
    {
        return $this->hasMany(DieselAllocation::class);
    }

    public function odometerReading()
    {
        return $this->hasMany(OdometerReading::class);
    }

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function vehicleSubType()
    {
        return $this->belongsTo(VehicleSubType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedSite()
    {
        return $this->belongsTo(MiningSite::class, 'assigned_site_id');
    }
}
