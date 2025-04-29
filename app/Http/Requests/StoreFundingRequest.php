<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFundingRequest extends FormRequest
{
    public function authorize()
    {
        // Accountant and Assistant Accountant
        $user = auth()->user();
        return $user && in_array($user->jobPosition->id, [3, 6,]);
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'account_id' => 'required|exists:accounts,id',
            'purpose' => 'required|string|max:255',
            'approval_notes' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'mining_site_id' => 'required|exists:mining_sites,id',
            'accountant_id' => 'required|exists:users,id',
            'decision_date' => 'nullable|date',
            'status' => 'required|in:pending,accepted,rejected',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'payment_method_id' => $this->input('paymentMethodId', $this->input('payment_method_id')),
            'mining_site_id' => $this->input('miningSiteId', $this->input('mining_site_id')),
            'account_id' => $this->input('accountId', $this->input('account_id')),
            'department_id' => $this->input('departmentId', $this->input('department_id')),
            'accountant_id' => $this->input('accountantId', $this->input('accountant_id')),
            'decision_date' => $this->input('decisionDate', $this->input('decision_date')),
        ]);
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Only accountants are allowed to create funding requests.'
        ], 403));
    }
}
