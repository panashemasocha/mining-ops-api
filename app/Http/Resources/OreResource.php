<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'quality' => [
                'type' => $this->quality_type,
                'grade' => $this->quality_grade,
            ],
            'quantity' => $this->quantity,
            'supplier' => new SupplierResource($this->supplier),
            'creator' => new UserResource(User::find($this->created_by)),
            'location' => [
                'name' => $this->location_name,
                'longitude' => $this->longitude,
                'latitude' => $this->latitude,
                'altitude' => $this->altitude,
            ],
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}