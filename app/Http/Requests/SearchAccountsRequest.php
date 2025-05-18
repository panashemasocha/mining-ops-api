<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchAccountsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        return in_array($user?->role?->id, [1, 2]) || $user->department?->name === 'Finance' || in_array($user?->jobPosition?->id, [3, 6]);
    }

    /**
     * Validation rules for optional filters.
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|string',
            'name' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Convert camelCase inputs to snake_case before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'type' => $this->input('type') ?? $this->input('accountType') ?? $this->input('account_type'),
            'name' => $this->input('name') ?? $this->input('accountName') ?? $this->input('account_name'),
        ]);
    }

    /**
     * Custom error for unauthorised access.
     */
    protected function failedAuthorization()
    {
        abort(403, 'Only users from the Finance department can perform this operation');
    }
}
