<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFundingRequest extends FormRequest
{
    public function authorize()
    {
        // Only finance managers, finance directors, or executives may update/approve funding requests
        $user = auth()->user();
        return $user && (
            in_array($user->jobPosition->id, [28, 29])
            || $user->role->id === 1
        );
    }

    public function rules()
    {
        // For full PUT, all fields are required; for PATCH (or others)
        $prefix = $this->isMethod('put') ? 'required|' : 'sometimes|';

        return [
            'amount' => "{$prefix}numeric|min:0",
            'payment_method_id' => "{$prefix}exists:payment_methods,id",
            'account_id' => "{$prefix}exists:accounts,id",
            'purpose' => "{$prefix}string|max:255",
            'approval_notes' => "{$prefix}nullable|string",
            'department_id' => "{$prefix}exists:departments,id",
            'mining_site_id' => "{$prefix}exists:mining_sites,id",
            'accountant_id' => "{$prefix}exists:users,id",
            'decision_date' => "{$prefix}nullable|date",
            'status' => "{$prefix}in:pending,accepted,rejected",
        ];
    }

    protected function prepareForValidation()
    {
        $mapping = [
            'amount' => ['amount', 'amount'],
            'payment_method_id' => ['payment_method_id', 'paymentMethodId'],
            'account_id' => ['account_id', 'accountId'],
            'purpose' => ['purpose', 'purpose'],
            'approval_notes' => ['approval_notes', 'approvalNotes'],
            'department_id' => ['department_id', 'departmentId'],
            'mining_site_id' => ['mining_site_id', 'miningSiteId'],
            'accountant_id' => ['accountant_id', 'accountantId'],
            'decision_date' => ['decision_date', 'decisionDate'],
            'status' => ['status', 'status'],
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
