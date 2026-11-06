<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ChartToothRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dental_chart_id' => ['required', 'exists:dental_charts,id'],
            'tooth_number' => ['required', 'integer', 'between:11,85'],
            'tooth_type' => ['required', 'in:permanent,primary'],
            'tooth_status' => ['nullable', 'string', 'max:50'],
            'general_notes' => ['nullable', 'string'],
        ];
    }
}

