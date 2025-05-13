<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOdometerReadingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->jobPosition->id, [5]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Use 'required' for full PUT updates, 'sometimes' for PATCH
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'vehicle_id' => "{$prefix}exists:vehicles,id",
            'trip_id' => "{$prefix}nullable|exists:trips,id",
            'initial_value' => "{$prefix}integer|nullable|min:0",
            'trip_end_value' => "{$prefix}integer|nullable|min:0",
            'reading_unit' => "{$prefix}in:Kilometre,Mile",
            'meter_not_working' => "{$prefix}boolean",
        ];
    }

    // /**
    //  * Custom error messages for validation.
    //  */
    // public function messages(): array
    // {
    //     return [
    //         'trip_end_value.gt' => 'The trip end value must be greater than the initial value.',
    //     ];
    // }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation(): void
    {
        $mapping = [
            'vehicle_id' => ['vehicle_id', 'vehicleId'],
            'trip_id' => ['trip_id', 'tripId'],
            'initial_value' => ['initial_value', 'initialValue'],
            'trip_end_value' => ['trip_end_value', 'tripEndValue'],
            'reading_unit' => ['reading_unit', 'readingUnit'],
            'meter_not_working' => ['meter_not_working', 'meterNotWorking'],
        ];

        $data = [];
        foreach ($mapping as $field => [$snakeKey, $camelKey]) {
            if ($this->has($snakeKey) || $this->has($camelKey)) {
                $data[$field] = $this->input($snakeKey, $this->input($camelKey));
            }
        }

        $this->merge($data);
    }
}
