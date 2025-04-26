<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'regNumber'            => $this->reg_number,

            'category'             => new VehicleCategoryResource($this->vehicleCategory),
            'subType'              => new VehicleSubTypeResource($this->vehicleSubType),
            'department'           => new DepartmentResource($this->department),
            'assignedSite'         => new MiningSiteResource($this->assignedSite),

            'vehicleType'          => $this->vehicle_type,
            'make'                 => $this->make,
            'model'                => $this->model,
            'yearOfManufacture'    => $this->year_of_manufacture,
            'vin'                  => $this->vin,

            'loadingCapacity'      => $this->loading_capacity,
            'engineHours'          => $this->engine_hours,

            'fuelType'             => $this->fuel_type,
            'acquisitionDate'      => $this->acquisition_date,
            'nextServiceDate'      => $this->next_service_date,
            'insuranceExpiryDate'  => $this->insurance_expiry_date,

            'lastKnownLocation'    => [
                'longitude' => $this->last_known_longitude,
                'latitude'  => $this->last_known_latitude,
                'altitude'  => $this->last_known_altitude,
            ],
            "dieselAllocations" =>  DieselAllocationResource::collection($this->dieselAllocations),
            'status'               => $this->status,
            'createdAt'            => $this->created_at,
            'updatedAt'            => $this->updated_at,
        ];
    }
}