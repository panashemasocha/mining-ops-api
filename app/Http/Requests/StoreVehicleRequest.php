<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
            'reg_number' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:255',
            'loading_capacity' => 'nullable|numeric|min:0',
            'last_known_longitude' => 'nullable|numeric|between:-180,180',
            'last_known_latitude' => 'nullable|numeric|between:-90,90',
            'last_known_altitude' => 'nullable|numeric',
            'status' => 'required|in:active trip,off trip',
        ];
    }
}
