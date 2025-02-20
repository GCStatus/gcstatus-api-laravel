<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
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
            'url' => ['nullable', 'string', 'active_url'],
            'logo' => ['nullable', 'string', 'active_url'],
            'name' => ['nullable', 'string', Rule::unique('stores', 'name')->ignore($this->store)],
        ];
    }
}
