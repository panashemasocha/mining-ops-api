<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExcavatorUsageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->jobPosition->id === 7;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:users,id',
            'dispatch_id' => 'required|exists:dispatches,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'diesel_allocation_id' => 'required|exists:diesel_allocations,id',
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
