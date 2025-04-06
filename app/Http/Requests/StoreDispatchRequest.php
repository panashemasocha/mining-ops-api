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
        return auth()->user()->jobPosition->name === 'Site Clerk';
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
            'ore_quantity_remaining' => 'required|numeric|min:0',
            'status' => 'required|in:pending,accepted,rejected',
            'payment_status' => 'required|in:fully-paid,pending,partially-paid,n/a',
        ];
    }
}
