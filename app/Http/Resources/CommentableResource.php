<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Commentable $commentable */
        $commentable = $this->resource;

        $isHearted = (bool) $commentable->is_hearted; // @phpstan-ignore-line

        return [
            'id' => $commentable->id,
            'is_hearted' => $isHearted,
            'comment' => $commentable->comment,
            'hearts_count' => $commentable->hearts_count,
            'user' => UserResource::make($this->whenLoaded('user')),
            'replies' => CommentableResource::collection($this->whenLoaded('children')),
        ];
    }
}
