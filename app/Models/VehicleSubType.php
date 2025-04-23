<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSubType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id'];

    public function vehicleCategory()
    {
        return $this->belongsTo(VehicleCategory::class);
    }
}
