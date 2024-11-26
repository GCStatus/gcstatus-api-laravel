<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class SocialUpdateRequest extends FormRequest
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
            'share' => ['required', 'boolean'],
            'phone' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string', 'active_url'],
            'facebook' => ['nullable', 'string', 'active_url'],
            'github' => ['nullable', 'string', 'active_url'],
            'youtube' => ['nullable', 'string', 'active_url'],
            'twitch' => ['nullable', 'string', 'active_url'],
            'twitter' => ['nullable', 'string', 'active_url'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('share')) {
            $this->merge([
                'share' => filter_var($this->share, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
