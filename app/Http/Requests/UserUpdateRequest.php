<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    public function rules()
    {
        return [
            'employee_code'    => 'sometimes|unique:users,employee_code,' . $this->user,
            'email'            => 'sometimes|email|max:255|nullable',
            'first_name'       => 'sometimes|string|max:255',
            'last_name'        => 'sometimes|string|max:255',
            'phone_number'     => 'sometimes|unique:users,phone_number,' . $this->user . '|regex:/^\+263\d{9}$/',
            'pin'              => 'sometimes|digits:4',
            'status'           => 'sometimes|in:0,1,2',
            'job_position_id'  => 'sometimes|exists:job_positions,id',
            'branch_id'        => 'sometimes|exists:branches,id',
            'department_id'    => 'sometimes|exists:departments,id',
            'role_id'          => 'sometimes|exists:user_roles,id',
            'physical_address' => 'sometimes|string|max:255',
            'date_of_birth'    => 'sometimes|date',
            'national_id'      => 'sometimes|string|max:50|nullable',
            'gender'           => 'sometimes|string|max:50|nullable',
        ];
    }
}
