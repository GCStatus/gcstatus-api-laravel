<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\MediaType $mediaType */
        $mediaType = $this->resource;

        return [
            'id' => $mediaType->id,
            'name' => $mediaType->name,
            'created_at' => $mediaType->created_at,
            'updated_at' => $mediaType->updated_at,
        ];
    }
}
