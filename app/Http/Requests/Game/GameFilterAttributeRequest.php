<?php

namespace App\Http\Requests\Game;

use Illuminate\Foundation\Http\FormRequest;

class GameFilterAttributeRequest extends FormRequest
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
            'value' => ['required', 'string'],
            'attribute' => ['required', 'string', 'in:tags,genres,cracks,crackers,platforms,categories,publishers,developers,protections'],
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
            'attribute.in' => 'This attribute is not accepted!',
        ];
    }
}
