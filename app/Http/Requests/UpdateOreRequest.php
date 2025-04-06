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
            'quality' => 'sometimes|string|max:255',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'created_by' => 'sometimes|exists:users,id',
            'longitude' => 'sometimes|numeric|between:25.237,33.056',
            'latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'altitude' => 'sometimes|numeric|min:0|max:2000',
        ];
    }
}
