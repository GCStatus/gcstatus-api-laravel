<?php

namespace App\Http\Resources;

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
        ];
    }
}
