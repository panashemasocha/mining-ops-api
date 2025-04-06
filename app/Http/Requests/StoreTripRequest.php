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
}
