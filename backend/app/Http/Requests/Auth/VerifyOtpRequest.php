<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination' => ['required', 'string', 'max:180'],
            'code' => ['required', 'string', 'size:6'],
            'purpose' => ['required', 'string', 'in:phone_verification,login,password_reset'],
        ];
    }
}
