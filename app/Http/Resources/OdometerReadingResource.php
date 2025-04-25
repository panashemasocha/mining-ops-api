<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class OdometerReadingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'vehicle'           => new VehicleResource($this->vehicle),
            'trip'              => $this->whenLoaded('trip', fn() => new TripResource($this->trip)),
            'initialValue'      => $this->initial_value,
            'tripEndValue'      => $this->trip_end_value,
            'readingUnit'       => $this->reading_unit,
            'meterNotWorking'   => (bool)$this->meter_not_working,
            'createdAt'         => $this->created_at,
            'updatedAt'         => $this->updated_at,
        ];
    }
}