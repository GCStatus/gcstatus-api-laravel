<?php

namespace App\Http\Requests\Admin\Dlc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DlcUpdateRequest extends FormRequest
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
            'free' => ['nullable', 'boolean'],
            'legal' => ['nullable', 'string'],
            'about' => ['nullable', 'string', 'min:25'],
            'cover' => ['nullable', 'string', 'active_url'],
            'release_date' => ['nullable', 'string', 'date'],
            'description' => ['nullable', 'string', 'min:25'],
            'title' => ['nullable', 'string', Rule::unique('dlcs', 'title')->ignore($this->dlc)],
            'short_description' => ['nullable', 'string', 'min:15'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['nullable', 'numeric', 'exists:tags,id'],
            'genres' => ['sometimes', 'array'],
            'genres.*' => ['nullable', 'numeric', 'exists:genres,id'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['nullable', 'numeric', 'exists:categories,id'],
            'platforms' => ['sometimes', 'array'],
            'platforms.*' => ['nullable', 'numeric', 'exists:platforms,id'],
            'publishers' => ['sometimes', 'array'],
            'publishers.*' => ['nullable', 'numeric', 'exists:publishers,id'],
            'developers' => ['sometimes', 'array'],
            'developers.*' => ['nullable', 'numeric', 'exists:developers,id'],
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
    }
}
