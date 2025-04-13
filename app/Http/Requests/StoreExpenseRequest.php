<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->department->name === 'Finance';
    }

    public function rules()
    {
        return [
            'trans_date'           => 'required|date',
            'description'          => 'required|string',
            'entries'              => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.debit_amt'  => 'nullable|numeric|min:0',
            'entries.*.credit_amt' => 'nullable|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert camelCase inputs to snake_case
        $this->merge([
            'trans_date'  => $this->input('transDate', $this->input('trans_date')),
            'description' => $this->input('description', $this->input('desc')),
            'entries'     => $this->input('entries', []),
        ]);
    }

    public function messages()
    {
        return [
            'authorize' => 'Only users from the Finance department can perform this operation',
        ];
    }
}
