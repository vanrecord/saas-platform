<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Actions\Fortify\PasswordValidationRules;

class StoreDomainRequest extends FormRequest
{
    use PasswordValidationRules;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company' => ['required', 'max:100'],
            'domain' => ['required', 'max:50'],
            'name'  => ['required', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules()
        ];
    }
}
