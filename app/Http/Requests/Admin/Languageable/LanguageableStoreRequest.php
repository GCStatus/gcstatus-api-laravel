<?php

namespace App\Http\Requests\Admin\Languageable;

use App\Models\Languageable;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class LanguageableStoreRequest extends FormRequest
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
        $allowedLanguageableTypes = Languageable::ALLOWED_LANGUAGEABLE_TYPES;

        return [
            'menu' => ['required', 'boolean'],
            'dubs' => ['required', 'boolean'],
            'subtitles' => ['required', 'boolean'],
            'languageable_id' => ['required', 'numeric'],
            'language_id' => ['required', 'numeric', 'exists:languages,id'],
            'languageable_type' => ['required', 'string', Rule::in($allowedLanguageableTypes)],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('menu')) {
            $this->merge([
                'menu' => filter_var($this->menu, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
        if ($this->has('dubs')) {
            $this->merge([
                'dubs' => filter_var($this->dubs, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
        if ($this->has('subtitles')) {
            $this->merge([
                'subtitles' => filter_var($this->subtitles, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
