<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'driverId' => $this->driver_id,
            'vehicleId' => $this->vehicle_id,
            'dispatchId' => $this->dispatch_id,
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
            'initialDiesel' => $this->initial_diesel,
            'tripDieselAllocated' => $this->trip_diesel_allocated,
            'topUpDiesel' => $this->top_up_diesel,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}