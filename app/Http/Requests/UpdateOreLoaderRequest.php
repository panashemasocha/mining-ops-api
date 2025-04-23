<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOreLoaderRequest extends FormRequest
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
            'loaders' => 'sometimes|numeric|min:1',
            'trip_id' => 'sometimes|exists:trips,id',
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'loaders' => $this->input('loaders', $this->input('loaders')),
            'trip_id' => $this->input('trip_id', $this->input('tripId')),
        ]);
    }
}
