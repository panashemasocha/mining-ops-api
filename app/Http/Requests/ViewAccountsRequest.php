<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class   ViewAccountsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user?->role?->id == 1 || $user->department?->name === 'Finance';
    }

    /**
     * Rules for optional filtering (e.g., date range).
     */
    public function rules(): array
    {
        return [
            'account_type' => 'nullable|string',
        ];
    }

    /**
     * Convert camelCase inputs to snake_case before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'account_type' => $this->input('account_type', $this->input('accountType')),
        ]);
    }

    /**
     * Custom error message for unauthorized access.
     */
    protected function failedAuthorization()
    {
        abort(403, 'Only users from the Finance department can perform this operation');
    }
}
