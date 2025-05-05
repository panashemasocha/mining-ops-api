<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->jobPosition->id, [5]);
    }

    public function rules()
    {
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'driver_id' => "{$prefix}exists:users,id",
            'vehicle_id' => "{$prefix}exists:vehicles,id",
            'dispatch_id' => "{$prefix}exists:dispatches,id",
            'ore_quantity' => "{$prefix}numeric|min:0",
            'initial_longitude' => "{$prefix}nullable|numeric|between:25.237,33.056",
            'initial_latitude' => "{$prefix}nullable|numeric|between:-22.421,-15.609",
            'initial_altitude' => "{$prefix}nullable|numeric|min:0|max:2000",
            'final_longitude' => "{$prefix}numeric|between:25.237,33.056",
            'final_latitude' => "{$prefix}numeric|between:-22.421,-15.609",
            'final_altitude' => "{$prefix}numeric|min:0|max:2000",
            'diesel_allocation_id' => "{$prefix}nullable|exists:diesel_allocations,id",
            'status' => "{$prefix}in:fulfilled,pending,in-transit,failed",
        ];
    }

    protected function prepareForValidation()
    {
        $mapping = [
            'driver_id' => ['driver_id', 'driverId'],
            'vehicle_id' => ['vehicle_id', 'vehicleId'],
            'dispatch_id' => ['dispatch_id', 'dispatchId'],
            'ore_quantity' => ['ore_quantity', 'oreQuantity'],
            'initial_longitude' => ['initial_longitude', 'initialLongitude'],
            'initial_latitude' => ['initial_latitude', 'initialLatitude'],
            'initial_altitude' => ['initial_altitude', 'initialAltitude'],
            'final_longitude' => ['final_longitude', 'finalLongitude'],
            'final_latitude' => ['final_latitude', 'finalLatitude'],
            'final_altitude' => ['final_altitude', 'finalAltitude'],
            'diesel_allocation_id' => ['diesel_allocation_id', 'dieselAllocationId'],
            'status' => ['status', 'status'],
        ];

        $data = [];
        foreach ($mapping as $snake => [$snakeKey, $camelKey]) {
            if ($this->has($snakeKey) || $this->has($camelKey)) {
                $data[$snake] = $this->input($snakeKey, $this->input($camelKey));
            }
        }

        $this->merge($data);
    }

}
