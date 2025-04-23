<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDieselAllocationTypeRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'type' => 'sometimes|string|max:255|unique:diesel_allocation_types,type,' . $this->route('diesel_allocation_type')->id
        ];
    }
}
