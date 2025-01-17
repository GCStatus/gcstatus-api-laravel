<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Language $language */
        $language = $this->resource;

        return [
            'id' => $language->id,
            'name' => $language->name,
            'slug' => $language->slug,
        ];
    }
}
