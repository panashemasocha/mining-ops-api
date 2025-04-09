<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'oreId' => new OreResource($this->ore),
            'vehicle' => new VehicleResource($this->vehicle),
            'siteClerkId' => $this->site_clerk_id,
            'loadingMethod' => $this->loading_method,
            'costPerTonne'=>[
                'ore' => $this->ore_cost_per_tonne,
                'loading' => $this->loading_cost_per_tonne,
            ],
            'oreQuantityRemaining' => $this->ore_quantity_remaining,
            'status' => $this->status,
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}