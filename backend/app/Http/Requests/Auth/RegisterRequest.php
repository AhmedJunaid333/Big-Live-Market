<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'phone_e164' => ['nullable', 'string', 'regex:/^\+[1-9]\d{1,14}$/', 'unique:users,phone_e164'],
            'locale' => ['nullable', 'string', 'in:en,ur'],
            'preferred_currency' => ['nullable', 'string', 'size:3'],
            'role' => ['nullable', 'string', 'in:buyer,seller'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
