<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcavatorUsage extends Model
{
    use HasFactory;

    protected $table = 'excavator_usage';
    protected $fillable = ['vehicle_id','driver_id','dispatch_id','start','end', 'diesel_allocation_id'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(){
        return $this->belongsTo(User::class);
    }

    public function dispatch(){
        return $this->belongsTo(Dispatch::class);
    }

    public function dieselAllocation()
    {
        return $this->belongsTo(DieselAllocation::class);
    }
}
