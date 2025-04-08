<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->role->name === 'management';
    }

    public function rules()
    {
        return [
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'dispatch_id' => 'required|exists:dispatches,id',
            'ore_quantity' => 'required|numeric|min:0',
            'initial_longitude' => 'required|numeric|between:25.237,33.056',
            'initial_latitude' => 'required|numeric|between:-22.421,-15.609',
            'initial_altitude' => 'required|numeric|min:0|max:2000',
            'final_longitude' => 'required|numeric|between:25.237,33.056',
            'final_latitude' => 'required|numeric|between:-22.421,-15.609',
            'final_altitude' => 'required|numeric|min:0|max:2000',
            'initial_diesel' => 'required|numeric|min:0',
            'trip_diesel_allocated' => 'required|numeric|min:0',
            'top_up_diesel' => 'nullable|numeric|min:0',
            'status' => 'required|in:fulfilled,pending,in-transit,failed',
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
            'initial_diesel' => $this->input('initial_diesel', $this->input('initialDiesel')),
            'trip_diesel_allocated' => $this->input('trip_diesel_allocated', $this->input('tripDieselAllocated')),
            'top_up_diesel' => $this->input('top_up_diesel', $this->input('topUpDiesel')),
            'status' => $this->input('status', $this->input('status')),
        ]);
    }
}
