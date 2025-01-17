<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CriticableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Criticable $criticable */
        $criticable = $this->resource;

        return [
            'id' => $criticable->id,
            'url' => $criticable->url,
            'rate' => $criticable->rate,
            'posted_at' => $criticable->posted_at,
            'critic' => CriticResource::make($this->whenLoaded('critic')),
        ];
    }
}
