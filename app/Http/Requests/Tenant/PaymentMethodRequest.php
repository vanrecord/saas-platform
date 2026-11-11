<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMethodId = $this->route('payment_method')?->id
            ?? $this->route('paymentMethod')?->id
            ?? $this->input('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods', 'name')->ignore($paymentMethodId),
            ],
            'print' => ['nullable', 'boolean'],
            'is_default_payment' => ['nullable', 'boolean'],
            'is_disable' => ['nullable', 'boolean'],
            'is_sponsor' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'qr_code' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            'remove_qr_code' => ['nullable', 'boolean'],
            'provider' => ['nullable', 'string', Rule::in(['', 'ABA'])],
            'is_activate' => ['nullable', 'boolean'],
            'currency' => ['nullable', 'string', 'max:10'],
            'datas' => ['nullable', 'array'],
            'datas.merchant_id' => ['nullable', 'string', 'max:255'],
            'datas.api_key' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter Payment Method name',
            'name.unique' => 'This Payment Method name is already taken.',
            'qr_code.image' => 'The QR Code must be a valid image file (jpeg, png, jpg, webp, svg).',
            'qr_code.max' => 'The QR Code image must not exceed 4MB.',
            'provider.in' => 'Selected Bank Provider is invalid. Supported: ABA.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isActivate = filter_var($this->input('is_activate'), FILTER_VALIDATE_BOOLEAN);
            if ($isActivate) {
                $provider = trim((string)$this->input('provider'));
                if (empty($provider)) {
                    $validator->errors()->add('provider', 'Please configure the Bank Provider before enabling Online Payment');
                    $validator->errors()->add('is_activate', 'Please configure the Bank Provider before enabling Online Payment');
                    return;
                }

                if ($provider === 'ABA') {
                    $paymentMethod = $this->route('payment_method') ?? $this->route('paymentMethod');
                    $existingDatas = [];
                    if ($paymentMethod) {
                        $existingDatas = is_array($paymentMethod->datas)
                            ? $paymentMethod->datas
                            : (json_decode($paymentMethod->datas ?? '', true) ?: []);
                    }

                    $merchantId = trim((string)$this->input('datas.merchant_id'));
                    if (empty($merchantId) && empty($existingDatas['merchant_id'])) {
                        $validator->errors()->add('datas.merchant_id', 'Merchant ID is required when enabling ABA Online Payment.');
                    }

                    $apiKey = trim((string)$this->input('datas.api_key'));
                    $hasExistingKey = !empty($existingDatas['api_key']);
                    if (empty($apiKey) && !$hasExistingKey) {
                        $validator->errors()->add('datas.api_key', 'API Key is required when enabling ABA Online Payment.');
                    }
                }
            }
        });
    }
}
