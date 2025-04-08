<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverInfoRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id|unique:driver_info,user_id',
            'license_number' => 'nullable|string|max:20',
            'last_known_longitude' => 'nullable|numeric|between:25.237,33.056', 
            'last_known_latitude' => 'nullable|numeric|between:-22.421,-15.609', 
            'last_known_altitude' => 'nullable|numeric|min:0|max:2000', 
            'status' => 'required|in:active trip,off trip',
        ];
    }

       /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id'              => $this->input('user_id', $this->input('userId')),
            'license_number'       => $this->input('license_number', $this->input('licenseNumber')),
            'last_known_longitude' => $this->input('last_known_longitude', $this->input('lastKnownLongitude')),
            'last_known_latitude'  => $this->input('last_known_latitude', $this->input('lastKnownLatitude')),
            'last_known_altitude'  => $this->input('last_known_altitude', $this->input('lastKnownAltitude')),
            'status'               => $this->input('status', $this->input('status')),
        ]);
    }
}
