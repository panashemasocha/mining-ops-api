<?php

namespace App\Models;
use Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code', 'first_name', 'last_name', 'phone_number', 'pin', 'status',
        'job_position_id', 'branch_id', 'department_id', 'role_id',
        'physical_address', 'date_of_birth', 'national_id', 'gender', 'email'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pin',
        'remember_token',
    ];

    public function setPinAttribute($value)
    {
        $this->attributes['pin'] = Hash::make($value);
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = strpos($value, '+263') === 0 ? $value : '+263' . ltrim($value, '0');
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class);
    }

    public function driverInfo()
    {
        return $this->hasOne(DriverInfo::class);
    }
}
