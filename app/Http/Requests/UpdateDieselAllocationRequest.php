<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDieselAllocationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'type_id' => 'sometimes|exists:diesel_allocation_types,id',
            'litres' => 'sometimes|numeric|min:1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'vehicle_id' => $this->input('vehicleId', $this->input('vehicle_id')),
            'type_id' => $this->input('allocationTypeId', $this->input('type_id')),
            'litres' => $this->input('litres', $this->input('litres')),
        ]);
    }
}
