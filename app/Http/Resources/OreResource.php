<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'quality' => $this->quality,
            'supplierId' => $this->supplier_id,
            'createdBy' => $this->created_by,
            'location' => [
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'altitude' => $this->altitude,
            ],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}