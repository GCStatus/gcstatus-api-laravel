<?php

namespace App\Http\Requests\Admin\Galleriable;

use App\Models\Galleriable;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class GalleriableStoreRequest extends FormRequest
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
        $allowedGalleriableTypes = Galleriable::ALLOWED_GALLERIABLES_TYPE;

        return [
            's3' => ['required', 'boolean'],
            'galleriable_id' => ['required', 'numeric'],
            'url' => ['required_if:s3,false', 'string', 'active_url'],
            'media_type_id' => ['required', 'numeric', 'exists:media_types,id'],
            'galleriable_type' => ['required', Rule::in($allowedGalleriableTypes)],
            'file' => ['required_if:s3,true', 'file', 'mimes:png,jpg,jpeg,gif,bmp,webp,mp4,mov', 'max:2048'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        if ($this->has('s3')) {
            $this->merge([
                's3' => filter_var($this->s3, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
