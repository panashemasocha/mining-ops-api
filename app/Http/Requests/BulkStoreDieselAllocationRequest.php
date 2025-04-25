<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkStoreDieselAllocationRequest extends FormRequest
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
        $storeDieselAllocationRules = (new StoreDieselAllocationRequest())->rules();

        $rules = [
            'dieselAllocations' => 'required|array',
        ];

        // Prefix each rule with 'dieselAllocations.*.'
        foreach ($storeDieselAllocationRules as $field => $rule) {
            $rules['dieselAllocations.*.' . $field] = $rule;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation by converting camelCase keys in each diesel Allocation.
     */
    protected function prepareForValidation()
    {
        $processedDieselAllocations = [];

        foreach ($this->input('dieselAllocations', []) as $dieselAllocation) {
            $processedDieselAllocations[] = [
                'vehicle_id' => $dieselAllocation['vehicleId'] ?? $dieselAllocation['vehicle_id'] ?? null,
                'type_id' => $dieselAllocation['typeId'] ?? $dieselAllocation['type_id'] ?? null,
                'litres' => $dieselAllocation['litres'] ?? $dieselAllocation['litres'] ?? null,
            ];
        }

        $this->merge(['dieselAllocations' => $processedDieselAllocations]);
    }
}