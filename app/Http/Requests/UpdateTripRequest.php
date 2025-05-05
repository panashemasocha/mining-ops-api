<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->jobPosition->id, [5]);
    }

    public function rules()
    {
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'driver_id'           => "{$prefix}exists:users,id",
            'vehicle_id'          => "{$prefix}exists:vehicles,id",
            'dispatch_id'         => "{$prefix}exists:dispatches,id",
            'ore_quantity'        => "{$prefix}numeric|min:0",
            'initial_longitude'   => "{$prefix}nullable|numeric|between:25.237,33.056",
            'initial_latitude'    => "{$prefix}nullable|numeric|between:-22.421,-15.609",
            'initial_altitude'    => "{$prefix}nullable|numeric|min:0|max:2000",
            'final_longitude'     => "{$prefix}numeric|between:25.237,33.056",
            'final_latitude'      => "{$prefix}numeric|between:-22.421,-15.609",
            'final_altitude'      => "{$prefix}numeric|min:0|max:2000",
            'diesel_allocation_id'=> "{$prefix}nullable|exists:diesel_allocations,id",
            'status'              => "{$prefix}in:fulfilled,pending,in-transit,failed",
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'driver_id'            => $this->input('driver_id', $this->input('driverId')),
            'vehicle_id'           => $this->input('vehicle_id', $this->input('vehicleId')),
            'dispatch_id'          => $this->input('dispatch_id', $this->input('dispatchId')),
            'ore_quantity'         => $this->input('ore_quantity', $this->input('oreQuantity')),
            'initial_longitude'    => $this->input('initial_longitude', $this->input('initialLongitude')),
            'initial_latitude'     => $this->input('initial_latitude', $this->input('initialLatitude')),
            'initial_altitude'     => $this->input('initial_altitude', $this->input('initialAltitude')),
            'final_longitude'      => $this->input('final_longitude', $this->input('finalLongitude')),
            'final_latitude'       => $this->input('final_latitude', $this->input('finalLatitude')),
            'final_altitude'       => $this->input('final_altitude', $this->input('finalAltitude')),
            'diesel_allocation_id' => $this->input('diesel_allocation_id', $this->input('dieselAllocationId')),
        ]);
    }
}
