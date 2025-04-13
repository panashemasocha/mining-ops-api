<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['account_name','account_type'];

    public function entries()
    {
        return $this->hasMany(GLEntry::class, 'account_id');
    }
}
