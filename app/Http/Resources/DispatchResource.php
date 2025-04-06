<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'oreId' => $this->ore_id,
            'vehicleId' => $this->vehicle_id,
            'siteClerkId' => $this->site_clerk_id,
            'loadingMethod' => $this->loading_method,
            'oreCostPerTonne' => $this->ore_cost_per_tonne,
            'loadingCostPerTonne' => $this->loading_cost_per_tonne,
            'oreQuantityRemaining' => $this->ore_quantity_remaining,
            'status' => $this->status,
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}