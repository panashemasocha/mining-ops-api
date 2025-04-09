<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobPositionRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255|unique:job_positions,name,' . $this->job_position,
            'role_id' => 'sometimes|exists:user_roles,id',
        ];
    }

    /**
     * Prepare the data for validation by converting camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->input('name', $this->input('name')),
            'role_id' => $this->input('role_id', $this->input('roleId')),
        ]);
    }
}
