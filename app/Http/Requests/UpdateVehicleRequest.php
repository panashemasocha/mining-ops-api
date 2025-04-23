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

    public function rules()
    {
        return [
            'category_id' => 'nullable|exists:vehicle_categories,id',
            'sub_type_id' => 'nullable|exists:vehicle_sub_types,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_site_id' => 'nullable|exists:sites,id',

            'reg_number' => 'sometimes|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year_of_manufacture' => 'nullable|date_format:Y',
            'vin' => 'nullable|string|size:17',

            'loading_capacity' => 'nullable|numeric|min:0',
            'engine_hours' => 'nullable|integer|min:0',

            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid',
            'acquisition_date' => 'nullable|date',
            'next_service_date' => 'nullable|date|after_or_equal:acquisition_date',
            'insurance_expiry_date' => 'nullable|date|after_or_equal:today',

            'last_known_longitude' => 'nullable|numeric|between:-180,180',
            'last_known_latitude' => 'nullable|numeric|between:-90,90',
            'last_known_altitude' => 'nullable|numeric|min:0',

            'status' => 'nullable|in:active,inactive,maintenance,decommissioned,active trip,off trip',
       ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge((new StoreVehicleRequest())->prepareForValidation());
    }
}
