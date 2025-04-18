<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ore extends Model
{
    use HasFactory;
    protected $fillable = [
        'ore_type_id',
        'ore_quality_type_id',
        'ore_quality_grade_id',
        'quantity',
        'supplier_id',
        'created_by',
        'location_name',
        'longitude',
        'latitude',
        'altitude'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function oreType()
    {
        return $this->belongsTo(OreType::class);
    }

    public function oreQualityType()
    {
        return $this->belongsTo(OreQualityType::class);
    }

    public function oreQualityGrade()
    {
        return $this->belongsTo(OreQualityGrade::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
