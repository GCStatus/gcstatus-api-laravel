<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'unique:users,nickname', 'max:255'],
            'birthdate' => ['required', 'date', 'before_or_equal:-14 years'],
            'email' => ['required', 'string', 'unique:users,email', 'email:rfc,dns'],
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
            'email.unique' => 'The providen email is already in use.',
            'nickname.unique' => 'The providen nickname is already in use.',
            'name.max' => 'Your name must not have more than 255 characters.',
            'nickname.max' => 'Your nickname must not have more than 255 characters.',
            'birthdate.before_or_equal' => 'You should have at least 14 years old to proceed.',
        ];
    }
}
