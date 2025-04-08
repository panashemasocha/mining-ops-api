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
