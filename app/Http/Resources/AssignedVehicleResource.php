<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignedVehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driver' => new UserResource(User::find($this->driver_id)),
            'vehicleId' => new VehicleResource($this->vehicle),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}