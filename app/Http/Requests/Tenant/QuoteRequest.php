<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'assistant_id' => ['nullable'],
            'employee_id' => ['nullable'],
            'code' => ['required'],
            'estimate_at' => ['required'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'discount_invoice' => ['nullable', 'numeric', 'min:0'],
            'discount_inv_as' => ['nullable', 'string', 'in:%,$'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'medical_alert' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'chief_complaint' => ['nullable', 'string', 'max:255'],
            'term' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.service_id' => ['nullable'],
            'items.*.detailable_id' => ['nullable'],
            'items.*.item_type' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.cost' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_as' => ['nullable', 'string', 'in:%,$'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.date' => ['nullable'],
            'items.*.note' => ['nullable', 'string'],
            'items.*.teeth' => ['nullable'],
            'odontogram_findings' => ['nullable', 'array'],
            'odontogram_findings.*' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Please select a patient.',
            'patient_id.exists' => 'The selected patient does not exist.',
            'code.required' => 'Code No is required.',
            'estimate_at.required' => 'Daily Date is required.',
            'items.required' => 'Please add at least one service or product to the quote.',
            'items.min' => 'Please add at least one service or product to the quote.',
        ];
    }
}
