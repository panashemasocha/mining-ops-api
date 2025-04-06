<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'required|string|unique:suppliers,national_id',
            'physical_address' => 'required|string|max:255',
            'created_by' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'phone_number' => 'required|string|unique:suppliers,phone_number|regex:/^\+263\d{9}$/',
        ];
    }
}
