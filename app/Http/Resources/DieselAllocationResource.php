<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DieselAllocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle' => $this->vehicle 
                ? new VehicleResource($this->vehicle) 
                : null,
            'type' => $this->dieselAllocationType 
                ? new DieselAllocationTypeResource($this->dieselAllocationType) 
                : null,
            'litres' => $this->litres,
        ];
    }
}