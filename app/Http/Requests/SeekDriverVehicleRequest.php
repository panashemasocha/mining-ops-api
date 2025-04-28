<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeekDriverVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'ore_id' => 'required|exists:ores,id',
            'sub_type_id' => 'required|string'
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'ore_id' => $this->input('ore_id', $this->input('oreId')),
            'sub_type_id' => $this->input('sub_type_id', $this->input('vehicleSubTypeId')),
        ]);
    }
}
