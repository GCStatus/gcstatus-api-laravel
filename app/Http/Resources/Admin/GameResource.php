<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Game $game */
        $game = $this->resource;

        return [
            'id' => $game->id,
            'age' => $game->age,
            'slug' => $game->slug,
            'free' => $game->free,
            'title' => $game->title,
            'cover' => $game->cover,
            'about' => $game->about,
            'legal' => $game->legal,
            'website' => $game->website,
            'views_count' => $game->views,
            'condition' => $game->condition,
            'description' => $game->description,
            'release_date' => $game->release_date,
            'great_release' => $game->great_release,
            'short_description' => $game->short_description,
            'hearts_count' => $game->hearts_count,
            'comments_count' => $game->comments_count,
            'created_at' => $game->created_at,
            'updated_at' => $game->updated_at,
            'crack' => CrackResource::make($this->whenLoaded('crack')),
            'support' => GameSupportResource::make($this->whenLoaded('support')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'platforms' => PlatformResource::collection($this->whenLoaded('platforms')),
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'galleries' => GalleriableResource::collection($this->whenLoaded('galleries')),
            'languages' => LanguageableResource::collection($this->whenLoaded('languages')),
            'requirements' => RequirementableResource::collection($this->whenLoaded('requirements')),
            'stores' => StoreableResource::collection($this->whenLoaded('stores')),
            'comments' => CommentableResource::collection($this->whenLoaded('comments')),
            'developers' => DeveloperResource::collection($this->whenLoaded('developers')),
            'publishers' => PublisherResource::collection($this->whenLoaded('publishers')),
            'torrents' => TorrentResource::collection($this->whenLoaded('torrents')),
            'reviews' => ReviewableResource::collection($this->whenLoaded('reviews')),
            'critics' => CriticableResource::collection($this->whenLoaded('critics')),
            'dlcs' => DlcResource::collection($this->whenLoaded('dlcs')),
        ];
    }
}
