<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            // Authentication
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore($userId),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],

            // e-Health Yoeung
            'is_publish' => ['nullable', 'boolean'],
            'ehealth_is_doctor_appointment' => ['nullable', 'boolean'],
            'consult_video' => ['nullable', 'boolean'],
            'consult_video_fee' => ['nullable'],
            'consult_visit' => ['nullable', 'boolean'],
            'consult_visit_fee' => ['nullable'],
            'consult_house' => ['nullable', 'boolean'],
            'consult_house_fee' => ['nullable'],
            'ehealth_enable_video_consult' => ['nullable', 'boolean'],
            'ehealth_video_consult_fee' => ['nullable'],
            'ehealth_enable_visit_consult' => ['nullable', 'boolean'],
            'ehealth_visit_consult_fee' => ['nullable'],
            'ehealth_enable_house_call' => ['nullable', 'boolean'],
            'ehealth_house_call_fee' => ['nullable'],

            // User Profile
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,m,f,Male,Female,M,F'],
            'date_of_birth' => ['nullable', 'date'],
            'primary_mobile' => ['nullable', 'string', 'max:50'],
            'secondary_mobile' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'User name',
            'email' => 'Email',
            'role' => 'Role',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'primary_mobile' => 'Phone 1',
            'secondary_mobile' => 'Phone 2',
            'avatar' => 'Avatar',
            'consult_video_fee' => 'Video Consult Fee',
            'consult_visit_fee' => 'Visit Consult Fee',
            'consult_house_fee' => 'House Call Fee',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'User name is required.',
            'username.unique' => 'User name already exists.',
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be valid.',
            'email.unique' => 'Email already exists.',
            'firstname.required' => 'First name is required.',
            'lastname.required' => 'Last name is required.',
        ];
    }
}
