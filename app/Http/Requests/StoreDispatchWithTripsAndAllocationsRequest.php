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
        // Dispatch validation rules 
        $dispatchRules = (new StoreDispatchRequest())->rules();
        $prefixedDispatchRules = [];
        foreach ($dispatchRules as $field => $rule) {
            $prefixedDispatchRules["dispatch.$field"] = $rule;
        }

        // Bulk trip rules (required)
        $bulkTripRules = (new BulkStoreTripRequest())->rules();

        // Bulk diesel allocation rules (optional)
        $bulkDieselRules = (new BulkStoreDieselAllocationRequest())->rules();
        $bulkDieselRules['dieselAllocations'] = 'nullable|array';

        return array_merge(
            ['dispatch' => 'required|array'],
            $prefixedDispatchRules,
            $bulkTripRules,
            $bulkDieselRules
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
        $bulkTripRequest = new BulkStoreTripRequest();
        $bulkTripRequest->merge(['trips' => $tripsData]);
        $bulkTripRequest->prepareForValidation();
        $this->merge(['trips' => $bulkTripRequest->input('trips')]);

        // Prepare diesel allocations (only if present)
        if ($this->has('dieselAllocations')) {
            $dieselData = $this->input('dieselAllocations', []);
            $bulkDieselRequest = new BulkStoreDieselAllocationRequest();
            $bulkDieselRequest->merge(['dieselAllocations' => $dieselData]);
            $bulkDieselRequest->prepareForValidation();
            $this->merge(['dieselAllocations' => $bulkDieselRequest->input('dieselAllocations')]);
        }
    }
}