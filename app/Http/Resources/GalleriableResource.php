<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleriableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Galleriable $galleriable */
        $galleriable = $this->resource;

        return [
            'id' => $galleriable->id,
            'path' => $galleriable->s3 ? storage()->getPath($galleriable->path) : $galleriable->path,
            'type' => MediaTypeResource::make($this->whenLoaded('mediaType')),
        ];
    }
}
