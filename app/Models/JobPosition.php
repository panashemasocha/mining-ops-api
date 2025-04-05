<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'role_id'];

    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }
}
