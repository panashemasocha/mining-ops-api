<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCostPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        $user = auth()->user();
        return $user && (
            in_array($user->role->name, ['management', 'executive'])
            || in_array($user->jobPosition->id, [3, 6])
        );
    }

    public function rules()
    {
        // For full PUT requests, fields are required; for PATCH (and others), they are validated if present.
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'commodity'      => "{$prefix}string|in:loading cost,ore cost,diesel cost",
            'ore_type'       => "{$prefix}nullable|string|max:255",
            'quality_type'   => "{$prefix}nullable|string|max:255",
            'quality_grade'  => "{$prefix}nullable|string|max:255",
            'price'          => "{$prefix}numeric|min:0",
            'created_by'     => "{$prefix}exists:users,id",
        ];
    }

    /**
     * Prepare the data for validation by normalizing camelCase inputs to snake_case.
     */
    protected function prepareForValidation()
    {
        $mapping = [
            'commodity'      => ['commodity', 'commodity'],
            'ore_type'       => ['ore_type', 'oreType'],
            'quality_type'   => ['quality_type', 'qualityType'],
            'quality_grade'  => ['quality_grade', 'qualityGrade'],
            'price'          => ['price', 'price'],
            'created_by'     => ['created_by', 'createdBy'],
        ];

        $data = [];
        foreach ($mapping as $snake => [$snakeKey, $camelKey]) {
            if ($this->has($snakeKey) || $this->has($camelKey)) {
                $data[$snake] = $this->input($snakeKey, $this->input($camelKey));
            }
        }

        $this->merge($data);
    }
}
