<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'patient_id' => !empty($this->patient_id) ? (int) $this->patient_id : null,
            'doctor_id' => !empty($this->doctor_id) ? (int) $this->doctor_id : null,
            'user_id' => !empty($this->user_id) ? (int) $this->user_id : null,
            'assistant_id' => !empty($this->assistant_id) ? (int) $this->assistant_id : null,
            'estimate_id' => !empty($this->estimate_id) ? (int) $this->estimate_id : null,
            'prefix_id' => !empty($this->prefix_id) ? (int) $this->prefix_id : null,
            'discount_invoice' => ($this->discount_invoice !== null && $this->discount_invoice !== '') ? (float) $this->discount_invoice : 0.0,
            'rate' => ($this->rate !== null && $this->rate !== '') ? (float) $this->rate : 4050.0,
        ]);
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'date' => ['required', 'date'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'discount_invoice' => ['nullable', 'numeric', 'min:0'],
            'discount_inv_as' => ['nullable', 'string', 'in:%,$'],
            'visit_type' => ['nullable', 'string', 'max:50'],
            'diagnosis' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'medical_alert' => ['nullable', 'string'],
            'prefix_id' => ['nullable', 'exists:prefixs,id'],
            'estimate_id' => ['nullable', 'integer'],

            'examination_notes' => ['nullable', 'string'],
            'odontogram_findings' => ['nullable', 'array'],
            'odontogram_findings.*.tooth' => ['required_with:odontogram_findings', 'string'],
            'odontogram_findings.*.surface' => ['required_with:odontogram_findings', 'string'],
            'odontogram_findings.*.condition_id' => ['nullable'],
            'odontogram_findings.*.dental_condition_id' => ['nullable'],
            'odontogram_findings.*.condition_code' => ['nullable', 'string'],
            'odontogram_findings.*.color' => ['nullable', 'string'],
            'odontogram_findings.*.status' => ['nullable', 'string'],
            'odontogram_findings.*.treatment_status' => ['nullable', 'string'],
            'odontogram_findings.*.note' => ['nullable', 'string'],
            'odontogram_findings.*.service_id' => ['nullable'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable'],
            'items.*.item_type' => ['required', 'string', 'in:service,product'],
            'items.*.detailable_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.cost' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_as' => ['nullable', 'string', 'in:%,$'],
            'items.*.date' => ['nullable'],
            'items.*.teeth' => ['nullable', 'array'],
            'items.*.teeth.*.teeth_number' => ['nullable'],
            'items.*.teeth.*.tooth' => ['nullable'],
            'items.*.teeth.*.class_one' => ['nullable', 'boolean'],
            'items.*.teeth.*.class_two' => ['nullable', 'boolean'],
            'items.*.teeth.*.class_three' => ['nullable', 'boolean'],
            'items.*.teeth.*.class_four' => ['nullable', 'boolean'],
            'items.*.teeth.*.class_five' => ['nullable', 'boolean'],
            'items.*.teeth.*.class_six' => ['nullable', 'boolean'],
            'items.*.teeth.*.tooth_type' => ['nullable', 'string'],
            'items.*.teeth.*.tooth_status' => ['nullable', 'string'],
            'items.*.teeth.*.surfaces' => ['nullable', 'array'],
            'items.*.teeth.*.findings' => ['nullable', 'array'],
            'items.*.teeth.*.findings.*.surface' => ['nullable', 'string'],
            'items.*.teeth.*.findings.*.dental_condition_id' => ['nullable'],
            'items.*.teeth.*.findings.*.condition_id' => ['nullable'],
            'items.*.teeth.*.findings.*.custom_color' => ['nullable', 'string'],
            'items.*.teeth.*.findings.*.color' => ['nullable', 'string'],
            'items.*.teeth.*.findings.*.note' => ['nullable', 'string'],
            'items.*.teeth.*.findings.*.treatment_status' => ['nullable', 'string'],
            'items.*.teeth.*.findings.*.status' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a Patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'items.required' => 'Please add at least one Service or Product.',
            'items.min' => 'Please add at least one Service or Product.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
