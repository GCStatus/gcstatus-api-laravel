<?php

namespace App\Http\Requests\Admin\Steam;

use Illuminate\Foundation\Http\FormRequest;

class SteamAppStoreRequest extends FormRequest
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
            'app_id' => ['required', 'numeric', 'unique:storeables,store_item_id'],
        ];
    }

    /**
     * The response messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'app_id.unique' => 'The given app id is already stored on database.'
        ];
    }
}
