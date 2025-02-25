<?php

namespace App\Http\Requests\Admin\Game;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GameUpdateRequest extends FormRequest
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
            'legal' => ['sometimes', 'string'],
            'great_release' => ['nullable', 'boolean'],
            'about' => ['nullable', 'string', 'min:25'],
            'cover' => ['nullable', 'string', 'active_url'],
            'release_date' => ['nullable', 'string', 'date'],
            'description' => ['nullable', 'string', 'min:25'],
            'website' => ['nullable', 'string', 'active_url'],
            'age' => ['nullable', 'numeric', 'gte:0', 'lte:18'],
            'title' => ['nullable', 'string', Rule::unique('games', 'title')->ignore($this->game)],
            'short_description' => ['nullable', 'string', 'min:15'],
            'condition' => ['nullable', 'string', 'in:hot,sale,popular,common,unreleased'],
            'crack' => ['nullable', 'array'],
            'crack.status' => ['nullable', 'string', 'in:cracked,uncracked,cracked-oneday'],
            'crack.cracked_at' => ['nullable', 'string', 'date', 'before_or_equal:today'],
            'crack.cracker_id' => ['nullable', 'numeric', 'exists:crackers,id'],
            'crack.protection_id' => ['nullable', 'numeric', 'exists:protections,id'],
            'support' => ['nullable', 'array'],
            'support.contact' => ['nullable', 'string'],
            'support.url' => ['nullable', 'string', 'active_url'],
            'support.email' => ['nullable', 'string', 'email:dns,rfc'],
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

        if ($this->has('great_release')) {
            $this->merge([
                'great_release' => filter_var($this->great_release, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
