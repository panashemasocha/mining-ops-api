<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdometerReading extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'trip_id',
        'initial_value',
        'trip_end_value',
        'reading_unit',
        'meter_not_working',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
