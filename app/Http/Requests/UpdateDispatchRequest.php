<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDispatchRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'ore_id' => 'sometimes|exists:ores,id',
            'site_clerk_id' => 'sometimes|exists:users,id',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'loading_method' => 'sometimes|nullable|string|in:manual,mechanical',
            'ore_cost_per_tonne' => 'sometimes|numeric|min:0',
            'loading_cost_per_tonne' => 'sometimes|numeric|min:0',
            'ore_quantity' => 'sometimes|numeric|min:1',
            'max_quantity_per_trip' => 'sometimes|numeric|min:1',
            'status' => 'sometimes|in:pending,accepted,rejected',
            'payment_status' => 'sometimes|in:fully-paid,pending,partially-paid,n/a',
            'payment_method' => 'sometimes|nullable|string|in:Cash,Bank Transfer,Ecocash',

        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'ore_id' => $this->input('oreId', $this->input('ore_id')),
            'site_clerk_id' => $this->input('siteClerkId', $this->input('site_clerk_id')),
            'supplier_id' => $this->input('supplierId', $this->input('supplier_id')),
            'loading_method' => $this->input('loadingMethod', $this->input('loading_method')),
            'ore_cost_per_tonne' => $this->input('oreCostPerTonne', $this->input('ore_cost_per_tonne')),
            'loading_cost_per_tonne' => $this->input('loadingCostPerTonne', $this->input('loading_cost_per_tonne')),
            'ore_quantity' => $this->input('oreQuantity', $this->input('ore_quantity')),
            'max_quantity_per_trip' => $this->input('max_quantity_per_trip', $this->input('maxQuantityPerTrip')),
            'status' => $this->input('status', $this->input('status')),
            'payment_status' => $this->input('paymentStatus', $this->input('payment_status')),
            'payment_method' => $this->input('payment_method', $this->input('paymentMethod')),
        ]);
    }
}
