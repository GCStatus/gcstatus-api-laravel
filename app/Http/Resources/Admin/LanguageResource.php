<?php

namespace App\Http\Resources\Admin;

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
            'created_at' => $language->created_at,
            'updated_at' => $language->updated_at,
        ];
    }
}
