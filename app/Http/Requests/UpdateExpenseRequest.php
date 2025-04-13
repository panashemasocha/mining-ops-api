<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->department->name === 'Finance';
    }

    public function rules()
    {
        return [
            'trans_date' => 'sometimes|date',
            'description' => 'sometimes|string',
            'entries' => 'sometimes|array|min:2',
            'entries.*.account_id' => 'required_with:entries|exists:accounts,id',
            'entries.*.debit_amt' => 'nullable|numeric|min:0',
            'entries.*.credit_amt' => 'nullable|numeric|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'trans_date' => $this->input('transDate', $this->input('trans_date')),
            'description' => $this->input('description', $this->input('desc')),
            'entries' => $this->input('entries', []),
        ]);
    }

    public function messages()
    {
        return [
            'authorize' => 'Only users from the Finance department can perform this operation',
        ];
    }
}
