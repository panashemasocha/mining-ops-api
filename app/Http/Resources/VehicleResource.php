<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'regNumber'=> $this->reg_number,
            'vehicleType'=>$this->vehicle_type,
            'loadingCapacity' => $this->loading_capacity,
            'lastKnownLocation' => [
                'longitude' => $this->last_known_longitude,
                'latitude' => $this->last_known_latitude,
                'altitude' => $this->last_known_altitude,
            ],
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}