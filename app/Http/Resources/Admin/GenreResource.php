<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Genre $genre */
        $genre = $this->resource;

        return [
            'id' => $genre->id,
            'name' => $genre->name,
            'slug' => $genre->slug,
            'created_at' => $genre->created_at,
            'updated_at' => $genre->updated_at,
        ];
    }
}
