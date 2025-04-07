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
}
