<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispatchWithTripsAndAllocationsRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && $user->jobPosition->id === 7;
    }

    public function rules()
    {
        // 1. Dispatch rules (prefixed with "dispatch.")
        $dispatchRules = (new StoreDispatchRequest())->rules();
        $prefixedDispatchRules = [];
        foreach ($dispatchRules as $field => $rule) {
            $prefixedDispatchRules["dispatch.$field"] = $rule;
        }

        // 2. Bulk Trip rules (prefixed with "trips.*.")
        $tripRules = (new StoreTripRequest())->rules();
        $prefixedTripRules = [];
        foreach ($tripRules as $field => $rule) {
            $prefixedTripRules["trips.*.$field"] = $rule;
        }

        // 3. Bulk Diesel Allocation rules (prefixed with "dieselAllocations.*.")
        $dieselRules = (new StoreDieselAllocationRequest())->rules();
        $prefixedDieselRules = [];
        foreach ($dieselRules as $field => $rule) {
            $prefixedDieselRules["dieselAllocations.*.$field"] = $rule;
        }

        return array_merge(
            [
                'dispatch' => 'required|array',
                'trips' => 'required|array|min:1',
                'dieselAllocations' => 'nullable|array',
            ],
            $prefixedDispatchRules,
            $prefixedTripRules,
            $prefixedDieselRules
        );
    }

    protected function prepareForValidation()
    {
        // Prepare dispatch data (camelCase - snake_case)
        $dispatchData = $this->input('dispatch', []);
        $dispatchRequest = new StoreDispatchRequest();
        $dispatchRequest->merge($dispatchData);
        $dispatchRequest->prepareForValidation();
        $this->merge(['dispatch' => $dispatchRequest->all()]);

        // Prepare trips data (camelCase - snake_case)
        $tripsData = $this->input('trips', []);
        foreach ($tripsData as $index => $trip) {
            $tripRequest = new StoreTripRequest();
            $tripRequest->merge($trip);
            $tripRequest->prepareForValidation();
            $tripsData[$index] = $tripRequest->all();
        }
        $this->merge(['trips' => $tripsData]);

        // Prepare diesel allocations (camelCase - snake_case)
        $dieselData = $this->input('dieselAllocations', []);
        foreach ($dieselData as $index => $allocation) {
            $dieselRequest = new StoreDieselAllocationRequest();
            $dieselRequest->merge($allocation);
            $dieselRequest->prepareForValidation();
            $dieselData[$index] = $dieselRequest->all();
        }
        $this->merge(['dieselAllocations' => $dieselData]);
    }
}