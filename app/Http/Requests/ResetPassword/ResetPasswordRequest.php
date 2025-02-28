<?php

namespace App\Http\Requests\ResetPassword;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'exists:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised(),
            ],
        ];
    }

    /**
     * The validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.exists' => 'We could not find any user with the given email. Please, double check it and try again!',
        ];
    }
}
