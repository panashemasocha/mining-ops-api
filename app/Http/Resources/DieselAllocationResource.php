<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DieselAllocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'vehicleId' =>$this->vehicle_id,
            'type'=> new DieselAllocationTypeResource($this->dieselAllocationType),
            'litres' => $this->litres,
        ];
    }
}