<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->id, [1, 2, 3]);
    }

    public function rules()
    {
        // For full PUT requests, fields are required; for PATCH (and others), they are validated only if present.
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'first_name' => "{$prefix}string|max:255",
            'last_name' => "{$prefix}string|max:255",
            'national_id' => "{$prefix}string|unique:suppliers,national_id,{$this->supplier}",
            'physical_address' => "{$prefix}string|max:255",
            'created_by' => "{$prefix}exists:users,id",
            'payment_method_id' => "{$prefix}exists:payment_methods,id",
            'phone_number' => "{$prefix}string|unique:suppliers,phone_number,{$this->supplier}|regex:/^\\+263\\d{9}$/",
        ];
    }

    /**
     * Prepare the data for validation by normalizing camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $mapping = [
            'first_name' => ['first_name', 'firstName'],
            'last_name' => ['last_name', 'lastName'],
            'national_id' => ['national_id', 'nationalId'],
            'physical_address' => ['physical_address', 'physicalAddress'],
            'created_by' => ['created_by', 'createdBy'],
            'payment_method_id' => ['payment_method_id', 'paymentMethodId'],
            'phone_number' => ['phone_number', 'phoneNumber'],
        ];

        $data = [];
        foreach ($mapping as $snake => [$snakeKey, $camelKey]) {
            if ($this->has($snakeKey) || $this->has($camelKey)) {
                $data[$snake] = $this->input($snakeKey, $this->input($camelKey));
            }
        }

        $this->merge($data);
    }
}
