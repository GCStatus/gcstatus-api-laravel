<?php

namespace App\Http\Requests\Admin\Developer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DeveloperUpdateRequest extends FormRequest
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
            'acting' => ['nullable', 'boolean'],
            'name' => ['required', 'string', Rule::unique('developers', 'name')->ignore($this->developer)],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('acting')) {
            $this->merge([
                'acting' => filter_var($this->acting, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
