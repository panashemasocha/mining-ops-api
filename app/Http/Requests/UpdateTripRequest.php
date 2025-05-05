<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array( $user->jobPosition->id,[5]);
    }

    public function rules()
    {
        return [
            'driver_id' => 'sometimes|exists:users,id',
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'dispatch_id' => 'sometimes|exists:dispatches,id',
            'ore_quantity' => 'sometimes|numeric|min:0',
            'initial_longitude' => 'sometimes|nullable|numeric|between:25.237,33.056',
            'initial_latitude' => 'sometimes|nullable|numeric|between:-22.421,-15.609',
            'initial_altitude' => 'sometimes|nullable|numeric|min:0|max:2000',
            'final_longitude' => 'sometimes|numeric|between:25.237,33.056',
            'final_latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'final_altitude' => 'sometimes|numeric|min:0|max:2000',
            'diesel_allocation_id' => 'sometimes|nullable|exists:diesel_allocations,id',
            'status' => 'sometimes|in:fulfilled,pending,in-transit,failed',
        ];
    }

        /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'driver_id' => $this->input('driver_id', $this->input('driverId')),
            'vehicle_id' => $this->input('vehicle_id', $this->input('vehicleId')),
            'dispatch_id' => $this->input('dispatch_id', $this->input('dispatchId')),
            'ore_quantity' => $this->input('ore_quantity', $this->input('oreQuantity')),
            'initial_longitude' => $this->input('initial_longitude', $this->input('initialLongitude')),
            'initial_latitude' => $this->input('initial_latitude', $this->input('initialLatitude')),
            'initial_altitude' => $this->input('initial_altitude', $this->input('initialAltitude')),
            'final_longitude' => $this->input('final_longitude', $this->input('finalLongitude')),
            'final_latitude' => $this->input('final_latitude', $this->input('finalLatitude')),
            'final_altitude' => $this->input('final_altitude', $this->input('finalAltitude')),
            'diesel_allocation_id' => $this->input('diesel_allocation_id', $this->input('dieselAllocationId')),
            'status' => $this->input('status', $this->input('status')),
        ]);
    }
}
