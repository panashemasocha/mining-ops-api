<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverInfoRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id|unique:driver_info,user_id,' . $this->driver_info,
            'license_number' => 'sometimes|string|max:20',
            'last_known_longitude' => 'sometimes|numeric|between:25.237,33.056',
            'last_known_latitude' => 'sometimes|numeric|between:-22.421,-15.609',
            'last_known_altitude' => 'sometimes|numeric|min:0|max:2000',
            'status' => 'sometimes|in:active trip,off trip',
        ];
    }
}
