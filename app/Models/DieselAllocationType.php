<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DieselAllocationType extends Model
{
    use HasFactory;
    protected $fillable = ['type'];

    public function dieselAllocation(){
        return $this->hasMany(DieselAllocation::class);
    }
    
}
