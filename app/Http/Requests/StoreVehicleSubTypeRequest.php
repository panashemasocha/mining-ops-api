<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleSubTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:vehicleCategories,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('name', $this->input('name')),
            'category_id' => $this->input('category_id', $this->input('categoryId')),
        ]);
    }
}
