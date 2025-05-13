<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOdometerReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->jobPosition->id, [5]);
    }

    public function rules(): array
    {
        // Base rules
        $rules = [
            'vehicle_id' => 'required|exists:vehicles,id',
            'trip_id' => 'nullable|exists:trips,id',
            'initial_value' => 'nullable|integer|min:0',
            'reading_unit' => 'required|in:Kilometre,Mile',
            'meter_not_working' => 'sometimes|boolean',
        ];

        // Always allow trip_end_value to be nullable, integer, min:0
        $tripEndRules = ['nullable', 'integer', 'min:0'];

        // Only enforce gt:initial_value if initial_value > 0
        if ($this->input('initial_value') !== null && (int) $this->input('initial_value') > 0) {
            $tripEndRules[] = 'gt:initial_value';
        }

        $rules['trip_end_value'] = $tripEndRules;

        return $rules;
    }

    public function messages(): array
    {
        return [
            'trip_end_value.gt' => 'The trip end value must be greater than the initial value.',
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
