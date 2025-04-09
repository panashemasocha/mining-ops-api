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

    
    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'national_id' => $this->input('national_id', $this->input('nationalId')),
            'physical_address' => $this->input('physical_address', $this->input('physicalAddress')),
            'created_by' => $this->input('created_by', $this->input('createdBy')),
            'payment_method_id' => $this->input('payment_method_id', $this->input('paymentMethodId')),
            'phone_number' => $this->input('phone_number', $this->input('phoneNumber')),
        ]);
    }
}
