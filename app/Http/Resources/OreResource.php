<?php
namespace App\Http\Resources;

use App\Models\OreQualityType;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OreResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => new OreTypeResource($this->oreType),
            'qualityType' => new OreQualityTypeResource($this->oreQualityType),
            'qualityGrade' => new OreQualityGradeResource($this->oreQualityGrade),
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