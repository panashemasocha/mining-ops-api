<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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

            'reg_number' => 'required|string|max:255',
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

    protected function prepareForValidation()
    {
        $this->merge([
            'category_id' => $this->input('category_id', $this->input('categoryId')),
            'sub_type_id' => $this->input('sub_type_id', $this->input('subTypeId')),
            'department_id' => $this->input('department_id', $this->input('departmentId')),
            'assigned_site_id' => $this->input('assigned_site_id', $this->input('assignedSiteId')),

            'reg_number' => $this->input('reg_number', $this->input('regNumber')),
            'vehicle_type' => $this->input('vehicle_type', $this->input('vehicleType')),
            'make' => $this->input('make', $this->input('make')),
            'model' => $this->input('model', $this->input('model')),
            'year_of_manufacture' => $this->input('year_of_manufacture', $this->input('yearOfManufacture')),
            'vin' => $this->input('vin', $this->input('vin')),

            'loading_capacity' => $this->input('loading_capacity', $this->input('loadingCapacity')),
            'engine_hours' => $this->input('engine_hours', $this->input('engineHours')),

            'fuel_type' => $this->input('fuel_type', $this->input('fuelType')),
            'acquisition_date' => $this->input('acquisition_date', $this->input('acquisitionDate')),
            'next_service_date' => $this->input('next_service_date', $this->input('nextServiceDate')),
            'insurance_expiry_date' => $this->input('insurance_expiry_date', $this->input('insuranceExpiryDate')),

            'last_known_longitude' => $this->input('last_known_longitude', $this->input('lastKnownLongitude')),
            'last_known_latitude' => $this->input('last_known_latitude', $this->input('lastKnownLatitude')),
            'last_known_altitude' => $this->input('last_known_altitude', $this->input('lastKnownAltitude')),
            'status' => $this->input('status', $this->input('status')),
        ]);
    }
}
