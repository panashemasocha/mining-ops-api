<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCostPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->role->name === 'management';
    }

    public function rules()
    {
        return [
            'commodity'     => 'sometimes|string|in:loading cost,ore cost',
            'ore_type'      => 'sometimes|string|max:255',
            'quality_type'  => 'sometimes|nullable|string|max:255',
            'quality_grade' => 'sometimes|nullable|string|max:255',
            'price'         => 'sometimes|numeric|min:0',
            'date_created'  => 'sometimes|date',
            'created_by'    => 'sometimes|exists:users,id',
        ];
    }

     /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'commodity' => $this->input('commodity', $this->input('commodity')),
            'ore_type' => $this->input('ore_type', $this->input('oreType')),
            'quality_type' => $this->input('quality_type', $this->input('qualityType')),
            'quality_grade' => $this->input('quality_grade', $this->input('qualityGrade')),
            'price' => $this->input('price', $this->input('price')),
            'date_created' => $this->input('date_created', $this->input('dateCreated')),
            'created_by' => $this->input('created_by', $this->input('createdBy')),
        ]);
    }
}
