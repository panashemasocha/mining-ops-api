<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driver' => $this->driver_id ? new UserResource(User::find($this->driver_id)) : null,
            'vehicle' => $this->vehicle ? new VehicleResource($this->vehicle) : null,
            'dispatch' => $this->dispatch ? new DispatchResource($this->dispatch) : null,
            'oreQuantity' => $this->ore_quantity,
            'initialLocation' => [
                'longitude' => $this->initial_longitude,
                'latitude' => $this->initial_latitude,
                'altitude' => $this->initial_altitude,
            ],
            'finalLocation' => [
                'longitude' => $this->final_longitude,
                'latitude' => $this->final_latitude,
                'altitude' => $this->final_altitude,
            ],
            'dieselAllocation' => $this->dieselAllocation
                ? DieselAllocationResource::collection($this->dieselAllocation)
                : null,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}