<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class BasicUpdateRequest extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'birthdate' => ['sometimes', 'date', 'before_or_equal:-14 years'],
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
            'birthdate.before_or_equal' => 'You should have at least 14 years old to proceed.',
        ];
    }
}
