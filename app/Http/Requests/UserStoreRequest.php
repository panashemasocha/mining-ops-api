<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Example: Only management or executive can create users
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    public function rules()
    {
        return [
            'employee_code'    => 'required|unique:users,employee_code',
            'email'            => 'sometimes|email|max:255|nullable',
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'phone_number'     => 'required|unique:users,phone_number|regex:/^\+263\d{9}$/',
            'pin'              => 'required|digits:4',
            'status'           => 'required|in:0,1,2',
            'job_position_id'  => 'required|exists:job_positions,id',
            'branch_id'        => 'required|exists:branches,id',
            'department_id'    => 'required|exists:departments,id',
            'role_id'          => 'required|exists:user_roles,id',
            'physical_address' => 'sometimes|string|max:255',
            'date_of_birth'    => 'sometimes|date',
            'national_id'      => 'sometimes|string|max:50|nullable',
            'gender'           => 'sometimes|string|max:50|nullable',
        ];
    }
}
