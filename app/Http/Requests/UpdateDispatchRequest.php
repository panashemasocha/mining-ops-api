<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDispatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->jobPosition->name === 'Site Clerk';
    }

    public function rules()
    {
        return [
            'ore_id' => 'sometimes|exists:ores,id',
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'site_clerk_id' => 'sometimes|exists:users,id',
            'loading_method' => 'sometimes|nullable|string|in:manual,mechanic',
            'ore_cost_per_tonne' => 'sometimes|numeric|min:0',
            'loading_cost_per_tonne' => 'sometimes|numeric|min:0',
            'ore_quantity_remaining' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,accepted,rejected',
            'payment_status' => 'sometimes|in:fully-paid,pending,partially-paid,n/a',
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
            'ore_quantity_remaining' => $this->input('ore_quantity_remaining', $this->input('oreQuantityRemaining')),
            'status' => $this->input('status', $this->input('status')),
            'payment_status' => $this->input('payment_status', $this->input('paymentStatus')),
        ]);
    }
}
