<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignedVehicleRequest extends FormRequest
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
        ]);
    }
}
