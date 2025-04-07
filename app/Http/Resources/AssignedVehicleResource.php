<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignedVehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driverId' => $this->driver_id,
            'vehicleId' => $this->vehicle_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}