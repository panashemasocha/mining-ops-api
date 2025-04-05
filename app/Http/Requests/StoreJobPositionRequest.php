<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPositionRequest extends FormRequest
{
    public function authorize()
    {
        $user = auth()->user();
        return $user && in_array($user->role->name, ['management', 'executive']);
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:job_positions,name',
            'role_id' => 'required|exists:user_roles,id',
        ];
    }
}
