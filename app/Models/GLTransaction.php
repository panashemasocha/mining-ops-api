<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLTransaction extends Model
{
    use HasFactory;
    
    protected $table = 'gl_transactions';
    protected $fillable = [
        'trans_date',
        'description',
        'created_by',
    ];

    public function entries()
    {
        return $this->hasMany(GLEntry::class, 'trans_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
