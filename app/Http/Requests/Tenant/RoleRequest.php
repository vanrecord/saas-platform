<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
        if (is_object($roleId)) {
            $roleId = $roleId->id;
        }

        $uniqueRule = Rule::unique('roles', 'name')
            ->where('guard_name', 'tenant');

        if ($roleId) {
            $uniqueRule->ignore($roleId);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $uniqueRule,
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.unique' => 'Role name already exists.',
        ];
    }
}
