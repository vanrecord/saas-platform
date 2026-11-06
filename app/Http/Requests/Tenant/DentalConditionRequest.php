<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DentalConditionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $condition = $this->route('dental_condition');
        $conditionId = is_object($condition) ? $condition->id : $condition;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('dental_conditions', 'code')->ignore($conditionId),
            ],
            'description' => ['nullable', 'string'],
            'default_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

