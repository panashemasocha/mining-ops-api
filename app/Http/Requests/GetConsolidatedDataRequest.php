<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetConsolidatedDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check(); 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'role_id' => 'required|integer|min:1|max:100',
            'id' => 'required|integer|min:1|max:100',
            'job_position_id'=>  'required|integer|min:1|max:100',
            'ores_per_page' => 'nullable|integer|min:1|max:100',
            'dispatches_per_page' => 'nullable|integer|min:1|max:100',
            'suppliers_per_page' => 'nullable|integer|min:1|max:100',
            'trips_per_page' => 'nullable|integer|min:1|max:100',
            'vehicles_per_page' => 'nullable|integer|min:1|max:100',
            'prices_per_page' => 'nullable|integer|min:1|max:100',
            'departments_per_page' => 'nullable|integer|min:1|max:100',
            'branches_per_page' => 'nullable|integer|min:1|max:100',
            'job_positions_per_page' => 'nullable|integer|min:1|max:100',
            'roles_per_page' => 'nullable|integer|min:1|max:100',
            'financials_per_page' => 'nullable|integer|min:1|max:100',

        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case if provided.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'role_id' => $this->input('role_id', $this->input('roleId')),
            'id' => $this->input('id', $this->input('id')),
            'job_position_id' => $this->input('job_position_id', $this->input('jobPositionId')),
            'ores_per_page' => $this->input('ores_per_page', $this->input('oresPerPage')),
            'dispatches_per_page' => $this->input('dispatches_per_page', $this->input('dispatchesPerPage')),
            'suppliers_per_page' => $this->input('suppliers_per_page', $this->input('suppliersPerPage')),
            'trips_per_page' => $this->input('trips_per_page', $this->input('tripsPerPage')),
            'vehicles_per_page' => $this->input('vehicles_per_page', $this->input('vehiclesPerPage')),
            'prices_per_page' => $this->input('prices_per_page', $this->input('pricesPerPage')),
            'departments_per_page' => $this->input('departments_per_page', $this->input('departmentsPerPage')),
            'branches_per_page' => $this->input('branches_per_page', $this->input('branchesPerPage')),
            'job_positions_per_page' => $this->input('job_positions_per_page', $this->input('jobPositionsPerPage')),
            'roles_per_page' => $this->input('roles_per_page', $this->input('rolesPerPage')),
            'financials_per_page' => $this->input('financials_per_page', $this->input('financialsPerPage')),
        ]);
    }
}