<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        // Use 'required' for full PUT updates, 'sometimes' for PATCH
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'category_id' => "{$prefix}nullable|exists:vehicle_categories,id",
            'sub_type_id' => "{$prefix}nullable|exists:vehicle_sub_types,id",
            'department_id' => "{$prefix}nullable|exists:departments,id",
            'assigned_site_id' => "{$prefix}nullable|exists:sites,id",

            'reg_number' => "{$prefix}string|max:255",
            'vehicle_type' => "{$prefix}nullable|string|max:255",
            'make' => "{$prefix}nullable|string|max:255",
            'model' => "{$prefix}nullable|string|max:255",
            'year_of_manufacture' => "{$prefix}nullable|date_format:Y",
            'vin' => "{$prefix}nullable|string|size:17",

            'loading_capacity' => "{$prefix}nullable|numeric|min:0",
            'engine_hours' => "{$prefix}nullable|integer|min:0",

            'fuel_type' => "{$prefix}nullable|in:petrol,diesel,electric,hybrid",
            'acquisition_date' => "{$prefix}nullable|date",
            'next_service_date' => "{$prefix}nullable|date|after_or_equal:acquisition_date",
            'insurance_expiry_date' => "{$prefix}nullable|date|after_or_equal:today",

            'last_known_longitude' => "{$prefix}nullable|numeric|between:-180,180",
            'last_known_latitude' => "{$prefix}nullable|numeric|between:-90,90",
            'last_known_altitude' => "{$prefix}nullable|numeric|min:0",

            'status' => "{$prefix}nullable|in:active,inactive,maintenance,decommissioned,active trip,off trip",
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $mapping = [
            'category_id' => ['category_id', 'categoryId'],
            'sub_type_id' => ['sub_type_id', 'subTypeId'],
            'department_id' => ['department_id', 'departmentId'],
            'assigned_site_id' => ['assigned_site_id', 'assignedSiteId'],

            'reg_number' => ['reg_number', 'regNumber'],
            'vehicle_type' => ['vehicle_type', 'vehicleType'],
            'make' => ['make', 'make'],
            'model' => ['model', 'model'],
            'year_of_manufacture' => ['year_of_manufacture', 'yearOfManufacture'],
            'vin' => ['vin', 'vin'],

            'loading_capacity' => ['loading_capacity', 'loadingCapacity'],
            'engine_hours' => ['engine_hours', 'engineHours'],

            'fuel_type' => ['fuel_type', 'fuelType'],
            'acquisition_date' => ['acquisition_date', 'acquisitionDate'],
            'next_service_date' => ['next_service_date', 'nextServiceDate'],
            'insurance_expiry_date' => ['insurance_expiry_date', 'insuranceExpiryDate'],

            'last_known_longitude' => ['last_known_longitude', 'lastKnownLongitude'],
            'last_known_latitude' => ['last_known_latitude', 'lastKnownLatitude'],
            'last_known_altitude' => ['last_known_altitude', 'lastKnownAltitude'],
            'status' => ['status', 'status'],
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
