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

        # TODO: Implement the path when s3 is configured
        return [
            'id' => $galleriable->id,
            'path' => $galleriable->path,
            'type' => MediaTypeResource::make($this->whenLoaded('mediaType')),
        ];
    }
}
