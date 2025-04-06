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
        return auth()->user()->role->name === 'management';
    }

    public function rules()
    {
        return [
            'driver_id' => 'sometimes|exists:users,id',
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'dispatch_id' => 'sometimes|exists:dispatches,id',
            'ore_quantity' => 'sometimes|numeric|min:0',
            'initial_longitude' => 'sometimes|numeric|between:25.237,33.056',
            'initial_latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'initial_altitude' => 'sometimes|numeric|min:0|max:2000',
            'final_longitude' => 'sometimes|numeric|between:25.237,33.056',
            'final_latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'final_altitude' => 'sometimes|numeric|min:0|max:2000',
            'initial_diesel' => 'sometimes|numeric|min:0',
            'trip_diesel_allocated' => 'sometimes|numeric|min:0',
            'top_up_diesel' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:fulfilled,pending,in-transit,failed',
        ];
    }
}
