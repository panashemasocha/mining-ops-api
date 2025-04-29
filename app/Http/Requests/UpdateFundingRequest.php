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
        return [
            'amount' => 'sometimes|numeric|min:0',
            'payment_method_id' => 'sometimes|exists:payment_methods,id',
            'account_id' => 'sometimes|exists:accounts,id',
            'purpose' => 'sometimes|string|max:255',
            'approval_notes' => 'sometimes|nullable|string',
            'department_id' => 'sometimes|exists:departments,id',
            'mining_site_id' => 'sometimes|exists:mining_sites,id',
            'accountant_id' => 'sometimes|exists:users,id',
            'decision_date' => 'sometimes|nullable|date',
            'status' => 'sometimes|in:pending,accepted,rejected',
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
}
