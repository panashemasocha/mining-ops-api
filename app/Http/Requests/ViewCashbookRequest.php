<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewCashbookRequest extends FormRequest
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Convert camelCase inputs to snake_case before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->input('startDate', $this->input('start_date')),
            'end_date' => $this->input('endDate', $this->input('end_date')),
        ]);
    }

    /**
     * Custom error message for unauthorised access.
     */
    protected function failedAuthorization()
    {
        abort(403, 'Only users from the Finance department can perform this operation');
    }
}
