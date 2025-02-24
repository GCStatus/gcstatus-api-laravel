<?php

namespace App\Http\Resources\Admin;

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

        return [
            'id' => $commentable->id,
            'comment' => $commentable->comment,
            'hearts_count' => $commentable->hearts_count,
            'created_at' => $commentable->created_at,
            'updated_at' => $commentable->updated_at,
            'user' => SocialUserResource::make($this->whenLoaded('user')),
            'replies' => CommentableResource::collection($this->whenLoaded('children')),
        ];
    }
}
