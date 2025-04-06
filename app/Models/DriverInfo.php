<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverInfo extends Model
{
    use HasFactory;
    protected $table = 'driver_info';

    protected $fillable = [
        'user_id', 'license_number', 'last_known_longitude', 'last_known_latitude',
        'last_known_altitude', 'status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
