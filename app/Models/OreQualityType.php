<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OreQualityType extends Model
{
    use HasFactory;
    protected $fillable = ['quality'];
}
