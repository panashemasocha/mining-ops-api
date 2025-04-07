<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCostPriceRequest extends FormRequest
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
            'commodity'     => 'required|string|in:loading cost,ore cost',
            'ore_type'      => 'required|string|max:255',
            'quality_type'  => 'sometimes|nullable|string|max:255',
            'quality_grade' => 'sometimes|nullable|string|max:255',
            'price'         => 'required|numeric|min:0',
            'date_created'  => 'required|date',
            'created_by'    => 'required|exists:users,id',
        ];
    }
}
