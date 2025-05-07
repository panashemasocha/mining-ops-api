<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user?->role?->id === 1 || $user->department?->name === 'Finance';
    }

    public function rules(): array
    {
        return [
            'invoice_id' => [
                'required',
                'integer',
                Rule::exists('gl_transactions', 'id')
                    ->where('trans_type', 'invoice')
            ],
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')
                    ->where('account_type', 'Asset'),
            ],
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.exists'  => 'Invoice id does not exist.',
            'account_id.exists'  => 'Please select a valid current asset account id to pay for the invoice #' . $this->invoice_id,
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'invoice_id' => $this->input('invoiceId') ?? $this->input('invoice_id'),
            'account_id' => $this->input('accountId') ?? $this->input('account_id'),
            'payment_date' => $this->input('paymentDate') ?? $this->input('payment_date'),
        ]);
    }

    protected function failedAuthorization()
    {
        abort(403, 'Only Finance users may record payments');
    }
}
