<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDispatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'ore_id' => 'required|exists:ores,id',
            'site_clerk_id' => 'required|exists:users,id',
            'loading_method' => 'nullable|string|in:manual,mechanic',
            'ore_cost_per_tonne' => 'required|numeric|min:0',
            'loading_cost_per_tonne' => 'required|numeric|min:0',
            'ore_quantity' => 'required|numeric|min:1',
            'max_quantity_per_trip' => 'required|numeric|min:1',
            'status' => 'required|in:pending,accepted,rejected',
            'payment_status' => 'required|in:fully-paid,pending,partially-paid,n/a',
            'payment_method' => 'sometimes|nullable|string|in:Cash,Bank Transfer,Ecocash',
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'ore_id' => $this->input('ore_id', $this->input('oreId')),
            'site_clerk_id' => $this->input('site_clerk_id', $this->input('siteClerkId')),
            'loading_method' => $this->input('loading_method', $this->input('loadingMethod')),
            'ore_cost_per_tonne' => $this->input('ore_cost_per_tonne', $this->input('oreCostPerTonne')),
            'loading_cost_per_tonne' => $this->input('loading_cost_per_tonne', $this->input('loadingCostPerTonne')),
            'ore_quantity' => $this->input('ore_quantity', $this->input('oreQuantityRemaining')),
            'max_quantity_per_trip' => $this->input('max_quantity_per_trip', $this->input('maxQuantityPerTrip')),
            'status' => $this->input('status', $this->input('status')),
            'payment_status' => $this->input('paymentStatus', $this->input('payment_status')),
            'payment_method' => $this->input('payment_method', $this->input('paymentMethod')),
        ]);
    }
}
