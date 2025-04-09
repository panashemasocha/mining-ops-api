<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->jobPosition->name === 'Quality Controller';
    }

    public function rules()
    {
        return [
            'type' => 'sometimes|string|max:255',
            'quality_type' => 'sometimes|string|max:255',
            'quality_grade' => 'sometimes|string|in:A,B,C,High,Medium,Low|max:255',
            'quantity' => 'sometimes|numeric|min:1',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'created_by' => 'sometimes|exists:users,id',
            'location_name' => 'sometimes|nullable|string|max:255',
            'longitude' => 'sometimes|numeric|between:25.237,33.056',
            'latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'altitude' => 'sometimes|numeric|min:0|max:2000',
        ];
    }

    
    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'type' => $this->input('type', $this->input('type')),
            'quality_type' => $this->input('quality_type', $this->input('qualityType')),
            'quality_grade' => $this->input('quality_grade', $this->input('qualityGrade')),
            'quantity' => $this->input('quantity', $this->input('quantity')),
            'supplier_id' => $this->input('supplier_id', $this->input('supplierId')),
            'created_by' => $this->input('created_by', $this->input('createdBy')),
            'location_name' => $this->input('location_name', $this->input('locationName')),
            'longitude' => $this->input('longitude', $this->input('longitude')),
            'latitude' => $this->input('latitude', $this->input('latitude')),
            'altitude' => $this->input('altitude', $this->input('altitude')),
        ]);
    }
}
