<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */ public function authorize()
    {
        return auth()->user()->role->name === 'management';
    }

    public function rules()
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'national_id' => 'sometimes|string|unique:suppliers,national_id,' . $this->supplier,
            'physical_address' => 'sometimes|string|max:255',
            'created_by' => 'sometimes|exists:users,id',
            'payment_method_id' => 'sometimes|exists:payment_methods,id',
            'phone_number' => 'sometimes|string|unique:suppliers,phone_number,' . $this->supplier . '|regex:/^\+263\d{9}$/',
        ];
    }
}
