<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => 'required|regex:/^\+263\d{9}$/',
            'pin' => 'required|digits:4',
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'phone_number' => $this->input('phone_number', $this->input('phoneNumber')),
            'pin' => $this->input('pin', $this->input('pin')),
        ]);
    }
}
