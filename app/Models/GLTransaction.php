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
        'supplier_id',
        'trip_id',
        'trans_date',
        'description',
        'created_by',
    ];

        /**
     * Cast trans_date into a Carbon instance, and
     * timestamps into Carbon datetimes.
     */
    protected $casts = [
        'trans_date'  => 'date',      // now $this->trans_date is a Carbon
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * The journal entries for this transaction.
     */
    public function entries()
    {
        return $this->hasMany(GLEntry::class, 'trans_id');
    }

    /**
     * The user who created this transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The supplier involved in this transaction.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

     /**
     * The trip involved in this transaction.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Allocations where this transaction is a payment.
     */
    public function paymentAllocations()
    {
        return $this->hasMany(GlPaymentAllocation::class, 'payment_trans_id');
    }

    /**
     * Allocations where this transaction is an invoice.
     */
    public function invoiceAllocations()
    {
        return $this->hasMany(GlPaymentAllocation::class, 'invoice_trans_id');
    }

}
