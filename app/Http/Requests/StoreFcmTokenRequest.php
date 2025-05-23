<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFcmTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Define the validation rules.
     */
    public function rules()
    {
        return [
            'device_type' => 'required|in:android,ios',
            'token' => 'required|string'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id(),
            'device_type' => $this->input('device_type', $this->input('deviceType')),
        ]);
    }
}
