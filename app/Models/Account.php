<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['account_name', 'account_type', 'status'];

    /**
     * All GL entries posted to this account.
     */
    public function entries()
    {
        return $this->hasMany(GLEntry::class, 'account_id');
    }

    /**
     * All transactions that touch this account,
     * via the GL entries pivot.
     */
    public function transactions()
    {
        return $this->hasManyThrough(
            GLTransaction::class,
            GLEntry::class,
            'account_id',   // FK on gl_entries…
            'id',           // PK on gl_transactions
            'id',           // PK on accounts
            'trans_id'      // FK on gl_entries to gl_transactions
        );
    }
}
