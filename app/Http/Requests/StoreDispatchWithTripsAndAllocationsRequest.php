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
        // 1. Dispatch rules 
        $dispatchRules = (new StoreDispatchRequest())->rules();
        $prefixedDispatchRules = [];
        foreach ($dispatchRules as $field => $rule) {
            $prefixedDispatchRules["dispatch.$field"] = $rule;
        }

        // 2. Trip rules 
        $tripRules = collect((new StoreTripRequest())->rules())
            ->except(['dispatch_id'])
            ->toArray();

        $prefixedTripRules = [];
        foreach ($tripRules as $field => $rule) {
            $prefixedTripRules["trips.*.$field"] = $rule;
        }

        // 3. Diesel allocation rules
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
        // Prepare dispatch data
        $dispatchData = $this->input('dispatch', []);
        $dispatchRequest = new StoreDispatchRequest();
        $dispatchRequest->merge($dispatchData);
        $dispatchRequest->prepareForValidation();
        $this->merge(['dispatch' => $dispatchRequest->all()]);

        // Prepare trips data 
        $tripsData = $this->input('trips', []);
        foreach ($tripsData as $index => $trip) {
            $tripRequest = new StoreTripRequest();
            $tripRequest->merge($trip);
            $tripRequest->prepareForValidation();
            $validatedTrip = $tripRequest->all();
            unset($validatedTrip['dispatch_id']);
            $tripsData[$index] = $validatedTrip;
        }
        $this->merge(['trips' => $tripsData]);

        // Prepare diesel allocations
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