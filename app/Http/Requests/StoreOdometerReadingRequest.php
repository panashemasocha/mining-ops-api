<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOdometerReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role->name === 'management';
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_id' => 'nullable|exists:trips,id',
            'initial_value' => 'required|integer|min:0',
            'trip_end_value' => 'required|integer|min:0',
            'reading_unit' => 'required|in:Kilometre,Mile',
            'meter_not_working' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'vehicle_id' => $this->input('vehicle_id', $this->input('vehicleId')),
            'trip_id' => $this->input('trip_id', $this->input('tripId')),
            'initial_value' => $this->input('initial_value', $this->input('initialValue')),
            'trip_end_value' => $this->input('trip_end_value', $this->input('tripEndValue')),
            'reading_unit' => $this->input('reading_unit', $this->input('readingUnit')),
            'meter_not_working' => $this->input('meter_not_working', $this->input('meterNotWorking')),
        ]);
    }
}
