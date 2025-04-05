<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverInfoRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id|unique:driver_info,user_id',
            'license_number' => 'nullable|string|max:20',
            'last_known_longitude' => 'nullable|numeric|between:25.237,33.056', 
            'last_known_latitude' => 'nullable|numeric|between:-22.421,-15.609', 
            'last_known_altitude' => 'nullable|numeric|min:0|max:2000', 
            'status' => 'required|in:active trip,off trip',
        ];
    }
}
