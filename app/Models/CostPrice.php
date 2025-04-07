<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'commodity', 'ore_type', 'quality_type','quality_grade', 'price', 'date_created', 'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
