<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExcavatorUsageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle' => new VehicleResource($this->vehicle),
            'driver' => new UserResource($this->driver),
            'dispatch' => new DispatchResource($this->dispatch),
            'dieselAllocation' => new DieselAllocationResource($this->dieselAllocation),
            'start' => $this->start,
            'end' => $this->end
        ];
    }
}