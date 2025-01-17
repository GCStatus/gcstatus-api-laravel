<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Level $level */
        $level = $this->resource;

        return [
            'id' => $level->id,
            'level' => $level->level,
            'coins' => $level->coins,
            'experience' => $level->experience,
            'users' => UserResource::collection($this->whenLoaded('users')),
            'rewards' => RewardableResource::collection($this->whenLoaded('rewards')),
        ];
    }
}
