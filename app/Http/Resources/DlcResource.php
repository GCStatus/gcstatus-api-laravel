<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DlcResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Dlc $dlc */
        $dlc = $this->resource;

        return [
            'id' => $dlc->id,
            'slug' => $dlc->slug,
            'free' => $dlc->free,
            'title' => $dlc->title,
            'cover' => $dlc->cover,
            'legal' => $dlc->legal,
            'about' => $dlc->about,
            'description' => $dlc->description,
            'release_date' => $dlc->release_date,
            'short_description' => $dlc->short_description,
            'game' => GameResource::make($this->whenLoaded('game')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'platforms' => PlatformResource::collection($this->whenLoaded('platforms')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'galleries' => GalleriableResource::collection($this->whenLoaded('galleries')),
            'stores' => StoreableResource::collection($this->whenLoaded('stores')),
            'developers' => DeveloperResource::collection($this->whenLoaded('developers')),
            'publishers' => PublisherResource::collection($this->whenLoaded('publishers')),
        ];
    }
}
