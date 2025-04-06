<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOreRequest extends FormRequest
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
            'type' => 'required|string|max:255',
            'quality' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'created_by' => 'required|exists:users,id',
            'longitude' => 'required|numeric|between:25.237,33.056',
            'latitude' => 'required|numeric|between:-22.421,-15.609',
            'altitude' => 'required|numeric|min:0|max:2000',
        ];
    }
}
