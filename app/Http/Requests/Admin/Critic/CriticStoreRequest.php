<?php

namespace App\Http\Requests\Admin\Critic;

use Illuminate\Foundation\Http\FormRequest;

class CriticStoreRequest extends FormRequest
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
            'acting' => ['required', 'boolean'],
            'url' => ['required', 'string', 'active_url'],
            'logo' => ['required', 'string', 'active_url'],
            'name' => ['required', 'string', 'unique:critics,name'],
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
