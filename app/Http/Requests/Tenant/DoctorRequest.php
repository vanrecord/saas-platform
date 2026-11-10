<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\Doctor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor');
        $userId = null;

        if ($doctorId instanceof Doctor) {
            $userId = $doctorId->user_id;
        } elseif (is_numeric($doctorId)) {
            $doctor = Doctor::find($doctorId);
            $userId = $doctor?->user_id;
        }

        $isCreate = $this->isMethod('post');

        return [
            'username' => [
                $isCreate ? 'required' : 'required',
                'string',
                'max:255',
                $userId
                    ? Rule::unique('users', 'name')->ignore($userId)
                    : Rule::unique('users', 'name'),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                $userId
                    ? Rule::unique('users', 'email')->ignore($userId)
                    : Rule::unique('users', 'email'),
            ],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,m,f,Male,Female,M,F'],
            'date_of_birth' => ['nullable', 'date'],
            'primary_mobile' => ['nullable', 'string', 'max:50'],
            'secondary_mobile' => ['nullable', 'string', 'max:50'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['integer'],
            'description' => ['nullable', 'string'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'The user name field is required.',
            'username.unique' => 'The user name has already been taken.',
            'firstname.required' => 'The first name field is required.',
            'lastname.required' => 'The last name field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'avatar.image' => 'The avatar must be an image file (jpeg, png, jpg, gif, webp).',
            'avatar.max' => 'The avatar file size must not exceed 4MB.',
        ];
    }
}
