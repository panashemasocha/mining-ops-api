<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DieselAllocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'vehicle' => new VehicleResource($this->vehicle),
            'type'=> new DieselAllocationTypeResource($this->dieselAllocationType),
            'litres' => $this->litres,
        ];
    }
}