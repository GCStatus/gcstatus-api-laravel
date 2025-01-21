<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Reviewable $reviewable */
        $reviewable = $this->resource;

        return [
            'id' => $reviewable->id,
            'rate' => $reviewable->rate,
            'review' => $reviewable->review,
            'consumed' => $reviewable->consumed,
            'created_at' => $reviewable->created_at,
            'updated_at' => $reviewable->updated_at,
            'by' => SocialUserResource::make($this->whenLoaded('user')),
        ];
    }
}
