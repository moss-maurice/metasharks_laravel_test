<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password.string'),
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('attributes.email'),
            'password' => __('attributes.password'),
        ];
    }
}