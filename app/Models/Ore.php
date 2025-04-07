<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ore extends Model
{
    use HasFactory;
    protected $fillable = [
        'type', 'quality_type','quality_grade','quantity','supplier_id', 'created_by',
        'longitude', 'latitude', 'altitude'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
