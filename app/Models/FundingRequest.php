<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingRequest extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'amount',
            'payment_method_id',
            'account_id',
            'purpose', //Descriptive
            'approval_notes',
            'department_id',
            'mining_site_id',
            'accountant_id',
            'approved_by',
            'decision_date',
            'status' //enum: pending,accepted,rejected
        ];

    public function paymentMethod()
    {
        return $this->BelongsTo(PaymentMethod::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function miningSite()
    {
        return $this->belongsTo(MiningSite::class);
    }

    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

}
