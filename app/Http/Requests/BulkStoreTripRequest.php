<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->role->name === 'management';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $storeTripRules = (new StoreTripRequest())->rules();

        $rules = [
            'trips' => 'required|array',
        ];

        // Prefix each rule with 'trips.*.'
        foreach ($storeTripRules as $field => $rule) {
            $rules['trips.*.' . $field] = $rule;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation by converting camelCase keys in each trip.
     */
    protected function prepareForValidation()
    {
        $processedTrips = [];

        foreach ($this->input('trips', []) as $trip) {
            $processedTrips[] = [
                'driver_id' => $trip['driverId'] ?? $trip['driver_id'] ?? null,
                'vehicle_id' => $trip['vehicleId'] ?? $trip['vehicle_id'] ?? null,
                'dispatch_id' => $trip['dispatchId'] ?? $trip['dispatch_id'] ?? null,
                'ore_quantity' => $trip['oreQuantity'] ?? $trip['ore_quantity'] ?? null,
                'initial_longitude' => $trip['initialLongitude'] ?? $trip['initial_longitude'] ?? null,
                'initial_latitude' => $trip['initialLatitude'] ?? $trip['initial_latitude'] ?? null,
                'initial_altitude' => $trip['initialAltitude'] ?? $trip['initial_altitude'] ?? null,
                'final_longitude' => $trip['finalLongitude'] ?? $trip['final_longitude'] ?? null,
                'final_latitude' => $trip['finalLatitude'] ?? $trip['final_latitude'] ?? null,
                'final_altitude' => $trip['finalAltitude'] ?? $trip['final_altitude'] ?? null,
                'diesel_allocation_id' => $trip['dieselAllocationId'] ?? $trip['diesel_allocation_id'] ?? null,
                'status' => $trip['status'] ?? $trip['status'] ?? null,
            ];
        }

        $this->merge(['trips' => $processedTrips]);
    }
}