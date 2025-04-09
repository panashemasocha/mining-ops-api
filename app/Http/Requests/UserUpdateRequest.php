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
            'employee_code' => 'sometimes|unique:users,employee_code,' . $this->user,
            'email' => 'sometimes|email|max:255|nullable',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|unique:users,phone_number,' . $this->user . '|regex:/^\+263\d{9}$/',
            'pin' => 'sometimes|digits:4',
            'status' => 'sometimes|in:0,1,2',
            'job_position_id' => 'sometimes|exists:job_positions,id',
            'branch_id' => 'sometimes|exists:branches,id',
            'department_id' => 'sometimes|exists:departments,id',
            'role_id' => 'sometimes|exists:user_roles,id',
            'physical_address' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date',
            'national_id' => 'sometimes|string|max:50|nullable',
            'gender' => 'sometimes|string|max:50|nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'employee_code' => $this->input('employee_code', $this->input('employeeCode')),
            'email' => $this->input('employee_code', $this->input('email')),
            'first_name' => $this->input('first_name', $this->input('firstName')),
            'last_name' => $this->input('last_name', $this->input('lastName')),
            'phone_number' => $this->input('phone_number', $this->input('phoneNumber')),
            'job_position_id' => $this->input('job_position_id', $this->input('jobPositionId')),
            'branch_id' => $this->input('branch_id', $this->input('branchId')),
            'department_id' => $this->input('department_id', $this->input('departmentId')),
            'role_id' => $this->input('role_id', $this->input('roleId')),
            'physical_address' => $this->input('physical_address', $this->input('physicalAddress')),
            'date_of_birth' => $this->input('date_of_birth', $this->input('dateOfBirth')),
            'national_id' => $this->input('national_id', $this->input('nationalId')),
            'gender' => $this->input('gender', $this->input('gender')),
            'pin' => $this->input('pin', $this->input('pin')),
            'status' => $this->input('status', $this->input('status')),
        ]);
    }
}
