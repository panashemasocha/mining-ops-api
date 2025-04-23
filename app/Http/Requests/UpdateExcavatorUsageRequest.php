<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExcavatorUsageRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'driver_id' => 'sometimes|exists:users,id',
            'dispatch_id' => 'sometimes|exists:dispatches,id',
            'start' => 'sometimes|date',
            'end' => 'sometimes|date|after:start',
            'diesel_allocation_id' => 'sometimes|exists:diesel_allocations,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'vehicle_id' => $this->input('vehicleId', $this->input('vehicle_id')),
            'driver_id' => $this->input('driverId', $this->input('driver_id')),
            'dispatch_id' => $this->input('dispatchId', $this->input('dispatch_id')),
            'diesel_allocation_id' => $this->input('dieselAllocationId', $this->input('diesel_allocation_id')),
        ]);
    }
}
