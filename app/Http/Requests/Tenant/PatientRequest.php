<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Gracefully support aliases from older inputs if present
        $merge = [];

        if ($this->has('first_name') && !$this->has('firstname')) {
            $merge['firstname'] = $this->input('first_name');
        }
        if ($this->has('last_name') && !$this->has('lastname')) {
            $merge['lastname'] = $this->input('last_name');
        }
        if ($this->has('date_of_birth') && !$this->has('dob')) {
            $merge['dob'] = $this->input('date_of_birth');
        }
        if ($this->has('sex') && !$this->has('gender')) {
            $merge['gender'] = $this->input('sex');
        }
        if ($this->has('occupation') && !$this->has('profession')) {
            $merge['profession'] = $this->input('occupation');
        }
        if ($this->has('card_activated')) {
            $merge['card_activated'] = filter_var($this->input('card_activated'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($this->has('is_trail_card')) {
            $merge['is_trail_card'] = filter_var($this->input('is_trail_card'), FILTER_VALIDATE_BOOLEAN);
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $patient = $this->route('patient');
        $patientId = is_object($patient) ? $patient->id : $patient;

        return [
            'id_number' => [
                'required',
                'integer',
                Rule::unique('patients', 'id_number')->ignore($patientId),
            ],
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'max:10'],
            'dob' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            'card_activated' => ['nullable', 'boolean'],
            'card_code' => ['nullable', 'string'],
            'is_trail_card' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
            'return_to' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'age' => 'Age',
            'gender' => 'Gender',
            'phone' => 'Phone',
            'address' => 'Address',
            'id_number' => 'ID Number',
            'email' => 'Email Address',
            'profession' => 'Profession',
            'dob' => 'Date of Birth',
            'balance' => 'Balance',
            'card_activated' => 'Card Activated',
            'card_code' => 'Card Code',
            'is_trail_card' => 'Trail Card',
            'avatar' => 'Patient Avatar',
        ];
    }
}
