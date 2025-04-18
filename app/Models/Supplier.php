<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    
    protected $fillable = [
        'first_name', 'last_name', 'national_id', 'physical_address',
        'created_by', 'payment_method_id', 'phone_number'
    ];

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = strpos($value, '+263') === 0 ? $value : '+263' . ltrim($value, '0');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
