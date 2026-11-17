<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class InvoicePrefixRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->has('prefix_format')) {
            $this->merge([
                'prefix_format' => strtolower(trim((string) $this->input('prefix_format'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'prefix_name' => ['required', 'string', 'max:50'],
            'prefix_format' => ['required', 'string', 'in:series,year-series'],
            'default_prefix' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'prefix_name.required' => 'The Prefix Name is required.',
            'prefix_format.required' => 'The Format is required.',
            'prefix_format.in' => 'Format must be either series or year-series.',
        ];
    }
}
