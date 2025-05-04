<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GLEntry extends Model
{
    use HasFactory;

    protected $table = 'gl_entries';

    protected $fillable = [
        'trans_id',
        'account_id',
        'debit_amt',
        'credit_amt',
    ];

    public function transaction()
    {
        return $this->belongsTo(GLTransaction::class, 'trans_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }
}
