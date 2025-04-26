<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;
    protected $fillable = [
        'ore_id', 'site_clerk_id', 'loading_method',
        'ore_cost_per_tonne', 'loading_cost_per_tonne', 'ore_quantity','max_quantity_per_trip',
        'status', 'payment_status'
    ];

    public function ore()
    {
        return $this->belongsTo(Ore::class);
    }

    public function siteClerk()
    {
        return $this->belongsTo(User::class, 'site_clerk_id');
    }

    
}
