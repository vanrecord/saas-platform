<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'medical_history_id' => ['nullable', 'integer'],
            'prescription' => ['nullable', 'string'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.medicine_id' => ['required', 'integer', 'exists:medicines,id'],
            'details.*.dose' => ['nullable', 'numeric', 'min:0'],
            'details.*.qty' => ['required', 'numeric', 'min:0.01'],
            'details.*.ud' => ['required', 'numeric', 'min:0.01'],
            'details.*.morning' => ['nullable', 'boolean'],
            'details.*.afternoon' => ['nullable', 'boolean'],
            'details.*.evening' => ['nullable', 'boolean'],
            'details.*.night' => ['nullable', 'boolean'],
            'details.*.note' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'patient_id' => 'Patient',
            'medical_history_id' => 'Medical History',
            'details' => 'Medicine Details',
            'details.*.medicine_id' => 'Medicine',
            'details.*.dose' => 'Dose',
            'details.*.qty' => 'Quantity',
            'details.*.ud' => 'U/D',
            'details.*.note' => 'Note',
        ];
    }
}
