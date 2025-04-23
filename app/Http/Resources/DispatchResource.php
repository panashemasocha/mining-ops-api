<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ore' => new OreResource($this->ore),
            'siteClerk' => new UserResource(User::find($this->site_clerk_id)),
            'loadingMethod' => $this->loading_method,
            'costPerTonne' => [
                'ore' => $this->ore_cost_per_tonne,
                'loading' => $this->loading_cost_per_tonne,
            ],
            'oreQuantity' => $this->ore_quantity,
            'maxQuantityPerTrip'=> $this->max_quantity_per_trip,
            'status' => $this->status,
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}