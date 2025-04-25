<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDieselAllocationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->jobPosition->id === 7;
    }

    public function rules()
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'type_id' => 'required|exists:diesel_allocation_types,id',
            'litres' => 'required|numeric|min:1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'vehicle_id' => $this->input('vehicleId', $this->input('vehicle_id')),
            'type_id' => $this->input('typeId', $this->input('type_id')),
            'litres' => $this->input('litres', $this->input('litres')),
        ]);
    }
}
