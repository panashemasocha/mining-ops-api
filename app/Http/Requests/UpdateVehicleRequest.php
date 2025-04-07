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
        return auth()->user()->role === 'management';
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
}
