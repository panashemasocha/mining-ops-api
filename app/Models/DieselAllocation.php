<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DieselAllocation extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'type_id', 'litres'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function dieselAllocationType()
    {
        return $this->belongsTo(DieselAllocationType::class);
    }

    public function excavatorUsage()
    {
        return $this->hasMany(ExcavatorUsage::class);
    }

    public function trips(){
        return $this->hasMany(Trip::class);
    }

}
