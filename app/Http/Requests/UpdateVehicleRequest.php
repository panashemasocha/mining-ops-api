<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    public function rules()
    {
        return [
            'reg_number' => 'sometimes|string|max:255',
            'vehicle_type' => 'sometimes|string|max:255',
            'loading_capacity' => 'sometimes|numeric|min:0',
            'last_known_longitude' => 'sometimes|numeric|between:-180,180',
            'last_known_latitude' => 'sometimes|numeric|between:-90,90',
            'last_known_altitude' => 'sometimes|numeric',
            'status' => 'sometimes|in:active trip,off trip',
        ];
    }


    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'reg_number' => $this->input('reg_number', $this->input('regNumber')),
            'vehicle_type' => $this->input('vehicle_type', $this->input('vehicleType')),
            'loading_capacity' => $this->input('loading_capacity', $this->input('loadingCapacity')),
            'last_known_longitude' => $this->input('last_known_longitude', $this->input('lastKnownLongitude')),
            'last_known_latitude' => $this->input('last_known_latitude', $this->input('lastKnownLatitude')),
            'last_known_altitude' => $this->input('last_known_altitude', $this->input('lastKnownAltitude')),
            'status' => $this->input('status', $this->input('status')),
        ]);
    }
}
