<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Tag $tag */
        $tag = $this->resource;

        return [
            'id' => $tag->id,
            'slug' => $tag->slug,
            'name' => $tag->name,
        ];
    }
}
