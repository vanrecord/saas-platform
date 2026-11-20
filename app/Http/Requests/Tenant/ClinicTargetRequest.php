<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ClinicTargetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required'],
            'end_date' => ['required'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');

            if (!empty($startDate) && !empty($endDate)) {
                $start = strtotime($startDate);
                $end = strtotime($endDate);

                if ($start && $end && $end < $start) {
                    $validator->errors()->add('end_date', 'The End Date must not be earlier than the Start Date.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'The Start Date is required.',
            'end_date.required' => 'The End Date is required.',
            'amount.required' => 'The Target Amount is required.',
            'amount.numeric' => 'The Target Amount must be a valid number.',
            'amount.gt' => 'The Target Amount must be greater than zero.',
        ];
    }
}
