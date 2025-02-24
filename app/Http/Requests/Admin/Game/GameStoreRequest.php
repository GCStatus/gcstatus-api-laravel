<?php

namespace App\Http\Requests\Admin\Game;

use Illuminate\Foundation\Http\FormRequest;

class GameStoreRequest extends FormRequest
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
            'free' => ['required', 'boolean'],
            'legal' => ['sometimes', 'string'],
            'great_release' => ['required', 'boolean'],
            'about' => ['required', 'string', 'min:25'],
            'cover' => ['required', 'string', 'active_url'],
            'release_date' => ['required', 'string', 'date'],
            'description' => ['nullable', 'string', 'min:25'],
            'website' => ['nullable', 'string', 'active_url'],
            'age' => ['required', 'numeric', 'gte:0', 'lte:18'],
            'title' => ['required', 'string', 'unique:games,title'],
            'short_description' => ['required', 'string', 'min:15'],
            'condition' => ['required', 'string', 'in:hot,sale,popular,common,unreleased'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('free')) {
            $this->merge([
                'free' => filter_var($this->free, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }

        if ($this->has('great_release')) {
            $this->merge([
                'great_release' => filter_var($this->great_release, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
