<?php

namespace App\Http\Requests\Admin\TorrentProvider;

use Illuminate\Foundation\Http\FormRequest;

class TorrentProviderStoreRequest extends FormRequest
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
            'url' => ['required', 'string', 'active_url'],
            'name' => ['required', 'string', 'unique:torrent_providers,name'],
        ];
    }
}
