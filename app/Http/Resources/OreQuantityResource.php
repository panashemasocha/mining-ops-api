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
            // embed the full ore payload via your existing OreResource:
            'ore' => new OreResource($this->resource),
            'remainingQuantity' => (int) $this->remaining_quantity,
        ];
    }
}
