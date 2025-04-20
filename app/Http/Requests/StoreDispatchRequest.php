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
            'vehicle_id' => 'required|exists:vehicles,id',
            'site_clerk_id' => 'required|exists:users,id',
            'loading_method' => 'nullable|string|in:manual,mechanic',
            'ore_cost_per_tonne' => 'required|numeric|min:0',
            'loading_cost_per_tonne' => 'required|numeric|min:0',
            'ore_quantity' => 'required|numeric|min:0',
            'status' => 'required|in:pending,accepted,rejected',
            'payment_status' => 'required|in:fully-paid,pending,partially-paid,n/a',
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'ore_id' => $this->input('ore_id', $this->input('oreId')),
            'vehicle_id' => $this->input('vehicle_id', $this->input('vehicleId')),
            'site_clerk_id' => $this->input('site_clerk_id', $this->input('siteClerkId')),
            'loading_method' => $this->input('loading_method', $this->input('loadingMethod')),
            'ore_cost_per_tonne' => $this->input('ore_cost_per_tonne', $this->input('oreCostPerTonne')),
            'loading_cost_per_tonne' => $this->input('loading_cost_per_tonne', $this->input('loadingCostPerTonne')),
            'ore_quantity' => $this->input('ore_quantity', $this->input('oreQuantityRemaining')),
            'status' => $this->input('status', $this->input('status')),
            'payment_status' => $this->input('payment_status', $this->input('paymentStatus')),
        ]);
    }
}
