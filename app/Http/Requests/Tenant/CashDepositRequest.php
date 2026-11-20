<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CashDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'date' => ['nullable'],
            'code' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'max:10'],
            'rate' => ['nullable', 'numeric'],
            'is_cash' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'amount.required' => 'The deposit amount is required.',
            'amount.numeric' => 'The deposit amount must be a valid number.',
            'amount.gt' => 'The deposit amount must be greater than zero.',
        ];
    }
}
