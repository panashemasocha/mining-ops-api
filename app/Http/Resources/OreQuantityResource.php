<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OreQuantityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'ore' => new OreResource($this->resource),
            'remainingQuantity' => (int) $this->remaining_quantity,
        ];
    }
}
