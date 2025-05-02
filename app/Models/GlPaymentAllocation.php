<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlPaymentAllocation extends Model
{
    use HasFactory;
    protected $table = 'gl_payment_allocations';
    protected $fillable =
        [
            'payment_trans_id',
            'invoice_trans_id',
            'allocated_amount'
        ];

    /**
     * The payment transaction this allocation debits.
     */
    public function paymentTransaction()
    {
        return $this->belongsTo(GLTransaction::class, 'payment_trans_id');
    }

    /**
     * The invoice transaction this allocation credits.
     */
    public function invoiceTransaction()
    {
        return $this->belongsTo(GLTransaction::class, 'invoice_trans_id');
    }

}
