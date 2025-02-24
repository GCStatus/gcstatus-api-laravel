<?php

namespace App\Http\Requests\Admin\Dlc;

use Illuminate\Foundation\Http\FormRequest;

class DlcStoreRequest extends FormRequest
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
            'about' => ['required', 'string', 'min:25'],
            'cover' => ['required', 'string', 'active_url'],
            'release_date' => ['required', 'string', 'date'],
            'description' => ['nullable', 'string', 'min:25'],
            'title' => ['required', 'string', 'unique:dlcs,title'],
            'short_description' => ['required', 'string', 'min:15'],
            'game_id' => ['required', 'numeric', 'exists:games,id'],
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
