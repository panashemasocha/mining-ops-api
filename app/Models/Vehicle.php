<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'loading_capacity', 'last_known_longitude', 'last_known_latitude',
        'last_known_altitude', 'status'
    ];
}
